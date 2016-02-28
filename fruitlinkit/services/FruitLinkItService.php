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

class FruitLinkItService extends BaseApplicationComponent
{
    protected $plugin;
    protected $pluginHandle;

    public function __construct()
    {
        $this->plugin = craft()->plugins->getPlugin('fruitlinkit');
        $this->pluginHandle = $this->plugin->getPluginHandle();
    }

    public function getLinkItElementSources()
    {
        return array(
            'entry' => $this->_getElementSourcesWithUrls(ElementType::Entry),
            'asset' => $this->_getElementSourcesWithUrls(ElementType::Asset),
            'category' => $this->_getElementSourcesWithUrls(ElementType::Category),
        );
    }

    private function _getElementSourcesWithUrls($type)
    {
        $elementType = craft()->elements->getElementType($type);
        $sources = array();

        foreach ($elementType->getSources() as $key => $source)
        {
            if (!isset($source['heading']))
            {
                $sources[] = array(
                    'label' => $source['label'],
                    'value' => $key
                );
            }
        }
        return $sources;
    }

}
