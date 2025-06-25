<?php

namespace Fresco\Trainerfly\Core;

interface TransformerInterface
{
    /**
     * Transform a single item to API response format
     * 
     * @param mixed $item The item to transform
     * @return array
     */
    public static function transform($item): array;

    /**
     * Transform multiple items to API response format
     * 
     * @param array $items Array of items to transform
     * @return array
     */
    public static function transformCollection(array $items): array;
} 