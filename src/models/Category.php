<?php
namespace fruitstudios\linkit\models;

use Craft;

use fruitstudios\linkit\LinkIt;
use fruitstudios\linkit\base\ElementLink;

use craft\elements\Category as CraftCategory;

class Category extends ElementLink
{
    // Private
    // =========================================================================

    private $_category;

    // Public
    // =========================================================================

    // Static
    // =========================================================================

    public static function elementType()
    {
        return CraftCategory::class;
    }

    // Public Methods
    // =========================================================================

    public function getText(): string
    {
        if($this->customText != '')
        {
            return $this->customText;
        }
        return $this->getCategory()->title ?? $this->getUrl() ?? '';
    }

    public function getCategory()
    {
        if(is_null($this->_category))
        {
            $this->_category = Craft::$app->getCategories()->getCategoryById((int) $this->value);
        }
        return $this->_category;
    }
}
