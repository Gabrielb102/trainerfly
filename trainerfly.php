<?php
/*
Plugin Name: TrainerFly - a HivePress Extension
Description: Add the Trainerfly map UI to the MeetingHive theme for HivePress.
Version: 0.5.0
Author: Fresco Software
License: Proprietary
Text Domain: trainerfly
Domain Path: /languages
Required Plugins: hivepress
*/

namespace TrainerFly;

defined( 'ABSPATH' ) || exit;

// Autoload classes
require_once __DIR__ . '/vendor/autoload.php';

// Register HivePress extension directory
add_filter(
    'hivepress/v1/extensions',
    function( $extensions ) {
        $extensions[] = __DIR__;

        return $extensions;
    }
);

// Necessary Imports
use Fresco\Trainerfly\Frontend\PublicFrontend;
use Fresco\Trainerfly\Frontend\ScriptLoader;
use Fresco\Trainerfly\API\Router;

// Initialize the plugin

add_action('plugins_loaded', function () {

	// Load the front end
    PublicFrontend::registerShortcode();
    ScriptLoader::registerHooks(__DIR__);

    // Initialize the API Router
    Router::init();
});
