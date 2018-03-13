<?php
namespace fruitstudios\linkit;

use fruitstudios\linkit\fields\LinkItField;
use fruitstudios\linkit\services\LinkItService;

use Craft;
use craft\base\Plugin;
use yii\base\Event;

use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;

use craft\services\Plugins;
use craft\services\Fields;


class LinkIt extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        // Register Components (Services)
        $this->setComponents([
            'service' => LinkItService::class,
        ]);

        // Register our fields
        Event::on(Fields::className(), Fields::EVENT_REGISTER_FIELD_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = LinkItField::class;
        });

        // Do something after we're installed
        Event::on(Plugins::className(), Plugins::EVENT_AFTER_INSTALL_PLUGIN, function (PluginEvent $event) {
            if ($event->plugin === $this)
            {
                // Just installed
            }
        });

        // Log
        Craft::info(
            Craft::t('linkit', '{name} plugin loaded', [
                'name' => $this->name
            ]),
            __METHOD__
        );
    }
}
