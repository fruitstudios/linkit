<?php
namespace Craft;

class Linkit_LinkitFieldType extends BaseFieldType
{
    /**
     * Fieldtype name
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Linkit');
    }
    
	/**
	 * Returns the link field options.
	 *
	 * @return array
	 */
	public function getLinkitTypes()
	{
		return array(
			'email' => 'Email Address',
			'tel' => 'Phone Number',
			'custom' => 'Custom URL',
			'entry' => 'Entry',
			'asset' => 'Asset'
		);
	}
	
	/**
	 * Returns the default values
	 *
	 * @return array
	 */
	public function getLinkitValueDefaults()
	{
		return array(
			'type' => false,
			
			'email' => false,
			'custom' => false,
			'tel' => false,
			'entry' => false,
			'asset' => false,
			
			'text' => false,
			'target' => false,

			'link' => false,
			
			'linkText' => false,
			'url' => false,
			
			'entryCriteria' => false,
			'assetCriteria' => false,
		);
	}

    /**
     * Define database column
     *
     * @return AttributeType::String
     */
    public function defineContentAttribute()
    {
        return array(AttributeType::Mixed);
    }
    
	/**
	 * Defines the settings.
	 *
	 * @access protected
	 * @return array
	 */
	protected function defineSettings()
	{
	
		$settings['types'] = AttributeType::Mixed;
		$settings['text'] = AttributeType::Bool;	
		$settings['target'] = AttributeType::Bool;	
	
		$settings['entrySources'] = AttributeType::Mixed;
		$settings['entryTargetLocale'] = AttributeType::String;
		
		$settings['assetSources'] = AttributeType::Mixed;
		$settings['assetTargetLocale'] = AttributeType::String;	
		
		return $settings;
	}

	/**
	 * Returns the field's settings HTML.
	 *
	 * @return string|null
	 */
	public function getSettingsHtml()
	{		
		$entryElementType = craft()->elements->getElementType(ElementType::Entry);
		$assetElementType = craft()->elements->getElementType(ElementType::Asset);
	
		return craft()->templates->render('linkit/_fieldtype/settings', array(
			'types' 			   		=> $this->getLinkitTypes(),
			'entrySources'         		=> $this->getElementSources($entryElementType),
			'entryTargetLocaleField'    => $this->getTargetLocaleFieldHtml($entryElementType, $this->getSettings()->entryTargetLocale, 'Entry'),
			'assetSources'         		=> $this->getElementSources($assetElementType),
			'assetTargetLocaleField'    => $this->getTargetLocaleFieldHtml($assetElementType, $this->getSettings()->assetTargetLocale, 'Asset'),
			'settings'             		=> $this->getSettings()
		));
	}


