<?php namespace Wwrf\TransitionCenter\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Programs extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController','Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'wwrf.transitioncenter.*' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Wwrf.TransitionCenter', 'transitioncenter', 'programs');
    }
}
