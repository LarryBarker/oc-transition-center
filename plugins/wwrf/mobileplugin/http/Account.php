<?php namespace Wwrf\MobilePlugin\Http;

use Backend\Classes\Controller;
use Wwrf\MobilePlugin\Models\Settings;
use Wwrf\MobilePlugin\Classes\ProviderManager;

/**
 * Account Back-end Controller
 */
class Account extends Controller
{
    public $implement = [
        'Wwrf.MobilePlugin.Behaviors.RestController'
    ];

    public $restConfig = 'config_rest.yaml';

    public function signin()
    {
        $loginProviderAlias = Settings::get('provider', 'default');
        $loginClass = ProviderManager::instance()->findByAlias($loginProviderAlias)->class;
        $loginManager = new $loginClass();
        return $loginManager -> signin();
    }

    public function register()
    {
        $loginProviderAlias = Settings::get('provider', 'default');
        $loginClass = ProviderManager::instance()->findByAlias($loginProviderAlias)->class;
        $loginManager = new $loginClass();
        return $loginManager -> register();
    }

}
