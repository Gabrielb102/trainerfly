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
            register_rest_route(self::$prefix, '/entries', [
                'methods' => 'GET',
                'callback' => [Listing::class, 'getAll'],
            ]);

            // Register a new endpoint for geolocation search
            register_rest_route(self::$prefix, '/listings/geo', [
                'methods' => 'GET',
                'callback' => [new ListingController(), 'getListingsByLocation'],
                'permission_callback' => '__return_true',
            ]);
        });
    }
}
