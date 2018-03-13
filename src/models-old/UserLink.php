<?php
namespace fruitstudios\linkit\models;

use Craft;

use fruitstudios\linkit\models\ElementLink;
use fruitstudios\linkit\helpers\LinkItHelper;

class UserLink extends ElementLink
{
    // Private
    // =========================================================================

    private $_user;

    // Public
    // =========================================================================

    public $userPath = '';

    // Public Methods
    // =========================================================================

    public function getUrl(): string
    {
        return $this->getUser() ? $this->userPath.'-'.$this->getUser()->id.'-'.$this->getUser()->username : '';
    }

    public function getText(): string
    {
        if($this->customText != '')
        {
            return $this->customText;
        }
        return $this->getUser()->fullName ?? $this->getUrl() ?? '';
    }

    public function getUser()
    {
        if(is_null($this->_user))
        {
            $this->_user = Craft::$app->getUsers()->getUserById((int) $this->value);
        }
        return $this->_user;
    }

}
