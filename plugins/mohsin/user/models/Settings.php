<?php namespace Mohsin\User\Models;

use Model;
use Cms\Classes\Page;
use Mohsin\User\Classes\ProviderManager;
use RainLab\User\Models\Settings as UserSettings;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'mobile_user_settings';

    public $settingsFields = 'fields.yaml';

    public function initSettingsData()
    {
        $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
        if ($userActivation) {
            $this->getFieldConfig()->fields['activation_page']['cssClass'] = '';
        }
    }

    public function beforeFetch()
    {
        $this -> initSettingsData();
    }

    public function getProviderOptions()
    {
        $values = [];
        $providers = ProviderManager::instance()->listProviderObjects();
        foreach($providers as $key => $value)
          $values[$key] = $value->providerDetails()['name'];
        return $values;
    }

    public function getActivationPageOptions($keyValue = null)
    {
        return Page::sortBy('baseFileName')->lists('title', 'baseFileName');
    }

}
