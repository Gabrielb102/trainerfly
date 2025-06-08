<?php

namespace Fresco\Trainerfly\API\Listings\Controllers;

// Mostly taken from the HivePress plugin, with some modifications for the geolocation search.

use HivePress\Helpers as hp;
use HivePress\Models as Models;
use WP_REST_Response;
use WP_REST_Request;

class ListingController
{

    public function getListingsByLocation( WP_REST_Request $request ): WP_REST_Response {
        // Get location parameters
        $location = sanitize_text_field( $request->get_param( 'location' ) );
        $latitude = floatval( $request->get_param( 'latitude' ) );
        $longitude = floatval( $request->get_param( 'longitude' ) );
        $radius = absint( $request->get_param( 'radius' ) ) ?: absint( get_option( 'hp_geolocation_radius', 15 ) );

        // Check if we have valid coordinates
        if ( empty( $latitude ) || empty( $longitude ) ) {
            if ( empty( $location ) ) {
                return hp\rest_error( 400, ['message' => 'Location or coordinates are required'] );
            }

            // If only the location name is provided, we could implement geocoding here
            // but for now we'll require coordinates
            return hp\rest_error( 400, ['message' => 'Latitude and longitude are required'] );
        }

        // Convert radius to kilometers if using miles
        if ( get_option( 'hp_geolocation_use_miles' ) ) {
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
        $listings = $query->limit( 50 )->get();

        // Format results
        $results = [];

	    foreach ( $listings as $listing ) {
		    $vendor = $listing->get_vendor();

		    $results[] = [
			    'id' => $listing->get_id(),
			    'title' => $listing->get_title(),
			    'url' => get_permalink( $listing->get_id() ),
			    'latitude' => $listing->get_latitude(),
			    'longitude' => $listing->get_longitude(),
			    'location' => $listing->get_location(),
			    'image' => $listing->get_image() ? $listing->get_image()->get_url( 'thumbnail' ) : null,
			    'price' => $listing->get_price() ? $listing->get_price() : null,
			    'category' => $listing->get_categories() ? $listing->get_categories()[0]->get_name() : null,
			    'vendor' => [
				    'id' => $vendor->get_id(),
				    'name' => $vendor->get_name(), // or whatever the method is called
				    'image' => $vendor->get_image() ? $vendor->get_image()->get_url('thumbnail') : null,
				    // Add other vendor fields as needed
			    ],
		    ];
	    }
        return hp\rest_response( 200, $results );
    }
}
