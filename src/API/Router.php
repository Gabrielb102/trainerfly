<?php

namespace Fresco\Trainerfly\API;

use HivePress\Controllers\Listing;
use Fresco\Trainerfly\API\Listings\Controllers\ListingController;

class Router
{

	public static string $prefix = 'trainerfly/v1';

    public static function init(): void
    {
        add_action('rest_api_init', function () {

            // unusued at the moment
            register_rest_route(self::$prefix, '/entries', [
                'methods' => 'GET',
                'callback' => [Listing::class, 'getAll'],
            ]);

            // Register a new endpoint for geolocation search - optional to include category
            register_rest_route(self::$prefix, '/listings/geo', [
                'methods' => 'GET',
                'callback' => [ListingController::class, 'getListings'],
                'permission_callback' => '__return_true',
            ]);

            // Endpoint for categories available in location
            register_rest_route(self::$prefix, '/categories', [
                'methods' => 'GET',
                'callback' => [ListingController::class, 'getCategories'],
                'permission_callback' => '__return_true',
            ]);
        });
    }
}
