<?php
namespace Craft;

class FruitLinkItFieldType extends BaseFieldType
{
    public function getName()
    {
        return Craft::t('Link It');
    }

    public function defineContentAttribute()
    {
        return array(AttributeType::Mixed);
    }

	public function getSettingsHtml()
	{
		return craft()->templates->render('fruitlinkit/_fieldtype/settings', array(
			'settings'             		=> $this->getSettings(),
            'types' 			   		=> $this->_getAvaiableLinkItTypes(),
            'elementSources'            => craft()->fruitLinkIt->getLinkItElementSources(),
		));
	}

    public function getInputHtml($name, $value)
    {
        // CSS
        craft()->templates->includeCssResource('fruitlinkit/css/linkit.css');

        // Javascript
        $id = craft()->templates->formatInputId($name);
        craft()->templates->includeJsResource('fruitlinkit/js/FruitLinkIt.js');
        craft()->templates->includeJs('new FruitLinkIt("'.craft()->templates->namespaceInputId($id).'");');

    	// Settings
    	$settings = $this->getSettings();

    	// LinkIt Types
		$availableTypes = $this->_getLinkItTypes();
		$types = array('' => Craft::t('Link To...'));

       	if(is_array($settings['types']))
    	{
			foreach($settings['types'] as $type)
			{
				$types[$type] = $availableTypes[$type];
			}
    	}
    	else
    	{
	    	$types = $types + $availableTypes;
    	}

        // Element Select Options
        $elementSelectSettings = array(
            'entry' => array(
                'elementType' => new ElementTypeVariable( craft()->elements->getElementType(ElementType::Entry) ),
                'elements' => $value && $value->entry ? array($value->entry) : null,
                'sources' => $settings->entrySources,
                'criteria' => array(
                    'status' => null,
                ),
                'sourceElementId' => ( isset($this->element->id) ? $this->element->id : null ),
                'limit' => 1,
                'addButtonLabel' => Craft::t($settings->entrySelectionLabel),
                'storageKey' => 'field.'.$this->model->id,
            ),
            'asset' => array(
                'elementType' => new ElementTypeVariable( craft()->elements->getElementType(ElementType::Asset) ),
                'elements' => $value && $value->asset ? array($value->asset) : null,
                'sources' => $settings->assetSources,
                'criteria' => array(
                    'status' => null,
                ),
                'sourceElementId' => ( isset($this->element->id) ? $this->element->id : null ),
                'limit' => 1,
                'addButtonLabel' => Craft::t($settings->assetSelectionLabel),
                'storageKey' => 'field.'.$this->model->id,
            ),
            'category' => array(
                'elementType' => new ElementTypeVariable( craft()->elements->getElementType(ElementType::Category) ),
                'elements' => $value && $value->category ? array($value->category) : null,
                'sources' => $settings->categorySources,
                'criteria' => array(
                    'status' => null,
                ),
                'sourceElementId' => ( isset($this->element->id) ? $this->element->id : null ),
                'limit' => 1,
                'addButtonLabel' => Craft::t($settings->categorySelectionLabel),
                'storageKey' => 'field.'.$this->model->id,
            )
        );

		// Render Field
    	return craft()->templates->render('fruitlinkit/_fieldtype/input', array(
            'name'  => $name,
            'value' => $value,
            'settings' => $settings,
            'types' => $types,
            'elementSelectSettings' => $elementSelectSettings,
        ));
    }

	/**
	 * Returns the input value as it should be saved to the database.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function prepValueFromPost($value)
	{
        if( is_array($value) && $value['type'] != '' )
        {
    		return json_encode($value);
        }
        else
        {
            return '';
        }
	}

	/**
	 * Preps the field value for use.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function prepValue($value)
	{
        return $this->_valueToModel($value);
	}

	public function validate($value)
	{
        if(is_array($value) && $value['type'] != '')
        {
            $linkModel = $this->_valueToModel($value);
            $validated = $linkModel->validate();
            return $validated ? true : $linkModel->getAllErrors();
        }
        parent::validate($value);
	}


    // Protected Methods
    // =========================================================================

    protected function getSettingsModel()
    {
        return new FruitLinkIt_LinkSettingsModel();
    }


    // Private Methods
    // =========================================================================

    private function _getAvaiableLinkItTypes()
	{
        $types = $this->_getLinkItTypes();
        $sources = craft()->fruitLinkIt->getLinkItElementSources();
        if(!$sources['entry'])
        {
            unset($types['entry']);
        }
        if(!$sources['category'])
        {
            unset($types['category']);
        }
        if(!$sources['asset'])
        {
            unset($types['asset']);
        }
        return $types;
	}

    private function _getLinkItTypes()
	{
		return array(
			'email' => Craft::t('Email Address'),
			'tel' => Craft::t('Phone Number'),
			'custom' => Craft::t('Custom URL'),
			'entry' => Craft::t('Entry'),
			'category' => Craft::t('Category'),
			'asset' => Craft::t('Asset'),
		);
	}

    private function _valueToModel($value, $settings = false)
	{
        if( is_array($value) && $value['type'] != '' )
        {
            $settings = $settings ? $settings : $this->getSettings();

            $link = new FruitLinkIt_LinkModel;

            $value = $this->_prepLinkItValueArray($value);

            $link->type = isset($value['type']) && $value['type'] != '' ? $value['type'] : false;
            $link->value = $link->type ? $value[$link->type] : false;
            $link->customText = isset($value['customText']) ? $value['customText'] : false;
            $link->defaultText = $settings->defaultText;
            $link->target = isset($value['target']) ? ($value['target'] ? '_blank' : false) : false;


            return $link;
        }

        return '';
	}

    private function _prepLinkItValueArray(array $value)
    {
        // Update pre link it v2.0 array keys
        if(array_key_exists('text', $value))
        {
            $value['customText'] = $value['text'];
            unset($value['text']);
        }
        return $value;
    }

}
