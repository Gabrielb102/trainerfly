<?php

namespace Fresco\Trainerfly\Data;

use \HivePress\Models\Listing_Category;

// This is basically a one time use script to populate the categories for Trainerfly

class PopulateCategories {
    public static function populate() {
        // Populate the categories table
        $categories = self::getCategories();

        try {
            foreach ($categories as $category => $subcategories) {
                $categoryId = self::createCategory($category);
                foreach ($subcategories as $subcategory) {
                    self::createCategory($subcategory, $categoryId);
                }
            }
            error_log("Categories populated successfully");
            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public static function getCategories() {
        $categories = json_decode(file_get_contents(plugin_dir_path(__DIR__) . '/Data/categories.json'), true);
        return $categories;
    }

    public static function produceSlug($name) {
        // Replace all non-alphanumeric characters with dashes
        $slug = preg_replace('/[^a-zA-Z0-9]+/', '-', $name);
        
        // Convert to lowercase
        $slug = strtolower($slug);
        
        // Remove leading and trailing dashes
        $slug = trim($slug, '-');
        
        return $slug;
    }

    public static function createCategory($name, $parent = 0) {
        // Check if category already exists
        $existing = get_term_by('name', $name, 'hp_listing_category');
        if ($existing) {
            error_log("Category '$name' already exists with ID: " . $existing->term_id);
            return $existing->term_id;
        }
        
        // Validate parent category exists if specified
        if ($parent > 0) {
            $parent_term = get_term($parent, 'hp_listing_category');
            if (!$parent_term || is_wp_error($parent_term)) {
                error_log("ERROR: Parent category ID $parent does not exist for category '$name'");
                return false;
            }
        }
        
        $category = new Listing_Category([
            'name' => $name,
            'parent' => $parent,  // 0 for top-level
            'slug' => self::produceSlug($name),
        ]);
        
        // Save it - this actually creates the WordPress term
        try {
            $success = $category->save();
            
            if ($success) {
                error_log("SUCCESS: Category '$name' created with ID: " . $category->get_id());
                return $category->get_id();
            } else {
                error_log("ERROR: Category '$name' save() returned false");
                // Check if there are any WordPress errors
                global $wp_error;
                if ($wp_error && $wp_error->has_errors()) {
                    error_log("ERROR: WordPress errors: " . print_r($wp_error->get_error_messages(), true));
                }
                return false;
            }
        } catch (\Exception $e) {
            error_log("ERROR: Exception while saving category '$name': " . $e->getMessage());
            error_log("ERROR: Exception trace: " . $e->getTraceAsString());
            return false;
        }
    }
}