    /**
     * Display our fieldtype
     *
     * @param string $name  Our fieldtype handle
     * @return string Return our fields input template
     */
    public function getInputHtml($name, $value)
    {
    	// Settings
    	$settings = $this->getSettings();
    	
    	// Linkit Types
		$availableTypes = $this->getLinkitTypes();	   
		$types = array('' => 'Link To...');		 	
       	if(is_array($settings['types']))
    	{
			foreach ($settings['types'] as $type)
			{
				$types[$type] = $availableTypes[$type];
			}
    	}
    	else
    	{
	    	$types = $types + $availableTypes;
    	}
    	
    	// Setup Entry Field
		$entryElementType = craft()->elements->getElementType(ElementType::Entry);
		
		if(is_array($value))
		{
			if(!($value['entryCriteria'] instanceof ElementCriteriaModel))
			{
				$value['entryCriteria'] = craft()->elements->getCriteria(ElementType::Entry);
				$value['entryCriteria']->id = false;
			}
		}
		else
		{
			$defaultEntryCriteria = craft()->elements->getCriteria(ElementType::Entry);
			$defaultEntryCriteria->id = false;
		}
		//$value['entryCriteria']->status = null;
		//$value['entryCriteria']->localeEnabled = null;
		

		$entrySelectionCriteria = array();
		$entrySelectionCriteria['localeEnabled'] = null;
		$entrySelectionCriteria['locale'] = $this->getTargetLocale('Entry');		
		$entrySelectionCriteria['status'] = null;

		$entryVariables = array(
			'jsClass'            => 'Craft.BaseElementSelectInput',
			'elementType'        => new ElementTypeVariable($entryElementType),
			'id'                 => craft()->templates->formatInputId($name.'[entry]'),
			'fieldId'            => $this->model->id,
			'storageKey'         => 'field.'.$this->model->id,
			'name'               => $name.'[entry]',
			'elements'           => (isset($value['entryCriteria']) ? $value['entryCriteria'] : $defaultEntryCriteria),
			'sources'            => $settings->entrySources,
			'criteria'           => $entrySelectionCriteria,
			'sourceElementId'    => (isset($this->element->id) ? $this->element->id : null),
			'limit'              => 1,
			'addButtonLabel'     => 'Select Entry'
		);
		
    	// Setup Asset Field
		$assetElementType = craft()->elements->getElementType(ElementType::Asset);
		
		if(is_array($value))
		{
			if(!($value['assetCriteria'] instanceof ElementCriteriaModel))
			{
				$value['assetCriteria'] = craft()->elements->getCriteria(ElementType::Asset);
				$value['assetCriteria']->id = false;
			}
		}
		else
		{
			$defaultAssetCriteria = craft()->elements->getCriteria(ElementType::Asset);
			$defaultAssetCriteria->id = false;
		}
		//$value['assetCriteria']->status = null;
		//$value['assetCriteria']->localeEnabled = null;
		
		$assetSelectionCriteria = array();
		$assetSelectionCriteria['localeEnabled'] = null;
		$assetSelectionCriteria['locale'] = $this->getTargetLocale('Asset');		
		$assetSelectionCriteria['status'] = null;

		$assetVariables = array(
			//'jsClass'            => 'Craft.AssetSelectInput',
			'jsClass'            => 'Craft.BaseElementSelectInput',
			'elementType'        => new ElementTypeVariable($assetElementType),
			'id'                 => craft()->templates->formatInputId($name.'[asset]'),
			'fieldId'            => $this->model->id,
			'storageKey'         => 'field.'.$this->model->id,
			'name'               => $name.'[asset]',
			'elements'           => (isset($value['assetCriteria']) ? $value['assetCriteria'] : $defaultAssetCriteria),
			'sources'            => $settings->assetSources,
			'criteria'           => $assetSelectionCriteria,
			'sourceElementId'    => (isset($this->element->id) ? $this->element->id : null),
			'limit'              => 1,
			'addButtonLabel'     => 'Select Asset'
		);
		
    	
       	// Include Javascript & CSS
		craft()->templates->includeJsResource('lib/fileupload/jquery.ui.widget.js');
		craft()->templates->includeJsResource('lib/fileupload/jquery.fileupload.js');
    	craft()->templates->includeJsResource('linkit/js/linkit.js');
    	craft()->templates->includeCssResource('linkit/css/linkit.css');
    	
		// Render Field
    	return craft()->templates->render('linkit/_fieldtype/input', array(
            'name'  => $name,
            'value' => $value,
            'types' => $types,
            'settings' => $settings,
            'entryVariables' => $entryVariables,        
            'assetVariables' => $assetVariables        
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
		return json_encode($value);
	}

	/**
	 * Preps the field value for use.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function prepValue($value)
	{
		
		if(is_array($value))
		{
			// Get Defualts
			$defaults = $this->getLinkitValueDefaults();
		
			// Merge With Defaults
			$value = array_merge($defaults, $value);
			
			
			// Process?
			if($value['type'] == '')
			{
				$value = false;	
			}
			else
			{
				
				// Process Entry Field - Criteria
				// TODO - Should I Be Using the craft()->entries->getEntryById( $entryId )
				$entryCriteria = craft()->elements->getCriteria(ElementType::Entry);
				if($value['entry'] && $value['type'] == 'entry')
				{
					if(is_array($value['entry']))
					{
						$entryCriteria->id = array_values(array_filter($value['entry']));
					}
					else
					{
						$entryCriteria->id = false;
					}
				}
				else
				{
					$entryCriteria->id = false;
				}
				$value['entryCriteria'] = $entryCriteria;

				
				
				// Process Asset Field - Criteria
				$assetCriteria = craft()->elements->getCriteria(ElementType::Asset);
				if($value['asset'] && $value['type'] == 'asset')
				{
					if(is_array($value['asset']))
					{
						$assetCriteria->id = array_values(array_filter($value['asset']));
					}
					else
					{
						$assetCriteria->id = false;
					}
				}
				else
				{
					$assetCriteria->id = false;
				}
				$value['assetCriteria'] = $assetCriteria;	
					
				
				/* 
				Alternate Version Added from BaseElementFieldType - Do we need all this?	
			
				$entryCriteria = craft()->elements->getCriteria(ElementType::Entry);
				$entryCriteria->locale = $this->getEntryTargetLocale();
				$entryCriteria->limit = null;
				
				if($value['entry'])
				{
					// $value will be an array of element IDs if there was a validation error
					// or we're loading a draft/version.
					if (is_array($value['entry']))
					{
						$entryCriteria->id = array_values(array_filter($value['entry']));
						$entryCriteria->fixedOrder = true;
					}
					else if ($value['entry'] === '')
					{
						$entryCriteria->id = false;
					}
					else if (isset($this->element) && $this->element->id)
					{
						$entryCriteria->relatedTo = array(
							'sourceElement' => $this->element->id,
							'sourceLocale'  => $this->element->locale,
							'field'         => $this->model->id
						);
				
						if ($this->sortable)
						{
							$entryCriteria->order = 'sortOrder';
						}
					}
					else
					{
						$entryCriteria->id = false;
					}
				}
				else
				{
					$entryCriteria->id = false;
				}
				$value['criteria'] = $entryCriteria;			
				*/						

				
				// Define Links, URL & Link Text Per Type
				switch($value['type'])
				{
					case('email'): 
						$value['url'] = ($value['email'] ? 'mailto:'.$value['email'] : false);
						$value['linkText'] = ($value['text'] ? $value['text'] : $value['email']);
						break;
	
					case('tel'):
						$value['url'] = ($value['tel'] ? 'tel:'.$value['tel'] : false);
						$value['linkText'] = ($value['text'] ? $value['text'] : $value['tel']);
						break;
	
					case('custom'):
						$value['url'] = ($value['custom'] ? $value['custom'] : false);
						$value['linkText'] = ($value['text'] ? $value['text'] : $value['custom']);
						break;
	
					case('entry'):
						if($entryCriteria->first())
						{
							$value['entry'] = $entryCriteria->first();
							$value['url'] = $entryCriteria->first()->getUrl();
							$value['linkText'] = ($value['text'] ? $value['text'] : $entryCriteria->first()->title);
						}
						else
						{
							$value['entry'] = false; 
							$value['url'] = false;
							$value['linkText'] = ($value['text'] ? $value['text'] : '');
						}
						break;
						
					case('asset'):
						if($assetCriteria->first())
						{
							$value['asset'] = $assetCriteria->first();
							$value['url'] = $assetCriteria->first()->getUrl();
							$value['linkText'] = ($value['text'] ? $value['text'] : $assetCriteria->first()->title);
						}
						else
						{
							$value['asset'] = false; 
							$value['url'] = false;
							$value['linkText'] = ($value['text'] ? $value['text'] : '');
						}
						break;
				}
				
				// Set Unused Link Types To false
				$value['email'] = ($value['type'] == 'email' ? $value['email'] : false);
				$value['custom'] = ($value['type'] == 'custom' ? $value['custom'] : false);
				$value['tel'] = ($value['type'] == 'tel' ? $value['tel'] : false);
				$value['entry'] = ($value['type'] == 'entry' ? $value['entry'] : false);
				$value['asset'] = ($value['type'] == 'asset' ? $value['asset'] : false);
				
				// Set Target
				$value['target'] = ($value['target'] == '1' ? '_blank' : false);
				
				// Build The Link			
				$value['link'] = ($value['url'] ? '<a href="'.$value['url'] .'"'.($value['target'] ? ' target="'.$value['target'].'"' : '').' title="'.$value['linkText'].'">'.$value['linkText'].'</a>' : false);

			}
		}				
		
