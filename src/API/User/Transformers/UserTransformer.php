<?php

namespace Fresco\Trainerfly\API\User\Transformers;

use WP_User;
use Fresco\Trainerfly\Core\TransformerInterface;

class UserTransformer implements TransformerInterface
{
    /**
     * Transform a single user object to API response format
     * 
     * @param WP_User $user The WordPress user object
     * @return array
     */
    public static function transform($user): array
    {

        return [
            'ID' => $user->ID,
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'user_registered' => $user->user_registered,
            'roles' => $user->roles,
            'capabilities' => $user->caps,
            'allcaps' => $user->allcaps,
            // HivePress specific data
            'is_vendor' => $user->is_vendor,
            'pending_messages_count' => $user->pending_messages_count,
        ];
    }

    /**
     * Transform multiple users to API response format
     * 
     * @param array $users Array of WP_User objects
     * @return array
     */
    public static function transformCollection(array $users): array
    {
        $transformed = [];
        
        foreach ($users as $user) {
            $transformed[] = self::transform($user);
        }
        
        return $transformed;
    }
}
