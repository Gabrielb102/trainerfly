<?php

namespace Fresco\Trainerfly\API\Listings\Transformers;

use Fresco\Trainerfly\Core\TransformerInterface;

class CategoryTransformer implements TransformerInterface
{
    /**
     * Transform a single category object to API response format
     * 
     * @param object $category The category object from database
     * @return array
     */
    public static function transform($category): array
    {
        // Get category image if it exists
        $image_url = null;
        $image_id = get_term_meta($category->term_id, 'hp_image', true);
        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
        }

        return [
            'id' => (int)$category->term_id,
            'name' => $category->name,
            'description' => $category->description,
            'slug' => $category->slug,
            'parent' => (int)$category->parent,
            'image' => $image_url,
            'listing_count' => (int)$category->listing_count,
            'url' => get_term_link($category->term_id),
        ];
    }

    /**
     * Transform multiple categories to API response format
     * 
     * @param array $categories Array of category objects from database
     * @return array
     */
    public static function transformCollection(array $categories): array
    {
        $transformed = [];
        
        foreach ($categories as $category) {
            $transformed[] = self::transform($category);
        }
        
        return $transformed;
    }
}
