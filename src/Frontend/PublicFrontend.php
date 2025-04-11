<?php

namespace Fresco\Trainerfly\Frontend;

class PublicFrontend
{
    public static function displayFrontend(): string
    {
        return "<div id='trainerfly'></div>";
    }

    public static function registerShortcode(): void
    {
        add_shortcode('trainerfly', array(self::class, 'displayFrontend'));
    }
}

