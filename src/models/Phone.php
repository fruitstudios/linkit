<?php
namespace fruitstudios\linkit\models;

use Craft;

use fruitstudios\linkit\LinkIt;
use fruitstudios\linkit\base\Link;

class Phone extends Link
{
    // Private
    // =========================================================================

    // Public
    // =========================================================================

    // Static
    // =========================================================================

    public static function defaultLabel(): string
    {
        return Craft::t('linkit', 'Phone Number');
    }

    public static function defaultPlaceholder(): string
    {
        return Craft::t('linkit', '+44(0)0000 000000');
    }

    // Public Methods
    // =========================================================================

    public function getUrl(): string
    {
        return (string) 'tel:'.$this->value;
    }

}
