<?php namespace Wwrf\TransitionCenter\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use RainLab\User\Models\User as Users;
use Flash;
use Lang;

/**
 * Staff Members Back-end Controller
 */
class StaffMembers extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = [
        'index' => 'config_list.yaml',
        'caseload' => 'config_caseload_list.yaml'
    ];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Wwrf.TransitionCenter', 'transitioncenter', 'staffmembers');

        $this->makeLists();
    }

    public function index()
    {
        $this->bodyClass = 'slim-container';
        $this->makeLists();
    }

    public function caseload() {
        
    }

    public function listExtendQuery($query, $definition = null)
    {
        if ($definition == 'index') {
            $query->whereHas('groups', function($q){
                $q->where('id', '=', '4');
            });
        }
        
        if ($definition == 'caseload') {
            $query->where('counselor_id', $this->params[0]);
        }
    }

    /**
     * Manually activate a user
     */
     public function preview_onActivate($recordId = null)
     {
         $model = $this->formFindModelObject($recordId);
 
         $model->attemptActivation($model->activation_code);
 
         Flash::success(Lang::get('rainlab.user::lang.users.activated_success'));
 
         if ($redirect = $this->makeRedirect('update-close', $model)) {
             return $redirect;
         }
     }
}
