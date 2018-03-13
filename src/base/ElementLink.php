<?php
namespace fruitstudios\linkit\base;

use fruitstudios\linkit\LinkIt;

use Craft;
use craft\base\Element;

abstract class ElementLink extends Link
{
    // Private
    // =========================================================================

    private $_element;

    // Static
    // =========================================================================

    public static function elementType()
    {
        return Element::class;
    }

    public static function settingsTemplatePath(): string
    {
        return 'linkit/types/settings/_element';
    }

    public static function inputTemplatePath(): string
    {
        return 'linkit/types/input/_element';
    }

    // Public
    // =========================================================================

    public $sources = '*';
    public $customSelectionLabel;

    // Public Methods
    // =========================================================================

    public function defaultSelectionLabel(): string
    {
        return Craft::t('linkit', 'Select') . ' ' . $this->defaultLabel();
    }

    public function getSelectionLabel(): string
    {
        if(!is_null($this->customSelectionLabel) && $this->customSelectionLabel != '')
        {
            return $this->customSelectionLabel;
        }
        return $this->defaultSelectionLabel();
    }

    public function getUrl(): string
    {
        if(!$this->getElement())
        {
            return '';
        }
        return $this->getElement()->getUrl() ?? '';
    }

    public function getText(): string
    {
        if($this->customText != '')
        {
            return $this->customText;
        }
        return $this->getElement()->title ?? $this->getUrl() ?? '';
    }

    public function getElement()
    {
        if(is_null($this->_element))
        {
            $this->_element = Craft::$app->getElements()->getElementById((int) $this->value);
        }
        return $this->_element;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['customSelectionLabel', 'string'];
        return $rules;
    }

    // Protected Methods
    // =========================================================================

    public function getSourceOptions(): array
    {
        return LinkIt::$plugin->service->getSourceOptions($this->elementType());
    }

}
