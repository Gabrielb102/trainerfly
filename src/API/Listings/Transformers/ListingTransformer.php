<?php

namespace Fresco\Trainerfly\API\Listings\Transformers;

use HivePress\Models as Models;
use Fresco\Trainerfly\Core\TransformerInterface;

class ListingTransformer implements TransformerInterface
{
    /**
     * Transform a single listing object to API response format
     * 
     * @param object $listing The listing object from HivePress
     * @return array
     */
    public static function transform($listing): array
    {
        $vendor = $listing->get_vendor();

        return [
            'id' => $listing->get_id(),
            'title' => html_entity_decode((string) $listing->get_title(), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'url' => get_permalink($listing->get_id()),
            'latitude' => $listing->get_latitude(),
            'longitude' => $listing->get_longitude(),
            'location' => $listing->get_location(),
            'image' => $listing->get_image() ? $listing->get_image()->get_url('thumbnail') : null,
            'price' => $listing->get_price() ? $listing->get_price() : null,
            'category' => $listing->get_categories() ? $listing->get_categories()[0]->get_name() : null,
            'description' => $listing->get_description(),
            'reviews' => self::getListingReviews($listing),
            'vendor' => [
                'id' => $vendor->get_id(),
                'name' => $vendor->get_name(),
                'image' => $vendor->get_image() ? $vendor->get_image()->get_url('thumbnail') : null,
                'rating' => $vendor->get_rating(),
                'rating_count' => $vendor->get_rating_count(),
                'descriptors' => self::getVendorDescriptors($vendor),
            ],
        ];
    }

    /**
     * Transform multiple listings to API response format
     * 
     * @param array $listings Array of listing objects from HivePress
     * @return array
     */
    public static function transformCollection(array $listings): array
    {
        $transformed = [];
        
        foreach ($listings as $listing) {
            $transformed[] = self::transform($listing);
        }
        
        return $transformed;
    }

    /**
     * Get vendor descriptors
     * 
     * @param object $vendor The vendor object
     * @return array
     */
    private static function getVendorDescriptors($vendor): array
    {
        $vendor_descriptors = [];

        if ($vendor) {
            // Get ALL vendor fields, not just those configured for secondary display
            $vendor_fields = $vendor->_get_fields();

            $vendor_descriptors = $vendor_fields['descriptors']->get_value();

            if (! empty($vendor_descriptors) && is_string($vendor_descriptors)) {
                // Split the comma-separated string into an array
                $vendor_descriptors = array_map('trim', explode(',', $vendor_descriptors));

                // Remove any empty values
                $vendor_descriptors = array_filter($vendor_descriptors);
            } else {
                // Ensure it's an array even if empty or already an array
                $vendor_descriptors = is_array($vendor_descriptors) ? $vendor_descriptors : [];
            }
        }

        return $vendor_descriptors;
    }

    /**
     * Get listing reviews
     * 
     * @param object $listing The listing object
     * @return array
     */
    private static function getListingReviews($listing): array
    {
        $listing_reviews = [];
        if ($listing && hivepress()->get_version('reviews')) {
            // Get reviews for this listing only
            $reviews = Models\Review::query()->filter(
                [
                    'approved' => true,
                    'parent'   => null,
                    'listing'  => $listing->get_id(),
                ]
            )->order(['created_date' => 'desc'])
                ->limit(5) // Limit to 5 most recent reviews
                ->get();

            foreach ($reviews as $review) {
                $listing_reviews[] = [
                    'id' => $review->get_id(),
                    'text' => $review->get_text(),
                    'rating' => $review->get_rating(),
                    'created_date' => $review->get_created_date(),
                    'author_name' => $review->get_author__display_name(),
                ];
            }
        }
        return $listing_reviews;
    }
}
