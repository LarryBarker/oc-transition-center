<?php namespace Mohsin\User\Http;

use Backend\Classes\Controller;
use Mohsin\User\Models\Settings;
use Mohsin\User\Classes\ProviderManager;

/**
 * Account Back-end Controller
 */
class Account extends Controller
{
    public $implement = [
        'Mohsin.Rest.Behaviors.RestController'
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
