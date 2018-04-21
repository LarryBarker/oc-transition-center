<?php namespace Mohsin\Mobile\Controllers;

use BackendMenu;
use ApplicationException;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use Mohsin\Mobile\Models\Platform;

/**
 * Platforms Back-end Controller
 */
class Platforms extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'platforms');
        SettingsManager::setContext('Mohsin.Mobile', 'platforms');
    }

    public function formBeforeSave($model)
    {
      $name = str_slug(post('Platform[name]'));
      if(Platform::isReserved($name))
        throw new ApplicationException(e(trans('mohsin.mobile::lang.platform.is_reserved', ['name' => Platform::getReservedPluginName($name)])));
    }
}
