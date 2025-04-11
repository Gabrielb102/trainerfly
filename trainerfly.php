<?php
/*
Plugin Name: TrainerFly
Description: A plugin for managing training sessions and schedules.
Version: 0.1.0
Author: Fresco Software
License: Proprietary
Text Domain: trainerfly
Domain Path: /languages
*/

namespace TrainerFly;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Autoload classes
require_once __DIR__ . '/vendor/autoload.php';

// Necessary Imports
use Fresco\Trainerfly\Frontend\PublicFrontend;
use Fresco\Trainerfly\Frontend\ScriptLoader;

// Initialize the plugin

add_action('plugins_loaded', function () {
    PublicFrontend::registerShortcode();
    ScriptLoader::registerHooks(__DIR__);
});



