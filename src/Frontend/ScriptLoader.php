<?php

namespace Fresco\Trainerfly\Frontend;

class ScriptLoader
{
    public static function loadFrontendScripts($main_plugin_dir): void
    {
        $bundle_output_dir = $main_plugin_dir . '/frontend/dist/assets';

        // Enqueue all .js files
        foreach (glob($bundle_output_dir . '/*.js') as $js_file) {

            // Get the relative path and URL
            $relative_path = str_replace($main_plugin_dir, 'trainerfly/', $js_file);
            $file_url = plugins_url($relative_path, $main_plugin_dir);
            $handle = basename($js_file, '.js');

            // Enqueue the script
            wp_enqueue_script($handle, $file_url, array(), null, true);
            error_log("Enqueued script: $handle with URL: $file_url");
            wp_localize_script($handle, 'localized', array(
                'baseURL' => rest_url('trainerfly/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
                'mapboxAPIKey' => 'pk.eyJ1Ijoid2F4ZWQtbGVvcGFyZHMiLCJhIjoiY205eG9hZGJ6MHMyYjJqcHBsYmtlcjZhNSJ9.38V_X1HWjTriDm7CJC2yOA'
            ));
            
            // Force type="module" attribute
            add_filter('script_loader_tag', function ($tag, $handle_in) use ($handle) {
                if ($handle_in === $handle) {
                    return str_replace('<script ', '<script type="module" ', $tag);
                }
                return $tag;
            }, 10, 2);
        }

        // Enqueue all .css files
        foreach (glob($bundle_output_dir . '/*.css') as $css_file) {
            $relative_path = str_replace($main_plugin_dir, 'trainerfly/', $css_file);
            $file_url = plugins_url($relative_path, $main_plugin_dir);
            $version = filemtime($css_file); // Use file modification time as version
            wp_enqueue_style(basename($css_file, '.css'), $file_url, array(), $version);
        }
    }

    public static function registerHooks($main_plugin_dir): void
    {
        add_action('wp_enqueue_scripts', function () use ($main_plugin_dir) {
            self::loadFrontendScripts($main_plugin_dir);
        });
    }
}
