<?php
/**
 * Bulk Category Creation Class
 * 
 * @package TrainerFly
 */

if (!defined('ABSPATH')) {
    exit;
}

class tf_bulk_cats {
    
    /**
     * Initialize the class
     */
    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
        add_action('wp_footer', [__CLASS__, 'add_dialog_html']);
        add_action('wp_ajax_tf_create_categories', [__CLASS__, 'ajax_create_categories']);
        add_action('wp_ajax_nopriv_tf_create_categories', [__CLASS__, 'ajax_create_categories']);
    }
    
    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('tf-bulk-cats', plugin_dir_url(__FILE__) . '../../assets/js/tf-bulk-cats.js', ['jquery'], '1.0.0', true);
        wp_localize_script('tf-bulk-cats', 'tf_bulk_cats_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tf_bulk_cats_nonce')
        ]);
    }
    
    /**
     * Add dialog HTML to footer
     */
    public static function add_dialog_html() {
        ?>
        <div id="tf-bulk-cats-dialog" style="display: none;">
            <div class="tf-dialog-content">
                <h3>⚠️ Bulk Category Creation</h3>
                <p>This will create <strong>18 new categories</strong> in HivePress:</p>
                <ul>
                    <li>Professional & Career Skills</li>
                    <li>Real Estate Skills</li>
                    <li>Gaming (Video Gaming Skills)</li>
                    <li>Gambling & Sports Betting Skills</li>
                    <li>Trades & Technical Skills</li>
                    <li>Digital & Tech Skills</li>
                    <li>Creative & Artistic Skills</li>
                    <li>Musical Skills</li>
                    <li>Intellectual & Academic Skills</li>
                    <li>Health, Wellness & Spiritual Growth</li>
                    <li>Physical & Athletic Skills</li>
                    <li>Tactical & Survival Skills</li>
                    <li>Financial & Wealth-Building Skills</li>
                    <li>Culinary Skills</li>
                    <li>Adventurous & Experiential Skills</li>
                    <li>Relationship & Social Skills</li>
                    <li>Sports (All Types)</li>
                    <li>Personal Training Skills</li>
                </ul>
                <p><strong>Are you sure you want to proceed?</strong></p>
                <div class="tf-dialog-buttons">
                    <button id="tf-confirm-categories" class="button button-primary">Yes, Create Categories</button>
                    <button id="tf-cancel-categories" class="button button-secondary">Cancel</button>
                </div>
                <div id="tf-progress" style="display: none;">
                    <p>Creating categories... <span id="tf-progress-text">0/18</span></p>
                    <div class="tf-progress-bar">
                        <div class="tf-progress-fill"></div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            #tf-bulk-cats-dialog {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.7);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .tf-dialog-content {
                background: white;
                padding: 30px;
                border-radius: 8px;
                max-width: 500px;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            }
            .tf-dialog-content h3 {
                margin-top: 0;
                color: #d63638;
            }
            .tf-dialog-content ul {
                max-height: 200px;
                overflow-y: auto;
                border: 1px solid #ddd;
                padding: 15px;
                background: #f9f9f9;
            }
            .tf-dialog-content li {
                margin-bottom: 5px;
                font-size: 14px;
            }
            .tf-dialog-buttons {
                margin-top: 20px;
                text-align: center;
            }
            .tf-dialog-buttons button {
                margin: 0 10px;
                padding: 10px 20px;
            }
            .tf-progress-bar {
                width: 100%;
                height: 20px;
                background: #f0f0f0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 10px;
            }
            .tf-progress-fill {
                height: 100%;
                background: #0073aa;
                width: 0%;
                transition: width 0.3s ease;
            }
        </style>
        <?php
    }
    
    /**
     * AJAX handler for creating categories
     */
    public static function ajax_create_categories() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'tf_bulk_cats_nonce')) {
            wp_die('Security check failed');
        }
        
        // Check if user has permission
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $categories = [
            [
                'name' => 'Professional & Career Skills',
                'slug' => 'professional-career-skills',
                'description' => 'Skills related to professional development and career advancement',
                'parent' => 0,
                'sort_order' => 1
            ],
            [
                'name' => 'Real Estate Skills',
                'slug' => 'real-estate-skills',
                'description' => 'Skills related to real estate, property management, and real estate investment',
                'parent' => 0,
                'sort_order' => 2
            ],
            [
                'name' => 'Gaming (Video Gaming Skills)',
                'slug' => 'gaming-video-gaming-skills',
                'description' => 'Skills related to video gaming, esports, and gaming strategy',
                'parent' => 0,
                'sort_order' => 3
            ],
            [
                'name' => 'Gambling & Sports Betting Skills',
                'slug' => 'gambling-sports-betting-skills',
                'description' => 'Skills related to gambling strategies and sports betting analysis',
                'parent' => 0,
                'sort_order' => 4
            ],
            [
                'name' => 'Trades & Technical Skills',
                'slug' => 'trades-technical-skills',
                'description' => 'Hands-on technical skills and trade-related expertise',
                'parent' => 0,
                'sort_order' => 5
            ],
            [
                'name' => 'Digital & Tech Skills',
                'slug' => 'digital-tech-skills',
                'description' => 'Digital technology skills and computer-related expertise',
                'parent' => 0,
                'sort_order' => 6
            ],
            [
                'name' => 'Creative & Artistic Skills',
                'slug' => 'creative-artistic-skills',
                'description' => 'Creative and artistic skills including design, art, and creative expression',
                'parent' => 0,
                'sort_order' => 7
            ],
            [
                'name' => 'Musical Skills',
                'slug' => 'musical-skills',
                'description' => 'Musical skills including instruments, singing, and music production',
                'parent' => 0,
                'sort_order' => 8
            ],
            [
                'name' => 'Intellectual & Academic Skills',
                'slug' => 'intellectual-academic-skills',
                'description' => 'Intellectual and academic skills including research, analysis, and learning',
                'parent' => 0,
                'sort_order' => 9
            ],
            [
                'name' => 'Health, Wellness & Spiritual Growth',
                'slug' => 'health-wellness-spiritual-growth',
                'description' => 'Skills related to health, wellness, and spiritual development',
                'parent' => 0,
                'sort_order' => 10
            ],
            [
                'name' => 'Physical & Athletic Skills',
                'slug' => 'physical-athletic-skills',
                'description' => 'Physical and athletic skills including sports, fitness, and movement',
                'parent' => 0,
                'sort_order' => 11
            ],
            [
                'name' => 'Tactical & Survival Skills',
                'slug' => 'tactical-survival-skills',
                'description' => 'Tactical and survival skills including self-defense and emergency preparedness',
                'parent' => 0,
                'sort_order' => 12
            ],
            [
                'name' => 'Financial & Wealth-Building Skills',
                'slug' => 'financial-wealth-building-skills',
                'description' => 'Skills related to financial management, investing, and wealth building',
                'parent' => 0,
                'sort_order' => 13
            ],
            [
                'name' => 'Culinary Skills',
                'slug' => 'culinary-skills',
                'description' => 'Cooking and culinary skills including food preparation and cooking techniques',
                'parent' => 0,
                'sort_order' => 14
            ],
            [
                'name' => 'Adventurous & Experiential Skills',
                'slug' => 'adventurous-experiential-skills',
                'description' => 'Adventure and experiential skills including outdoor activities and exploration',
                'parent' => 0,
                'sort_order' => 15
            ],
            [
                'name' => 'Relationship & Social Skills',
                'slug' => 'relationship-social-skills',
                'description' => 'Skills related to building relationships and social interactions',
                'parent' => 0,
                'sort_order' => 16
            ],
            [
                'name' => 'Sports (All Types)',
                'slug' => 'sports-all-types',
                'description' => 'All types of sports skills and athletic training',
                'parent' => 0,
                'sort_order' => 17
            ],
            [
                'name' => 'Personal Training Skills',
                'slug' => 'personal-training-skills',
                'description' => 'Personal training and fitness instruction skills',
                'parent' => 0,
                'sort_order' => 18
            ]
        ];
        
        $results = [];
        $total = count($categories);
        
        foreach ($categories as $index => $cat_data) {
            try {
                $category = new \HivePress\Models\Listing_Category([
                    'name' => $cat_data['name'],
                    'description' => $cat_data['description'],
                    'parent' => $cat_data['parent'],
                    'sort_order' => $cat_data['sort_order']
                ]);
                
                $success = $category->save();
                
                if ($success) {
                    $results[] = [
                        'name' => $cat_data['name'],
                        'success' => true,
                        'term_id' => $category->get_id()
                    ];
                } else {
                    $results[] = [
                        'name' => $cat_data['name'],
                        'success' => false,
                        'error' => 'Failed to save category'
                    ];
                }
            } catch (Exception $e) {
                $results[] = [
                    'name' => $cat_data['name'],
                    'success' => false,
                    'error' => 'Exception: ' . $e->getMessage()
                ];
            }
            
            // Send progress update
            $progress = ($index + 1) / $total * 100;
            wp_send_json_success([
                'progress' => $progress,
                'current' => $index + 1,
                'total' => $total,
                'results' => $results
            ]);
        }
        
        wp_send_json_success([
            'progress' => 100,
            'current' => $total,
            'total' => $total,
            'results' => $results,
            'complete' => true
        ]);
    }
    
    /**
     * Static method to trigger the dialog
     */
    public static function doIt() {
        // This will be called from your main plugin file
        // The dialog will appear on the frontend
        add_action('wp_footer', function() {
            echo '<script>jQuery(document).ready(function($) { $("#tf-bulk-cats-dialog").show(); });</script>';
        });
    }
}
