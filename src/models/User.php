<?php
namespace fruitstudios\linkit\models;

use Craft;

use fruitstudios\linkit\LinkIt;
use fruitstudios\linkit\base\ElementLink;

use craft\elements\User as CraftUser;

class User extends ElementLink
{
    // Private
    // =========================================================================

    private $_user;

    // Public
    // =========================================================================

    public $userPath;

    // Static
    // =========================================================================

    public static function elementType()
    {
        return CraftUser::class;
    }

    public static function settingsTemplatePath(): string
    {
        return 'linkit/types/settings/_user';
    }

    public static function defualtUserPath(): string
    {
        return 'user/{id}';
    }

    // Public Methods
    // =========================================================================

    public function getUrl(): string
    {
        if($this->getUser())
        {
            $userPath = static::defualtUserPath();
            if($this->userPath && $this->userPath != '')
            {
                $userPath = $this->userPath;
            }

            return Craft::$app->getView()->renderObjectTemplate($userPath, $this->getUser());
        }
        return '';
    }

    public function getText(): string
    {
        if($this->customText != '')
        {
            return $this->customText;
        }
        return $this->getUser()->fullName ?? $this->getUser()->name ?? $this->getUrl() ?? '';
    }

    public function getUser()
    {
        if(is_null($this->_user))
        {
            $this->_user = Craft::$app->getUsers()->getUserById((int) $this->value);
        }
        return $this->_user;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['userPath', 'string'];
        return $rules;
    }
}
