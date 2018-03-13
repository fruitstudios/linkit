<?php
namespace fruitstudios\linkit\models;

use Craft;

use fruitstudios\linkit\LinkIt;
use fruitstudios\linkit\base\ElementLink;

use craft\elements\Asset as CraftAsset;

class Asset extends ElementLink
{
    // Private
    // =========================================================================

    private $_asset;

    // Public
    // =========================================================================

    // Static
    // =========================================================================

    public static function elementType()
    {
        return CraftAsset::class;
    }

    // Public Methods
    // =========================================================================

    public function getText(): string
    {
        if($this->customText != '')
        {
            return $this->customText;
        }
        return $this->getAsset()->filename ?? $this->getUrl() ?? '';
    }

    public function getAsset()
    {
        if(is_null($this->_asset))
        {
            $this->_asset = Craft::$app->getAssets()->getAssetById((int) $this->value);
        }
        return $this->_asset;
    }
}
