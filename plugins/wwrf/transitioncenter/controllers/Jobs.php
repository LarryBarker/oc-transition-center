<?php namespace Wwrf\TransitionCenter\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Jobs Back-end Controller
 */
class Jobs extends Controller
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

        BackendMenu::setContext('Wwrf.TransitionCenter', 'transitioncenter', 'jobs');
    }
}
