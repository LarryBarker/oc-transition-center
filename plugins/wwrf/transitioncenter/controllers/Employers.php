<?php namespace Wwrf\TransitionCenter\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Lang;
use Flash;

/**
 * Employers Back-end Controller
 */
class Employers extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ImportExportController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $importExportConfig = 'config_import_export.yaml';
    
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Wwrf.TransitionCenter', 'transitioncenter', 'employers');
    }

    public function index()
    {
        $this->bodyClass = 'slim-container';
        $this->makeLists();
    }

    public function listExtendQuery($query)
    {
        $query->whereHas('groups', function($q){
            $q->where('id', '=', '3');
                //->where('status', '!=', 'unavailable');
        });
    }

    /**
     * Manually activate a user
     */
     public function onActivate($recordId = null)
     {
         $model = $this->formFindModelObject($recordId);
 
         $model->attemptActivation($model->activation_code);
 
         Flash::success(Lang::get('rainlab.user::lang.users.activated_success'));
 
         if ($redirect = $this->makeRedirect('update-close', $model)) {
             return $redirect;
         }
     }

}
