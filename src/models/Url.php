<?php
namespace fruitstudios\linkit\models;

use Craft;

use fruitstudios\linkit\LinkIt;
use fruitstudios\linkit\base\Link;

class Url extends Link
{
    // Private
    // =========================================================================

    // Public
    // =========================================================================

    // Static
    // =========================================================================

    public static function defaultLabel(): string
    {
        return Craft::t('linkit', 'URL');
    }

    public static function defaultPlaceholder(): string
    {
        return Craft::t('linkit', 'https://domain.com');
    }

    // Public Methods
    // =========================================================================

}
