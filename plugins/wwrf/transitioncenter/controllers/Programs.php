<?php namespace Wwrf\TransitionCenter\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Programs extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'wwrf.transitioncenter.*' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Wwrf.TransitionCenter', 'transitioncenter', 'programs');
    }
}
