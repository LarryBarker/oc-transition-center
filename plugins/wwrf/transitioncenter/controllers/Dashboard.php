<?php namespace Wwrf\TransitionCenter\Controllers;

use DB;

use Lang;

use Flash; 

use Redirect;

use BackendAuth;

use BackendMenu;

use Backend\Classes\Controller;

use Backend\Widgets\ReportContainer;

use RainLab\User\Models\User;

use Wwrf\TransitionCenter\ReportWidgets\TransitionCenter;



/**

 * Transition Center Dashboard controller

 *

 * @package wwrf\transitioncenter

 * @author Larry Barker

 *

 */

class Dashboard extends Controller

{

    use \Backend\Traits\InspectableContainer;

    public $implement = [
        'Backend.Behaviors.ListController'
    ];

    public $listConfig = [
        'releases' => 'config_list.yaml',
        'unemployed' => 'config_unemployed_list.yaml',
        'topJobsList' => 'config_top_jobs_list.yaml',
        'topAppliedJobsList' => 'config_top_applied_jobs_list.yaml'
    ];

    public $bodyClass = 'compact-container';


    /**

     * @see checkPermissionRedirect()

     */

    public $requiredPermissions = [];



    public function __construct()

    {

        parent::__construct();

        BackendMenu::setContext('Wwrf.TransitionCenter', 'transitioncenter', 'sideboard');

        $this->addCss('/modules/backend/assets/css/dashboard/dashboard.css', 'core');

        $this->makeLists();

    }



    public function index()

    {

        if ($redirect = $this->checkPermissionRedirect()) {

            return $redirect;

        }

        $this->addJs('/plugins/rainlab/user/assets/js/bulk-actions.js');
        
        //$this->asExtension('ListController')->index();

        $this->initReportContainer();

        $this->pageTitle = 'Transiton Center Dashboard';

    }



    public function listExtendQueryBefore($query, $definition = null) {
        if ($definition == 'releases') {
            $query->whereYear('release_date', '=', date('Y'))->whereMonth('release_date','=', date('m'));
        }

        if ($definition == 'topJobsList') {
            $query->withCount('trackers')               // Count the trackers
                  ->orderBy('trackers_count', 'desc')   // Order by the tracker count
                  ->limit(10);                          // Take the first 10
        }

        if ($definition == 'topAppliedJobsList') {
            $query->whereHas('trackers', function($q) {
                $q->where('applied_on', '!=', NULL);
            })->withCount('trackers')->orderBy('trackers_count', 'desc')->limit(10);
        }

        if ($definition == 'unemployed') {
            $query->withoutTrashed()->isOffender()->isUnemployed();
        }
    }



    public function index_onInitReportContainer()

    {

        $this->initReportContainer();

        return ['#dashReportContainer' => $this->widget->transitionCenterDashboard->render()];

    }



    /**

     * Prepare the report widget used by the dashboard

     * @param Model $model

     * @return void

     */

    protected function initReportContainer()

    {

        //new ReportContainer($this, 'config_dashboard.yaml');

        $transitionCenterDashboard = new ReportContainer($this, ['context' => 'transitioncenter']);
        //$transitionCenterDashboard->canAddAndDelete = false;
        $transitionCenterDashboard->alias = 'transitionCenterDashboard';
        $transitionCenterDashboard->bindToController();
    }



    /**

     * Custom permissions check that will redirect to the next

     * available menu item, if permission to this page is denied.

     */

    protected function checkPermissionRedirect()

    {

        if (!$this->user->hasAccess('backend.access_dashboard')) {

            $true = function () { return true; };

            if ($first = array_first(BackendMenu::listMainMenuItems(), $true)) {

                return Redirect::intended($first->url);

            }

        }

    }

    /**
     * Perform bulk action on selected users
     */
    public function index_onBulkAction()
    {
        if (
            ($bulkAction = post('action')) &&
            ($checkedIds = post('checked')) &&
            is_array($checkedIds) &&
            count($checkedIds)
        ) {

            foreach ($checkedIds as $userId) {
                if (!$user = User::withTrashed()->find($userId)) {
                    continue;
                }

                switch ($bulkAction) {
                    case 'delete':
                        $user->forceDelete();
                        break;

                    case 'deactivate':
                        $user->delete();
                        break;

                    case 'restore':
                        $user->restore();
                        break;

                    case 'ban':
                        $user->ban();
                        break;

                    case 'unban':
                        $user->unban();
                        break;
                }
            }

            Flash::success(Lang::get('rainlab.user::lang.users.'.$bulkAction.'_selected_success'));
        }
        else {
            Flash::error(Lang::get('rainlab.user::lang.users.'.$bulkAction.'_selected_empty'));
        }

        return $this->listRefresh();
    }
}



