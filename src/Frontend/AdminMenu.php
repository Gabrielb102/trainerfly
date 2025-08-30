<?php

namespace Fresco\Trainerfly\Frontend;

use Fresco\Trainerfly\Data\TFBulkCategoryUpload;

class AdminMenu {
    public static function init() {
        add_action('admin_menu', [self::class, 'addMenuPage']);
        add_action('admin_post_populate_categories', [self::class, 'handlePopulateCategories']);
    }

    public static function addMenuPage() {
        add_submenu_page(
            'hp_settings',
            'Trainerfly Start Data', // Page title
            'Trainerfly Start Data', // Menu title
            'manage_options', // Capability required
            'trainerfly-categories', // Menu slug
            [self::class, 'renderPage'] // Callback function
        );
    }

    public static function renderPage() {
        ?>
        <div class="wrap">
            <h1>Trainerfly Category Population</h1>
            
            <div class="card">
                <h2>About Category Population</h2>
                <p>
                    This tool will populate your HivePress listing categories database with predefined categories 
                    for Trainerfly. The categories include various skill areas such as Professional & Career Skills, 
                    Gaming, Trades & Technical Skills, Digital & Tech Skills, Creative & Artistic Skills, Musical Skills, 
                    and many more specialized training categories.
                </p>
                <p>
                    <strong>Note:</strong> This should be a one-time operation. If categories already exist, they will not be duplicated. 
                    The system will only create new categories that don't already exist in your database.
                </p>
            </div>

            <div class="card">
                <h2>Populate Categories</h2>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('populate_categories_nonce', 'populate_categories_nonce'); ?>
                    <input type="hidden" name="action" value="populate_categories">
                    
                    <p>
                        <input type="submit" 
                               name="populate_categories" 
                               id="populate_categories" 
                               class="button button-primary button-large" 
                               value="Populate Categories Now"
                               onclick="return confirm('Are you sure you want to add the categories? This will create new listing categories in your database.');">
                    </p>
                </form>
            </div>

            <?php
            // Display any admin notices
            if (isset($_GET['populated']) && $_GET['populated'] === 'true') {
                echo '<div class="notice notice-success is-dismissible"><p>Categories populated successfully!</p></div>';
            } elseif (isset($_GET['populated']) && $_GET['populated'] === 'false') {
                echo '<div class="notice notice-error is-dismissible"><p>Error occurred while populating categories. Check the error logs for details.</p></div>';
            }
            ?>
        </div>
        <?php
    }

    public static function handlePopulateCategories() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['populate_categories_nonce'], 'populate_categories_nonce')) {
            wp_die('Security check failed');
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Attempt to populate categories
        $result = TFBulkCategoryUpload::doIt();

        // Redirect back with result
        $redirect_url = add_query_arg(
            'populated',
            $result ? 'true' : 'false',
            admin_url('edit.php?post_type=hp_listing&page=trainerfly-categories')
        );

        wp_redirect($redirect_url);
        exit;
    }
}