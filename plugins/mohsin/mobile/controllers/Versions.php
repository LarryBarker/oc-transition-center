<?php namespace Mohsin\Mobile\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Versions Back-end Controller
 */
class Versions extends Controller
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
        BackendMenu::setContext('Mohsin.Mobile', 'mobile', 'versions');
    }
}
