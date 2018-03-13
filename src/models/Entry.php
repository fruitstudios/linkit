<?php
namespace fruitstudios\linkit\models;

use Craft;

use fruitstudios\linkit\LinkIt;
use fruitstudios\linkit\base\ElementLink;

use craft\elements\Entry as CraftEntry;

class Entry extends ElementLink
{
    // Private
    // =========================================================================

    private $_entry;

    // Public
    // =========================================================================

    // Static
    // =========================================================================

    public static function elementType()
    {
        return CraftEntry::class;
    }

    // Public Methods
    // =========================================================================

    public function getText(): string
    {
        if($this->customText != '')
        {
            return $this->customText;
        }
        return $this->getEntry()->title ?? $this->getUrl() ?? '';
    }

    public function getEntry()
    {
        if(is_null($this->_entry))
        {
            $this->_entry = Craft::$app->getEntries()->getEntryById((int) $this->value);
        }
        return $this->_entry;
    }
}
