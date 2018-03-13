<?php
namespace fruitstudios\linkit\fields;

use fruitstudios\linkit\LinkIt;
use fruitstudios\linkit\assetbundles\field\FieldAssetBundle;
use fruitstudios\linkit\assetbundles\fieldsettings\FieldSettingsAssetBundle;
use fruitstudios\linkit\services\LinkItService;
use fruitstudios\linkit\base\Link;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json as JsonHelper;
use craft\helpers\Db as DbHelper;
use yii\db\Schema;
use yii\base\ErrorException;
use craft\validators\ArrayValidator;

class LinkItField extends Field
{

    // Private Properties
    // =========================================================================

    private $_availableLinkTypes;
    private $_enabledLinkTypes;

    //  Properties
    // =========================================================================

    public $selectLinkText = '';
    public $types;
    public $allowCustomText;
    public $defaultText;
    public $allowTarget;
    public $columnType = Schema::TYPE_TEXT;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('linkit', 'Link It');
    }

    public static function defaultSelectLinkText(): string
    {
        return Craft::t('linkit', 'Select link type...');
    }

    // Public Methods
    // =========================================================================

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['types'], ArrayValidator::class, 'min' => 1, 'tooFew' => Craft::t('linkit', 'You must select at least one link type.'), 'skipOnEmpty' => false];
        return $rules;
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public static function hasContentColumn(): bool
    {
        return true;
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        if($value instanceof Link)
        {
            return $value;
        }

        if(is_string($value))
        {
            $value = JsonHelper::decodeIfJson($value);
        }

        $link = null;

        if(isset($value['type']) && $value['type'] != '')
        {
            if(isset($value['values']))
            {
                $postedValue = $value['values'][$value['type']] ?? '';
                $value['value'] = is_array($postedValue) ? $postedValue[0] : $postedValue;
                unset($value['values']);
            }

            $link = $this->_getLinkTypeModelByType($value['type']);
            $link->setAttributes($value, false); // TODO: Get Rules added for these and remove false
        }

        return $link;
    }

    public function serializeValue($value, ElementInterface $element = null)
    {
        $serialized = [];
        if($value instanceof Link)
        {
            $serialized = [
                'type' => $value->type,
                'value' => $value->value,
                'customText' => $value->customText,
                'target' => $value->target,
            ];
        }

        return parent::serializeValue($serialized, $element);
    }

    public function getSettingsHtml()
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(FieldSettingsAssetBundle::class);

        return $view->renderTemplate(
            'linkit/fields/_settings',
            [
                'field' => $this,
            ]
        );
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();

        // Register our asset bundle
        $view->registerAssetBundle(FieldAssetBundle::class);

        // Get our id and namespace
        $id = $view->formatInputId($this->handle);
        $namespacedId = $view->namespaceInputId($id);

        // Javascript
        $jsVariables = JsonHelper::encode([
            'id' => $namespacedId,
            'name' => $this->handle,
        ]);
        $view->registerJs('new Garnish.LinkItField('.$jsVariables.');');

        // Render the input template
        return $view->renderTemplate(
            'linkit/fields/_input',
            [
                'id' => $id,
                'name' => $this->handle,
                'field' => $this,
                'currentLink' => $value,
            ]
        );
    }

    public function getAvailableLinkTypes()
    {
        if(is_null($this->_availableLinkTypes))
        {
            $linkTypes = LinkIt::$plugin->service->getAvailableLinkTypes();
            if($linkTypes)
            {
                foreach ($linkTypes as $linkType)
                {
                   $this->_availableLinkTypes[] = $this->_populateLinkTypeModel($linkType);
                }
            }
        }
        return $this->_availableLinkTypes;
    }

    public function getEnabledLinkTypes()
    {
        if(is_null($this->_enabledLinkTypes))
        {
            $this->_enabledLinkTypes = [];
            if(is_array($this->types))
            {
                foreach ($this->types as $type => $settings)
                {
                    if($settings['enabled'] ?? false) {
                        $linkType = $this->_getLinkTypeModelByType($type);
                        if($linkType)
                        {
                            $this->_enabledLinkTypes[] = $linkType;
                        }
                    }
                }
            }
        }
        return $this->_enabledLinkTypes;
    }

    public function getEnabledLinkTypesAsOptions()
    {
        $options = [];
        $enabledLinkTypes = $this->getEnabledLinkTypes();
        if($enabledLinkTypes)
        {
            $options = [
                [
                    'label' => $this->selectLinkText != '' ? $this->selectLinkText : static::defaultSelectLinkText(),
                    'value' => '',
                ],
            ];

            foreach ($enabledLinkTypes as $enabledLinkType) {
                $options[] = [
                    'label' => $enabledLinkType->label,
                    'value' => $enabledLinkType->type,
                ];
            }
        }

        return $options;
    }

    // Private Methods
    // =========================================================================

    private function _getLinkTypeModelByType(string $type, bool $populate = true)
    {
        try {
            $linkType = Craft::createObject($type);
            if($populate)
            {
                $linkType = $this->_populateLinkTypeModel($linkType);
            }
            return $linkType;
        } catch(ErrorException $exception) {
            $error = $exception->getMessage();
            return false;
        }
    }

    private function _populateLinkTypeModel(Link $linkType)
    {
        // Get Type Settings
        $attributes = $this->types[$linkType->type] ?? [];
        $linkType->setAttributes($attributes, false);
        $linkType->field = $this->getSettings();
        return $linkType;
    }

}
