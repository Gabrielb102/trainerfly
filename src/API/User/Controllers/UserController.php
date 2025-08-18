<?php

namespace Fresco\Trainerfly\API\User\Controllers;

use WP_User;
use Fresco\Trainerfly\API\User\Transformers\UserTransformer;

class UserController
{
    public static function getCurrent(): array | null {
        $wp_user = wp_get_current_user();
        
        if (!$wp_user->exists()) {
            return null;
        }

        $is_vendor = self::isUserVendor($wp_user->ID);
        $pending_messages_count = self::getPendingMessagesCount($wp_user->ID);

        $wp_user->is_vendor = $is_vendor;
        $wp_user->pending_messages_count = $pending_messages_count;

        return UserTransformer::transform($wp_user);
    }

    /**
     * Check if a user is a vendor using HivePress
     */
    private static function isUserVendor(int $user_id): bool {
        if (!class_exists('\HivePress\Models\Vendor')) {
            return false;
        }

        try {
            $vendor = \HivePress\Models\Vendor::query()->filter([
                'user' => $user_id,
                'status' => 'publish'
            ])->get_first();

            return !empty($vendor);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get pending messages count using HivePress message cache
     */
    private static function getPendingMessagesCount(int $user_id): int {
        try {
            if (!class_exists('\HivePress\Core')) {
                return 0;
            }

            $unread_count = hivepress()->cache->get_user_cache(
                $user_id, 
                'unread_count', 
                'models/message'
            );

            return is_numeric($unread_count) ? (int) $unread_count : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get vendor statistics if user is a vendor
     */
    private static function getVendorStats(int $user_id): array {
        try {
            if (!class_exists('\HivePress\Core')) {
                return [];
            }

            $stats = [];
            
            // Get vendor order counts
            $vendor_order_count = hivepress()->cache->get_user_cache(
                $user_id, 
                'vendor_order_count', 
                'models/order'
            );
            
            $vendor_order_processing_count = hivepress()->cache->get_user_cache(
                $user_id, 
                'vendor_order_processing_count', 
                'models/order'
            );

            // Get payout count
            $payout_count = hivepress()->cache->get_user_cache(
                $user_id, 
                'payout_count', 
                'models/payout'
            );

            // Get vendor count (should be 1 if user is a vendor)
            $vendor_count = hivepress()->cache->get_user_cache(
                $user_id, 
                'vendor_count', 
                'models/vendor'
            );

            $stats = [
                'order_count' => is_numeric($vendor_order_count) ? (int) $vendor_order_count : 0,
                'processing_orders' => is_numeric($vendor_order_processing_count) ? (int) $vendor_order_processing_count : 0,
                'payout_count' => is_numeric($payout_count) ? (int) $payout_count : 0,
                'vendor_count' => is_numeric($vendor_count) ? (int) $vendor_count : 0,
            ];

            return $stats;
        } catch (\Exception $e) {
            return [];
        }
    }
}