		return $value;
		
	}    
	
	/**
	 * Returns the locale that target elements should have.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getTargetLocale($type)
	{
		$targetLocale = false;
		if ($type == 'Entry')
		{
			$targetLocale = $this->getSettings()->entryTargetLocale;
		}
		else if ($type == 'Asset')
		{
			$targetLocale = $this->getSettings()->entryTargetLocale;
		}

		if ($targetLocale)
		{
			return $targetLocale;
		}
		else if (isset($this->element))
		{
			return $this->element->locale;
		}
		else
		{
			return craft()->language;
		}
	}
	
	
	/**
	 * Validates the value beyond the checks that were assumed based on the content attribute.
	 *
	 * Returns 'true' or any custom validation errors.
	 *
	 * @param array $value
	 * @return true|string|array
	 */
	public function validate($value)
	{
		$errors = array();
		
		$defaults = $this->getLinkitValueDefaults();
	
		if(is_array($value))
		{
			// Merge With Defaults
			$value = array_merge($defaults, $value);

			// Validate Values
			switch($value['type'])
			{
				case('email'):
					if($value['email'] == '' || !preg_match('/^\S+@\S+\.\S+$/', $value['email']))
					{
						$errors[] = Craft::t('Please enter a valid email address.');
					}
					break;
	
				case('tel'):
					if($value['tel'] == '')
					{
						$errors[] = Craft::t('Please enter a valid telephone.');
					}
					break;
	
				case('custom'):
					if($value['custom'] == '')
					{
						$errors[] = Craft::t('Please enter a valid url.');
					}
					break;
	
				case('entry'):
					if($value['entry'] == '')
					{
						$errors[] = Craft::t('Please select an entry.');
					}
					break;
					
				case('asset'):
					if($value['asset'] == '')
					{
						$errors[] = Craft::t('Please select an asset.');
					}
					break;
			}		
		}

		if ($errors)
		{
			return $errors;
		}
		else
		{
			return true;
		}	
	}	
	
	
	

	/**
	 * Returns the HTML for the Target Locale setting.
	 *
	 * @access protected
	 * @return string|null
	 */
	protected function getTargetLocaleFieldHtml($elementType, $targetLocale, $elementName)
	{
		if (craft()->hasPackage(CraftPackage::Localize) && $elementType->isLocalized())
		{
			$localeOptions = array(
				array('label' => Craft::t('Same as source'), 'value' => null)
			);

			foreach (craft()->i18n->getSiteLocales() as $locale)
			{
				$localeOptions[] = array('label' => $locale->getName(), 'value' => $locale->getId());
			}

			return craft()->templates->renderMacro('_includes/forms', 'selectField', array(
				array(
					'label' => Craft::t('Target Locale'),
					'instructions' => Craft::t('Which locale do you want to select {type} in?', array('type' => StringHelper::toLowerCase($elementName))),
					'id' => 'targetLocale',
					'name' => 'targetLocale',
					'options' => $localeOptions,
					'value' => $targetLocale
				)
			));
		}
	}
	
	
	
	/**
	 * Returns sources avaible to an element type.
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function getElementSources($elementType)
	{
		$sources = array();

		foreach ($elementType->getSources() as $key => $source)
		{
			if (!isset($source['heading']))
			{
				$sources[] = array('label' => $source['label'], 'value' => $key);
			}
		}

		return $sources;
	}
	
	
	
	
	
	
}
