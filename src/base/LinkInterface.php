<?php
namespace fruitstudios\linkit\base;

use fruitstudios\linkit\base\Link;

use Craft;
use craft\base\SavableComponentInterface;

interface LinkInterface extends SavableComponentInterface
{
    // Static
    // =========================================================================

    public static function defaultLabel(): string;
    public static function defaultPlaceholder(): string;
    public static function settingsTemplatePath(): string;
    public static function inputTemplatePath(): string;
    public static function hasSettings(): bool;

    // Public Methods
    // =========================================================================

    public function defaultSelectionLabel(): string;

    public function getLabel(): string;
    public function getSelectionLabel(): string;

    public function getInputHtml(string $name, Link $currentLink = null): string;
    public function getSettingsHtml(): string;

    public function getType(): string;
    public function getTypeHandle(): string;

    public function getLink($raw = true);
    public function getUrl(): string;
    public function getText(): string;

}
