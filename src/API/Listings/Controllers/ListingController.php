<?php

namespace Fresco\Trainerfly\API\Listings\Controllers;

// Mostly taken from the HivePress plugin, with some modifications for the geolocation search.

use HivePress\Helpers as hp;
use HivePress\Models as Models;
use WP_REST_Response;
use WP_REST_Request;

class ListingController
{

    public function getListingsByLocation(WP_REST_Request $request)
    {
        // Get location parameters
        $location = sanitize_text_field($request->get_param('location'));
        $latitude = floatval($request->get_param('latitude'));
        $longitude = floatval($request->get_param('longitude'));
        $radius = absint($request->get_param('radius')) ?: absint(get_option('hp_geolocation_radius', 15));

        // Check if we have valid coordinates
        if (empty($latitude) || empty($longitude)) {
            if (empty($location)) {
                return hp\rest_error(400, ['message' => 'Location or coordinates are required']);
            }

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

        // Format results
        $results = [];

        foreach ($listings as $listing) {
            $vendor = $listing->get_vendor();

            $results[] = [
                'id' => $listing->get_id(),
                'title' => $listing->get_title(),
                'url' => get_permalink($listing->get_id()),
                'latitude' => $listing->get_latitude(),
                'longitude' => $listing->get_longitude(),
                'location' => $listing->get_location(),
                'image' => $listing->get_image() ? $listing->get_image()->get_url('thumbnail') : null,
                'price' => $listing->get_price() ? $listing->get_price() : null,
                'category' => $listing->get_categories() ? $listing->get_categories()[0]->get_name() : null,
                'description' => $listing->get_description(),
                'reviews' => $this->getListingReviews($listing),
                'vendor' => [
                    'id' => $vendor->get_id(),
                    'name' => $vendor->get_name(),
                    'image' => $vendor->get_image() ? $vendor->get_image()->get_url('thumbnail') : null,
                    'rating' => $vendor->get_rating(),
                    'rating_count' => $vendor->get_rating_count(),
                    'descriptors' => $this->getVendorDescriptors($vendor),
                ],
            ];
        }
        return hp\rest_response(200, $results);
    }

    private function getVendorDescriptors($vendor): array
    {
        $vendor_descriptors = [];

        if ($vendor) {
            // Get ALL vendor fields, not just those configured for secondary display
            $vendor_fields = $vendor->_get_fields();

            $vendor_descriptors = $vendor_fields['descriptors']->get_value();

            if (! empty($vendor_descriptors) && is_string($vendor_descriptors)) {
                // Split the comma-separated string into an array
                $vendor_descriptors = array_map('trim', explode(',', $vendor_descriptors));

                // Remove any empty values
                $vendor_descriptors = array_filter($vendor_descriptors);
            } else {
                // Ensure it's an array even if empty or already an array
                $vendor_descriptors = is_array($vendor_descriptors) ? $vendor_descriptors : [];
            }
        }

        return $vendor_descriptors;
    }

    private function getListingReviews($listing): array
    {
        $listing_reviews = [];
        if ($listing && hivepress()->get_version('reviews')) {
            // Get reviews for this listing only
            $reviews = Models\Review::query()->filter(
                [
                    'approved' => true,
                    'parent'   => null,
                    'listing'  => $listing->get_id(),
                ]
            )->order(['created_date' => 'desc'])
                ->limit(5) // Limit to 5 most recent reviews
                ->get();

            foreach ($reviews as $review) {
                $listing_reviews[] = [
                    'id' => $review->get_id(),
                    'text' => $review->get_text(),
                    'rating' => $review->get_rating(),
                    'created_date' => $review->get_created_date(),
                    'author_name' => $review->get_author__display_name(),
                ];
            }
        }
        return $listing_reviews;
    }
}
