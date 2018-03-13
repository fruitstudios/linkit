<?php

namespace fruitstudios\linkit\helpers;

use Craft;

class LinkItHelper
{
    // Public Methods
    // =========================================================================

    public static function getLinkHtml(string $url = '#', string $text = '', array $attributes = [])
    {
        $attributesString = '';
        foreach ($attributes as $attribute => $value)
        {
        	$attributesString .= ' '.$attribute.'="'.$value.'"';
        }
        return '<a href="'.$url.'"'.$attributesString.'>'.$text.'</a>';
    }

}
