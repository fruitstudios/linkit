<?php
namespace Craft;

class m160208_010101_FruitLinkIt_UpdateExistingLinkItFields extends BaseMigration
{
    public function safeUp()
    {
        $plugin = craft()->plugins->getPlugin('fruitlinkit');

        // Firstly lets check if we have any existing Link It fields
        $query = craft()->db->createCommand()
            ->select('*')
            ->from('fields')
            ->where('type = :fruitlinkit OR type = :linkit', array(
                'fruitlinkit' => 'FruitLinkIt_LinkIt',
                'linkit' => 'Linkit_Linkit'
            ))
            ->queryAll();

        // Now lets update any existing Link It fields to the new settings structure
        $fields = FieldModel::populateModels($query);
        if($fields)
        {
            // Build new default types string
            $sources = craft()->fruitLinkIt->getLinkItElementSources();
            $defaultTypes = array('email','tel','custom');
            if($sources['entry'])
            {
                array_push($defaultTypes, 'entry');
            }
            if($sources['category'])
            {
                array_push($defaultTypes, 'category');
            }
            if($sources['asset'])
            {
                array_push($defaultTypes, 'asset');
            }

            $defaultSettings = array(
                'types' => $defaultTypes,
                'defaultText' => '',
                'allowCustomText' => false,
                'allowTarget' => false,
                'entrySources' => '*',
                'entrySelectionLabel' => 'Select an entry',
                'assetSources' => '*',
                'assetSelectionLabel' => 'Select an asset',
                'categorySources' => '*',
                'categorySelectionLabel' => 'Select a category'
            );

            foreach($fields as $field)
            {
                $existingSettings = $field->settings;
                $settings = $defaultSettings;

                // Same: Sources
                $settings['entrySources'] = $existingSettings['entrySources'];
                $settings['assetSources'] = $existingSettings['assetSources'];

                // Changed: Types
                if($existingSettings['types'] != '*')
                {
                    $settings['types'] = $existingSettings['types'];
                }

                // Changed: Allow Target
                if(array_key_exists('allowTarget', $existingSettings))
                {
                    $settings['allowTarget'] = $existingSettings['allowTarget'];
                }
                elseif(array_key_exists('target', $existingSettings))
                {
                    $settings['allowTarget'] = $existingSettings['target'];
                }

                // Changed: Default Text
                if(array_key_exists('defaultText', $existingSettings))
                {
                    $settings['defaultText'] = $existingSettings['defaultText'];
                }

                // Changed: Allow Custom Text
                if(array_key_exists('allowCustomText', $existingSettings))
                {
                    $settings['allowCustomText'] = $existingSettings['allowCustomText'];
                }
                elseif(array_key_exists('text', $existingSettings))
                {
                    $settings['allowCustomText'] = $existingSettings['text'];
                }

                // Changed: Category Sources
                if(array_key_exists('categorySources', $existingSettings))
                {
                    $settings['categorySources'] = $existingSettings['categorySources'];
                }

                // Changed: Entry Selection Label
                if(array_key_exists('entrySelectionLabel', $existingSettings))
                {
                    $settings['entrySelectionLabel'] = $existingSettings['entrySelectionLabel'];
                }

                // Changed: Category Selection Label
                if(array_key_exists('categorySelectionLabel', $existingSettings))
                {
                    $settings['categorySelectionLabel'] = $existingSettings['categorySelectionLabel'];
                }

                // Changed: Asset Selection Label
                if(array_key_exists('assetSelectionLabel', $existingSettings))
                {
                    $settings['assetSelectionLabel'] = $existingSettings['assetSelectionLabel'];
                }

                // Update
                $data = array('settings' => JsonHelper::encode($settings));
                if(craft()->db->createCommand()->update('fields', $data, 'id = :id', array(':id' => $field->id)))
                {
                    FruitLinkItPlugin::log('Updated field to latest schema: '.$field->id);
                }
                else
                {
                    FruitLinkItPlugin::log('Could not update field id: '.$field->id, LogLevel::Error);
                }
            }
            // Uupdate any old version fields fieldtype
            craft()->db->createCommand()->update('fields', ['type' => 'FruitLinkIt'], 'type=:fieldType', [':fieldType' => 'FruitLinkIt_LinkIt']);
            craft()->db->createCommand()->update('fields', ['type' => 'FruitLinkIt'], 'type=:fieldType', [':fieldType' => 'Linkit_Linkit']);

        }

        // Now delete the old version
        craft()->db->createCommand()->delete('plugins', "class = 'Linkit'");

        return true;
    }
}
