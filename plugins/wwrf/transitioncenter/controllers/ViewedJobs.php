<?php namespace Wwrf\TransitionCenter\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class ViewedJobs extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];
    
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Wwrf.TransitionCenter', 'transitioncenter');
    }
}