<?php

namespace Fresco\Trainerfly\API\Listings\Controllers;

// Mostly taken from the HivePress plugin, with some modifications for the geolocation search.

use HivePress\Helpers as hp;
use HivePress\Models as Models;
use WP_REST_Response;
use WP_REST_Request;
use Fresco\Trainerfly\API\Listings\Transformers\CategoryTransformer;
use Fresco\Trainerfly\API\Listings\Transformers\ListingTransformer;

class ListingController
{

    /**
     * Get listings by geographic location or remote listings
     * 
     * Retrieves published listings within a specified radius of given coordinates,
     * or retrieves remote listings (listings without location data) when remote=true.
     * Uses geolocation meta fields to filter listings by latitude and longitude.
     * 
     * @param WP_REST_Request $request The request object containing:
     *   - latitude (float, optional): The latitude coordinate
     *   - longitude (float, optional): The longitude coordinate
     *   - radius (int, optional): Search radius in km (default: 15) - only used for local listings
     *   - categoryId (int, optional): Category ID to filter listings by specific category
     *   - remote (string, optional): Set to 'true' to retrieve only remote listings (listings without location data)
     * 
     * @return WP_REST_Response Returns a REST response with:
     *   - status: 200 on success
     *   - data: Array of transformed listing objects with vendor and review information
     *   - status: 400 if coordinates are missing or invalid
     * 
     * @example
     * GET /wp-json/trainerfly/v1/listings/geo?latitude=40.7128&longitude=-74.0060&radius=15&categoryId=123
     * GET /wp-json/trainerfly/v1/listings/geo?remote=true&categoryId=123
     */
    public static function getListings(WP_REST_Request $request)
    {
        // Get location parameters
        $latitude = floatval($request->get_param('latitude'));
        $longitude = floatval($request->get_param('longitude'));
        $radius = absint($request->get_param('radius')) ?: absint(get_option('hp_geolocation_radius', 15));
        $categoryId = absint($request->get_param('categoryId'));
        $remoteOnly = $request->get_param('remoteOnly') === 'true'; // New parameter for remote listings

        // Error gate: If not requesting remote listings, location coordinates are required
        if (!$remoteOnly && (empty($latitude) || empty($longitude))) {
            return hp\rest_response(400, [
                'error' => 'Location coordinates are required when not requesting remote listings',
                'message' => 'Please provide both latitude and longitude parameters, or set remote=true to get remote listings only'
            ]);
        }

        // Convert radius to kilometers if using miles
        if (get_option('hp_geolocation_use_miles')) {
            $radius *= 1.60934;
        }

        // Create a query for listings
        $query = Models\Listing::query()->filter(
            [
                'status' => 'publish',
            ]
        );

        // Add category filter if provided
        if (!empty($categoryId)) {
            $query->filter(['categories__in' => [$categoryId]]);
        }

        // Handle remote listings (listings without location data)
        if ($remoteOnly) {
            $query->set_args([
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => 'hp_latitude',
                        'compare' => 'NOT EXISTS'
                    ],
                    [
                        'key' => 'hp_latitude',
                        'value' => '',
                        'compare' => '='
                    ],
                    [
                        'key' => 'hp_longitude',
                        'compare' => 'NOT EXISTS'
                    ],
                    [
                        'key' => 'hp_longitude',
                        'value' => '',
                        'compare' => '='
                    ]
                ]
            ]);
        }
        // Handle local listings (with location data)
        elseif (!empty($latitude) && !empty($longitude)) {
            $query->set_args([
                'meta_query' => [
                    'latitude' => [
                        'key' => 'hp_latitude',
                        'value' => [
                            $latitude - $radius / 111, // Approximate conversion from km to degrees
                            $latitude + $radius / 111,
                        ],
                        'type' => 'DECIMAL(10,6)',
                        'compare' => 'BETWEEN',
                    ],
                    'longitude' => [
                        'key' => 'hp_longitude',
                        'value' => [
                            $longitude - $radius / (111 * cos(deg2rad($latitude))), // Adjust for latitude
                            $longitude + $radius / (111 * cos(deg2rad($latitude))),
                        ],
                        'type' => 'DECIMAL(10,6)',
                        'compare' => 'BETWEEN',
                    ],
                ],
            ]);
        }

        // Get listings
        $listings = $query->limit(50)->get();

        // Transform the results using ListingTransformer
        $results = ListingTransformer::transformCollection($listings->serialize());

        return hp\rest_response(200, $results);
    }


    /**
     * Get categories available in a specific location, or categories with listings that have no assigned coordinates
     * 
     * @param WP_REST_Request $request The request object containing:
     *   - latitude (float, optional): The latitude coordinate. If not provided, returns categories with listings that have no coordinates
     *   - longitude (float, optional): The longitude coordinate. If not provided, returns categories with listings that have no coordinates
     *   - radius (int, optional): Search radius in km (default: 15) - only used when coordinates are provided
     *   - searchQuery (string, optional): Search term to filter categories
     *   - categoryId (int, optional): Parent category ID to get subcategories. If not provided, returns top-level categories
     */
    public static function getCategories(WP_REST_Request $request)
    {
        global $wpdb;

        // Get location parameters
        $latitude = floatval($request->get_param('latitude'));
        $longitude = floatval($request->get_param('longitude'));
        $radius = absint($request->get_param('radius')) ?: absint(get_option('hp_geolocation_radius', 15));
        $searchQuery = sanitize_text_field($request->get_param('searchQuery'));
        $categoryId = absint($request->get_param('categoryId'));

        // Convert radius to kilometers if using miles
        if (get_option('hp_geolocation_use_miles')) {
            $radius *= 1.60934;
        }

        // Calculate bounding box
        $lat_min = $latitude - $radius / 111;
        $lat_max = $latitude + $radius / 111;
        $lng_min = $longitude - $radius / (111 * cos(deg2rad($latitude)));
        $lng_max = $longitude + $radius / (111 * cos(deg2rad($latitude)));

        /** Build the SQL query with JOINs to get categories 
         * in a particular area - or remotely if no coordinates are provided
         * and their counts in one go
         **/

        $sql = "
            SELECT 
                t.term_id,
                t.name,
                t.slug,
                ANY_VALUE(tt.description) as description,
                ANY_VALUE(tt.parent) as parent,
                ANY_VALUE(icon_meta.meta_value) as icon,
                COUNT(DISTINCT p.ID) as listing_count,
                MAX(CASE WHEN child_tt.term_taxonomy_id IS NOT NULL THEN 1 ELSE 0 END) as has_children,
                COALESCE(grandparent_tt.parent, 0) as grandparent
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            LEFT JOIN {$wpdb->termmeta} icon_meta ON t.term_id = icon_meta.term_id AND icon_meta.meta_key = 'hp_icon'
            LEFT JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            LEFT JOIN {$wpdb->posts} p ON tr.object_id = p.ID AND p.post_type = 'hp_listing' AND p.post_status = 'publish'
            LEFT JOIN {$wpdb->term_taxonomy} child_tt ON child_tt.parent = t.term_id AND child_tt.taxonomy = 'hp_listing_category'
            LEFT JOIN {$wpdb->term_taxonomy} grandparent_tt ON tt.parent = grandparent_tt.term_id AND grandparent_tt.taxonomy = 'hp_listing_category'
        ";

        $params = [];

        // Check if we have valid coordinates
        if (empty($latitude) || empty($longitude)) {
            // Query for categories with listings that have no assigned longitude or latitude (remote)
            $sql .= "
                LEFT JOIN {$wpdb->postmeta} lat_meta ON p.ID = lat_meta.post_id AND lat_meta.meta_key = 'hp_latitude'
                LEFT JOIN {$wpdb->postmeta} lng_meta ON p.ID = lng_meta.post_id AND lng_meta.meta_key = 'hp_longitude'
                WHERE tt.taxonomy = 'hp_listing_category'
                AND (p.ID IS NULL OR (lat_meta.meta_value IS NULL OR lng_meta.meta_value IS NULL))
            ";
        } else {
            // Query for categories with listings in the specified geographic area
            $sql .= "
                LEFT JOIN {$wpdb->postmeta} lat_meta ON p.ID = lat_meta.post_id AND lat_meta.meta_key = 'hp_latitude'
                LEFT JOIN {$wpdb->postmeta} lng_meta ON p.ID = lng_meta.post_id AND lng_meta.meta_key = 'hp_longitude'
                WHERE tt.taxonomy = 'hp_listing_category'
                AND (p.ID IS NULL OR (
                    CAST(lat_meta.meta_value AS DECIMAL(10,6)) BETWEEN %f AND %f
                    AND CAST(lng_meta.meta_value AS DECIMAL(10,6)) BETWEEN %f AND %f
                ))
            ";

            $params = [$lat_min, $lat_max, $lng_min, $lng_max];
        }

        // Add parent category filter if provided
        if (!empty($categoryId)) {
            $sql .= " AND tt.parent = %d";
            $params[] = $categoryId;
        } else {
            // If no categoryId provided, get only top-level categories (parent = 0)
            $sql .= " AND tt.parent = 0";
        }

        // Add search filter to query if provided
        if (!empty($searchQuery)) {
            $sql .= " AND t.name LIKE %s";
            $params[] = '%' . $wpdb->esc_like($searchQuery) . '%';
        }

        $sql .= " GROUP BY t.term_id ORDER BY listing_count DESC, RAND()";

        // Execute the query
        $results = $wpdb->get_results($wpdb->prepare($sql, $params));

        if (empty($results)) {
            return hp\rest_response(200, []);
        }

        

        // Transform the results using CategoryTransformer
        $formatted_results = CategoryTransformer::transformCollection($results);

        return hp\rest_response(200, $formatted_results);
    }
}
