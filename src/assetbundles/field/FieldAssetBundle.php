<?php
namespace fruitstudios\linkit\assetbundles\field;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class FieldAssetBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@fruitstudios/linkit/assetbundles/field/build";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/LinkItField.js',
        ];

        $this->css = [
            'css/styles.css',
        ];

        parent::init();
    }
}
