<?php
namespace fruitstudios\linkit\assetbundles\fieldsettings;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class FieldSettingsAssetBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@fruitstudios/linkit/assetbundles/fieldsettings/build";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/LinkItFieldSettings.js',
        ];

        $this->css = [
            'css/styles.css',
        ];

        parent::init();
    }
}
