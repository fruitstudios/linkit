<?php
namespace Craft;

class FruitLinkItPlugin extends BasePlugin
{
    public function getName()
    {
        return 'Link It';
    }

    public function getVersion()
    {
        return '2.3.1';
    }

    public function getSchemaVersion()
    {
        return '2.3.0';
    }

    public function getDeveloper()
    {
        return 'Fruit Studios';
    }

    public function getDeveloperUrl()
    {
        return 'http://fruitstudios.co.uk';
    }

    public function getPluginHandle()
	{
		return StringHelper::toLowerCase($this->classHandle);
	}

    public function onAfterInstall()
    {
        $migrationClass = 'm160208_010101_FruitLinkIt_UpdateExistingLinkItFields';
        $migration = craft()->migrations->instantiateMigration($migrationClass, $this);
        if (!$migration->up())
        {
            FruitLinkItPlugin::log("Link It Upgrade Error. Could not run: " . $migrationClass, LogLevel::Error);
        }
    }

}
