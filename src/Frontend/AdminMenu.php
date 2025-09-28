<?php

namespace Fresco\Trainerfly\Frontend;

class AdminMenu
{
    public static function init()
    {
        add_action('admin_menu', [self::class, 'addMenuPage']);
        add_action('admin_post_save_trainerfly_options', [self::class, 'handleSave']);
    }

    public static function addMenuPage()
    {
        add_menu_page(
            'Trainerfly Settings', // Page title
            'Trainerfly Settings', // Menu title
            'manage_options', // Capability required
            'trainerfly-settings', // Menu slug
            [self::class, 'renderPage'], // Callback function
            'dashicons-admin-generic',
            80.0016
        );
    }

    public static function renderPage()
    {
?>
        <div class="wrap">
            <h1>Trainerfly Settings</h1>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('trainerfly_settings'); ?>
                <input type="hidden" name="action" value="save_trainerfly_options" />

                <div class="card">
                    <h2>MapBox API Key</h2>
                    <p>
                        This is the API key for the MapBox API. This is required to setup the search suggestions on the map.
                    </p>
                    <p>
                        <strong>Note:</strong> This is a required field. If you don't have an API key, you can get one from MapBox.
                        <a href="https://www.mapbox.com/pricing/" target="_blank">MapBox Pricing</a>
                    </p>
                    <input type="text" name="mapbox_api_key" value="<?php echo esc_attr(get_option('mapbox_api_key')); ?>" />
                    <p>
                        <strong>Mobile Home URL Suffix:</strong> This is the URL suffix for the mobile home page. For example, if your mobile home page is at <code>https://example.com/?page_id=9#/</code>, you would enter <code>?page_id=9#/</code> here.
                    </p>
                    <input type="text" name="mobile_home_url_suffix" value="<?php echo esc_attr(get_option('mobile_home_url_suffix')); ?>" />
                    <p>
                        <button type="submit" class="button button-primary">Save Settings</button>
                    </p>
                </div>
            </form>

            <?php
            // Display any admin notices - reading from the URL parameters
            if (isset($_GET['trainerfly_options_saved']) && $_GET['trainerfly_options_saved'] === 'true') {
                echo '<div class="notice notice-success is-dismissible"><p>MapBox API Key saved successfully!</p></div>';
            } elseif (isset($_GET['trainerfly_options_saved']) && $_GET['trainerfly_options_saved'] === 'false') {
                echo '<div class="notice notice-error is-dismissible"><p>Error occurred while saving MapBox API Key. Check the error logs for details.</p></div>';
            }
            ?>
        </div>
<?php
    }

    public static function handleSave()
    {
        if (! current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('trainerfly_settings');

        error_log('Handling save_trainerfly_options action');

        $mapbox_api_key = isset($_POST['mapbox_api_key']) ? sanitize_text_field(wp_unslash($_POST['mapbox_api_key'])) : '';
        $mobile_home_url_suffix = isset($_POST['mobile_home_url_suffix']) ? sanitize_text_field(wp_unslash($_POST['mobile_home_url_suffix'])) : '';

        $key_saved = update_option('mapbox_api_key', $mapbox_api_key);
        $url_saved = update_option('mobile_home_url_suffix', $mobile_home_url_suffix);

        $key_ok = $key_saved || (get_option('mapbox_api_key') === $mapbox_api_key);
        $url_ok = $url_saved || (get_option('mobile_home_url_suffix') === $mobile_home_url_suffix);
        $result = $key_ok && $url_ok;

        error_log('Handling save_trainerfly_options result: ' . json_encode([
            'key_ok' => $key_ok,
            'url_ok' => $url_ok,
            'result' => $result,
        ]));

        wp_safe_redirect(admin_url('admin.php?page=trainerfly-settings&trainerfly_options_saved=' . ($result ? 'true' : 'false')));
        exit;
    }
}
