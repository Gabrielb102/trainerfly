<?php

namespace Fresco\Trainerfly\API\User\Controllers;

use WP_User;

class UserController
{

    public static function getCurrent(): WP_User {
        return wp_get_current_user();
    }
}