<?php

/**
 * Link It by Fruit Studios
 *
 * @package   Link It
 * @author    Sam Hibberd
 * @copyright Copyright (c) 2015, Fruit Studios
 * @link      http://fruitstudios.co.uk
 * @license   http://fruitstudios.co.uk
 */

namespace Craft;

class FruitLinkIt_LinkModel extends BaseModel
{
    private $_entry;
    private $_asset;
    private $_category;

    protected function defineAttributes()
    {
        return array(
            'type' => array(AttributeType::String, 'default' => false),
            'value' => array(AttributeType::String, 'default' => false),
            'defaultText' => array(AttributeType::String, 'default' => false),
            'customText' => array(AttributeType::String, 'default' => false),
            'target' => array(AttributeType::String, 'default' => false),
        );
    }

    public function __toString()
    {
        $htmlLink = $this->getHtmlLink();
        return $htmlLink ? (string) $htmlLink : '';
    }

    public function getHtmlLink($attributes = false)
    {
        $url = $this->getUrl();
        $text = $this->getText();
        if($url && $text)
        {
            // Open  Link
            $htmlLink = '<a href="'.$url.'"';

            // Add Title (if not in attributes)
            if(!is_array($attributes) || !array_key_exists('title', $attributes))
            {
                $htmlLink .= ' title="'.$text.'"';
            }
            // Add Target (if not in attributes)
            if( ( !is_array($attributes) || !array_key_exists('title', $attributes) ) && $this->target )
            {
                $htmlLink .= ' target="'.$this->target.'"';
            }

            // Add Attributes
            if(is_array($attributes))
            {
                foreach ($attributes as $attr => $value)
                {
                    $htmlLink .= ' '.$attr.'="'.$value.'"';
                }
            }

            // Close Up Link
            $htmlLink .= '>'.$text.'</a>';

            // Get Raw
            return TemplateHelper::getRaw($htmlLink);
        }
        return false;
    }


    public function getUrl()
    {
        $url = false;
        switch ($this->type)
        {
            case('entry'):
                $entry = $this->_entry ? $this->_entry : $this->getEntry();
                if($entry)
                {
                    $url = $entry->status == 'live' ? $entry->getUrl() : false;
                }
                break;
            case('asset'):
                $asset = $this->_asset ? $this->_asset : $this->getAsset();
                if($asset)
                {
                    $url = $asset->getUrl();
                }
                break;
            case('category'):
                $category = $this->_category ? $this->_category : $this->getCategory();
                if($category)
                {
                    $url = $category->enabled ? $category->getUrl() : false;
                }
                break;
            case('custom'):
                $url = $this->value;
                break;
            case('tel'):
                $url = 'tel:'.str_replace(' ', '', $this->value);
                break;
            case('email'):
                $url = 'mailto:'.$this->value;
                break;
        }
        return $url;
    }


    public function getText()
    {
        if($this->customText)
        {
            return $this->customText;
        }

        if($this->defaultText)
        {
            return $this->defaultText;
        }

        $text = '';
        switch ($this->type)
        {
            case('entry'):
                $entry = $this->_entry ? $this->_entry : $this->getEntry();
                if($entry)
                {
                    $text = $entry->title;
                }
                break;
            case('asset'):
                $asset = $this->_asset ? $this->_asset : $this->getAsset();
                if($asset)
                {
                    $text = $asset->title;
                }
                break;
            case('category'):
                $category = $this->_category ? $this->_category : $this->getCategory();
                if($category)
                {
                    $text = $category->title;
                }
                break;
            default:
                $text = $this->value;
                break;

        }
        return $text;
    }


    public function getElement()
    {

        switch ($this->type)
        {
            case('entry'):
                $element = $this->entry;
                break;
            case('asset'):
                $element = $this->asset;
                break;
            case('category'):
                $element = $this->category;
                break;
            default:
                $element = false;
        }
        return $element;
    }


    public function getEntry()
    {
        if($this->type != 'entry')
        {
            return false;
        }

        if(!$this->_entry)
        {
            $id = is_array($this->value) ? $this->value[0] : false;
            if( $id && $entry = craft()->entries->getEntryById($id) )
            {

                $this->_entry = $entry;
            }
        }
        return $this->_entry;
    }


    public function getAsset()
    {
        if($this->type != 'asset')
        {
            return false;
        }

        if(!$this->_asset)
        {
            $id = is_array($this->value) ? $this->value[0] : false;
            if( $id && $asset = craft()->assets->getFileById($id) )
            {
                $this->_asset = $asset;
            }
        }
        return $this->_asset;
    }


    public function getCategory()
    {
        if($this->type != 'category')
        {
            return false;
        }

        if(!$this->_category)
        {
            $id = is_array($this->value) ? $this->value[0] : false;
            if( $id && $category = craft()->categories->getCategoryById($id) )
            {
                $this->_category = $category;
            }
        }
        return $this->_category;
    }

    public function validate($attributes = null, $clearErrors = true)
    {
        switch($this->type)
        {
            case('email'):
                if( !filter_var($this->value, FILTER_VALIDATE_EMAIL) )
                {
                    $this->addError('value', Craft::t('Please enter a valid email address.'));
                }
                break;

            case('tel'):
                $regexp = '/^[0-9+\(\)#\.\s\/ext-]+$/';
                if(!filter_var($this->value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regexp))))
                {
                    $this->addError('value', Craft::t('Please enter a valid telephone.'));
                }
                break;

            case('custom'):
                if(!filter_var($this->value, FILTER_VALIDATE_URL) && $this->value == '#')
                {
                    $this->addError('value', Craft::t('Please enter a valid url.'));
                }
                break;

            case('entry'):
                if($this->value == '')
                {
                    $this->addError('value', Craft::t('Please select an entry.'));
                }
                break;

            case('asset'):
                if($this->value == '')
                {
                    $this->addError('value', Craft::t('Please select an asset.'));
                }
                break;

            case('category'):
                if($this->value == '')
                {
                    $this->addError('value', Craft::t('Please select a category.'));
                }
                break;
        }

        return !$this->hasErrors();
    }



    // Deprecated: Pre Link It 2.0
    public function getLinkText()
    {
        craft()->deprecator->log('FruitLinkIt', '{{ linkItField.linkText }} has been deprecated. Use {{ linkItField.text }} instead.');
        return $this->getText();
    }

    public function getLink()
    {
        craft()->deprecator->log('FruitLinkIt', '{{ linkItField.link }} has been deprecated. Use {{ linkItField }} or {{ linkItField.htmlLink }} instead.');
        return $this->getHtmlLink();
    }

    public function getEmail()
    {
        craft()->deprecator->log('FruitLinkIt', '{{ linkItField.email }} has been deprecated. Use {{ linkItField.url }} instead.');
        return $this->getUrl();
    }

    public function getCustom()
    {
        craft()->deprecator->log('FruitLinkIt', '{{ linkItField.custom }} has been deprecated. Use {{ linkItField.url }} instead.');
        return $this->getUrl();
    }

    public function getTel()
    {
        craft()->deprecator->log('FruitLinkIt', '{{ linkItField.tel }} has been deprecated. Use {{ linkItField.url }} instead.');
        return $this->getUrl();
    }

}
