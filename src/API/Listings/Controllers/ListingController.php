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
     * Get listings by geographic location
     * 
     * Retrieves published listings within a specified radius of given coordinates.
     * Uses geolocation meta fields to filter listings by latitude and longitude.
     * 
     * @param WP_REST_Request $request The request object containing:
     *   - latitude (float, required): The latitude coordinate
     *   - longitude (float, required): The longitude coordinate
     *   - radius (int, optional): Search radius in km (default: 15)
     * 
     * @return WP_REST_Response Returns a REST response with:
     *   - status: 200 on success
     *   - data: Array of transformed listing objects with vendor and review information
     *   - status: 400 if coordinates are missing or invalid
     * 
     * @example
     * GET /wp-json/trainerfly/v1/listings/geo?latitude=40.7128&longitude=-74.0060&radius=15
     */
    public static function getListings(WP_REST_Request $request)
    {
        // Get location parameters
        $latitude = floatval($request->get_param('latitude'));
        $longitude = floatval($request->get_param('longitude'));
        $radius = absint($request->get_param('radius')) ?: absint(get_option('hp_geolocation_radius', 15));

        // Check if we have valid coordinates
        if (empty($latitude) || empty($longitude)) {

            // If only the location name is provided, we could implement geocoding here
            // but for now we'll require coordinates
            return hp\rest_error(400, ['message' => 'Latitude and longitude are required']);
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

        // Add meta query for latitude and longitude
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

        // Get listings
        $listings = $query->limit(50)->get();

        // Transform the results using ListingTransformer
        $transformer = new ListingTransformer();
        $results = $transformer->transformCollection($listings);
        
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
    public static function getCategories(WP_REST_Request $request) {
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
                t.description,
                t.parent,
                COUNT(DISTINCT p.ID) as listing_count
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
        ";

        $params = [];

        // Check if we have valid coordinates
        if (empty($latitude) || empty($longitude)) {
            // Query for categories with listings that have no assigned longitude or latitude (remote)
            $sql .= "
                LEFT JOIN {$wpdb->postmeta} lat_meta ON p.ID = lat_meta.post_id AND lat_meta.meta_key = 'hp_latitude'
                LEFT JOIN {$wpdb->postmeta} lng_meta ON p.ID = lng_meta.post_id AND lng_meta.meta_key = 'hp_longitude'
                WHERE tt.taxonomy = 'hp_listing_category'
                AND p.post_type = 'hp_listing'
                AND p.post_status = 'publish'
                AND (lat_meta.meta_value IS NULL OR lng_meta.meta_value IS NULL)
            ";
        } else {
            // Query for categories with listings in the specified geographic area
            $sql .= "
                INNER JOIN {$wpdb->postmeta} lat_meta ON p.ID = lat_meta.post_id
                INNER JOIN {$wpdb->postmeta} lng_meta ON p.ID = lng_meta.post_id
                WHERE tt.taxonomy = 'hp_listing_category'
                AND p.post_type = 'hp_listing'
                AND p.post_status = 'publish'
                AND lat_meta.meta_key = 'hp_latitude'
                AND lng_meta.meta_key = 'hp_longitude'
                AND CAST(lat_meta.meta_value AS DECIMAL(10,6)) BETWEEN %f AND %f
                AND CAST(lng_meta.meta_value AS DECIMAL(10,6)) BETWEEN %f AND %f
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

        $sql .= " GROUP BY t.term_id ORDER BY listing_count DESC, t.name ASC";

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
