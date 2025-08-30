<?php

namespace Fresco\Trainerfly\Data;

/**
 * Bulk Category Upload for HivePress
 * 
 * This script allows you to bulk create HivePress categories using provided slugs.
 */

class TFBulkCategoryUpload {

    public static $listing_categories = [
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
    

    public static function bulk_create_hivepress_categories($categories, $taxonomy = 'hp_listing_category')
    {
        $results = [];

        foreach ($categories as $cat_data) {
            // Validate required fields
            if (empty($cat_data['name']) || empty($cat_data['slug'])) {
                $results[] = [
                    'name' => $cat_data['name'] ?? 'Unknown',
                    'success' => false,
                    'error' => 'Name and slug are required'
                ];
                continue;
            }

            // Prepare category data
            $category_args = [
                'name' => $cat_data['name'],
                'description' => isset($cat_data['description']) ? $cat_data['description'] : '',
                'parent' => isset($cat_data['parent']) ? $cat_data['parent'] : 0,
                'sort_order' => isset($cat_data['sort_order']) ? $cat_data['sort_order'] : 0
            ];

            try {
                // Create category using HivePress model
                $category = new \HivePress\Models\Listing_Category($category_args);

                error_log('Attempting to create category: ' . $cat_data['name'] . ' with args: ' . print_r($category_args, true));

                $success = $category->save();
                
                if ($success) {
                    $results[] = [
                        'name' => $cat_data['name'],
                        'slug' => $cat_data['slug'],
                        'success' => true,
                        'term_id' => $category->get_id()
                    ];
                    error_log('Successfully created category: ' . $cat_data['name'] . ' with ID: ' . $category->get_id());
                } else {
                    $results[] = [
                        'name' => $cat_data['name'],
                        'slug' => $cat_data['slug'],
                        'success' => false,
                        'error' => 'Failed to save category'
                    ];
                    error_log('Failed to save category: ' . $cat_data['name']);
                }
            } catch (\Exception $e) {
                $results[] = [
                    'name' => $cat_data['name'],
                    'slug' => $cat_data['slug'],
                    'success' => false,
                    'error' => 'Exception: ' . $e->getMessage()
                ];
                error_log('Exception creating category ' . $cat_data['name'] . ': ' . $e->getMessage());
            }
        }

        return $results;
    }

    public static function getCategories() {
        $categories = json_decode(file_get_contents(plugin_dir_path(__DIR__) . '/Data/categories.json'), true);
        self::$listing_categories = $categories;
    }

    // Create the categories
    public static function doIt() {
        error_log('TFBulkCategoryUpload::doIt - Starting bulk category creation');
        // self::$listing_categories = [];
        
        // self::getCategories();
        $results = self::bulk_create_hivepress_categories(self::$listing_categories, 'hp_listing_category');
        
        // Log results
        error_log('TFBulkCategoryUpload::doIt - Completed. Results: ' . print_r($results, true));
        
        return $results;
    }
}