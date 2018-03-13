<?php
namespace fruitstudios\linkit\models;

use Craft;

use fruitstudios\linkit\base\Link;
use fruitstudios\linkit\helpers\LinkItHelper;

class ElementLink extends Link
{
    // Private
    // =========================================================================

    private $_element;

    // Public Methods
    // =========================================================================

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

}
