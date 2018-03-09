<?php namespace Wwrf\TransitionCenter;

use Backend;
use System\Classes\PluginBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UsersController;
use RainLab\Blog\Models\Post as PostModel;
use RainLab\Blog\Controllers\Posts as PostsController;
use Clake\UserExtended\Models\UserExtended;
use Wwrf\TransitionCenter\Models\Questionnaire as QuestionnaireModel;
use RainLab\Blog\Models\Category as CategoryModel;
use Wwrf\TransitionCenter\Models\StaffMember as StaffModel;
use Wwrf\TransitionCenter\Classes\IconList;
use Cms\Models\ThemeData;
use Event;
use Lang;
use Model;
use DB;
use Cms\Classes\Page;
use Backend\Facades\BackendMenu;

/**
 * TransitionCenter Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'TransitionCenter',
            'description' => 'Transition Center employment, staffing, and job features.',
            'author'      => 'Wwrf',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        BackendMenu::registerContextSidenavPartial('Wwrf.TransitionCenter', 'transitioncenter', '~/plugins/wwrf/transitioncenter/partials/_sidebar.htm');        
    }

    public function registerReportWidgets()
    {
        return [
            'Wwrf\TransitionCenter\ReportWidgets\Surveys' => [
                'label'   => 'Questionnaires',
                'context' => 'dashboard'
            ],
            'Wwrf\TransitionCenter\ReportWidgets\Employment' => [
                'label'   => 'Employment',
                'context' => 'dashboard'
            ],
            'Wwrf\TransitionCenter\ReportWidgets\TransitionCenter' => [
                'label'   => 'Transition Center Statistics',
                'context' => 'transitioncenter'
            ]
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

        CategoryModel::extend(function($model) {
            $model->addDynamicMethod('scopeIsJobIndustry', function($query) {
                return $query->where('parent_id', 3)->get();
            });
        });

        UserModel::extend(function($model){
            $model->hasMany['viewedJobs'] = [
                'Wwrf\TransitionCenter\Models\ViewedJob',
                'key' => 'user_id',
                'order' => 'created_at desc'
            ];
            $model->hasMany['appliedJobs'] = [
                'Wwrf\TransitionCenter\Models\AppliedJob',
                'key' => 'user_id',
                'order' => 'created_at desc'
            ];
            $model->belongsTo['counselor'] = [
                'RainLab\User\Models\User',
                'order' => 'surname asc',
                'conditions' => 'is_counselor = 1'
            ];

            $model->addDynamicMethod('scopeListFrontEnd', function($query, $options = []){
                extract(array_merge([
                    'page' => 1,
                    'perPage' => 10,
                    'sort' => 'last_seen desc',
                    'industries' => null
                ], $options));
        
                $query = $query->whereHas('groups', function($q){
                    $q->where('id', '=', '2')->where('status','!=','unavailable');
                })->whereIsActivated(true)->orderBy('last_seen','desc');
        
                if($industries !== null){
                    if(!is_array($industries)){
                        $industries = [$industries];
                    };
                    foreach($industries as $industry){
                        $query->whereHas('industries', function($q) use ($industry){
                            $q->where('id', '=', $industry);
                        });
                    }
                }
        
                $lastPage = $query->paginate($perPage, $page)->lastPage();
        
                if($lastPage < $page){
                    $page = 1;
                }
                
                return $query->paginate($perPage, $page);
            });
        });

        UsersController::extend(function($controller){         
            // Splice in configuration safely
            $myConfigPath = '$/wwrf/transitioncenter/controllers/viewedjobs/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );

        });

        Event::listen('backend.list.extendQuery', function ($widget, $query) {
            // Test your model
            if ($widget->model instanceof \RainLab\User\Models\User) {
                $query->whereHas('groups', function($q){
                    $q->where('id', '=', '2');
                        //->where('status', '!=', 'unavailable');
                });
            }
        });

        UsersController::extendFormFields(function($form, $model, $context){
            
            if (!$model instanceof UserModel)
                return;
            
            if (!$model->exists)
                return;

            $form->addTabFields([
                'viewedJobs' => [
                    'tab' => 'Activity',
                    'type'  => 'partial',
                    'path' => '$/wwrf/transitioncenter/controllers/viewedjobs/_viewed_jobs.htm'
                ]
            ]);

            
        });

        // Listen for post columns and extend with a link to online application
        Event::listen('backend.list.extendColumns', function($widget) {
            // Only for the post controller
            if (!$widget->getController() instanceof PostsController) {
                return;
            }

            // Only for the post model
            if (!$widget->model instanceof PostModel) {
                return;
            }

            // Add an extra column for post link
            $widget->addColumns([
                'link' => [
                    'label'     => 'Link',
                    'sortable'  => false,
                    'clickable' => false,
                ]
            ]);
        });

        /*UsersController::extendListColumns(function($list, $model) {

            if (!$model instanceof UserModel)
                return;
    
            $list->addColumns([
                'counselor' => [
                    'label' => 'Counselor',
                    'relation' => 'counselor',
                    'select' => 'surname'
                ]
            ]);
    
        });*/

        // extend post list controller to add link button
        Event::listen('backend.list.overrideColumnValue', function($widget, $model, $column, $value) {
            // Only for the Post model
            if (!$model instanceof PostModel) {
                return;
            }

            if ($column->columnName == 'link' && $model->link) {
                $button = '<a role="button"';
                $button .= ' href="' . url($model->link) . '"';
                $button .= ' target="#"';
                $button .= ' class="btn btn-xs btn-warning"';
                $button .= '>' . 'Link ' . '<i class="icon icon-external-link"></i></a>';

                return $button;
            }
        });

        UserModel::extend(function($model) {
            $model->belongsToMany['company'] = ['Wwrf\TransitionCenter\Models\Company', 'table' => 'wwrf_users_companies', 'key' => 'user_id'];
            $model->hasMany['jobs'] = ['Wwrf\TransitionCenter\Models\Job', 'table' => 'wwrf_users_jobs', 'key' => 'user_id'];
        });

        UsersController::extend(function($controller){         
            // Implement behavior if not already implemented
            if (!$controller->isClassExtendedWith('Backend.Behaviors.RelationController')) {
                $controller->implement[] = 'Backend.Behaviors.RelationController';
            }

            // Define property if not already defined
            if (!isset($controller->relationConfig)) {
                $controller->addDynamicProperty('relationConfig');
            }

            // Splice in configuration safely
            $myConfigPath = '$/wwrf/transitioncenter/controllers/jobs/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );
        });
        
        // Extend all backend form usage
        Event::listen('backend.form.extendFields', function($widget) {
            
            // Only for the User controller
            if (!$widget->getController() instanceof UsersController) {
                return;
            }

            // Only for the User model
            if (!$widget->model instanceof UserModel) {
                return;
            }
            
            // Add an job relation field
            $widget->addTabFields([
                'jobs' => [
                    'label'   => 'Job(s)',
                    'commentAbove' => "Add or update a user's job(s)...",
                    'type'    => 'partial',
                    'path' => '$/wwrf/transitioncenter/controllers/jobs/_jobs.htm',
                    'tab' => 'Employment',
                ]
            ]);
        });

        UserModel::extend(function($model){
            $model->hasMany['usersprograms'] = [
                'Wwrf\TransitionCenter\Models\UserProgram',
                'table' => 'wwrf_programtracker_users_programs',
                ];
        });

        UsersController::extendFormFields(function($form, $model, $context){

            if (!$model instanceof UserModel)
                return;
            
            if (!$model->exists)
                return;

            $form->addTabFields([
                'usersprograms' => [
                    //'label' => 'Programs',
                    'tab' => 'Programs',
                    'type'  => 'partial',
                    'path'  => '$/wwrf/transitioncenter/controllers/usersprograms/_users_programs.htm',
                ]
            ]);

        });

        UsersController::extend(function($controller){         
            // Splice in configuration safely
            $myConfigPath = '$/wwrf/transitioncenter/controllers/usersprograms/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );
        });

        UserModel::extend(function($model){
            $model->hasOne['questionnaire'] = [
                'Wwrf\TransitionCenter\Models\Questionnaire'
            ];
        });

        UsersController::extendFormFields(function($form, $model, $context){

            if (!$model instanceof UserModel)
                return;
            
            if (!$model->exists)
                return;

            QuestionnaireModel::getFromUser($model);

            $form->addSecondaryTabFields([
                'questionnaire[updated_at]' => [
                    'label' => 'Questionnaire Updated:',
                    'type'  => 'datepicker',
                    'mode'  => 'date',
                    'disabled' => 'true'
                ]
            ]);

        });

        UsersController::extend(function($controller){         
            // Splice in questionnaire configuration safely
            $myConfigPath = '$/wwrf/transitioncenter/controllers/questionnaires/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );
        });

        // Extend all backend form usage
        Event::listen('backend.form.extendFields', function($widget) {
            
            // Only for the User controller
            if (!$widget->getController() instanceof UsersController) {
                return;
            }

            // Only for the User model
            if (!$widget->model instanceof UserModel) {
                return;
            }
            
            // Add an questionnaire relation field
            $widget->addTabFields([
                'surveys' => [
                    'label'   => 'Questionnaire',
                    'type'    => 'partial',
                    'path' => '$/wwrf/transitioncenter/controllers/questionnaires/_questionnaire.htm',
                    'tab' => 'Surveys',
                ]
            ]);

            // Add an questionnaire relation field
            $widget->addFields([
                'counselor' => [
                    'label'   => 'Counselor',
                    'type'    => 'relation',
                    'nameFrom' => 'surname',
                    'descriptionFrom' => 'surname',
                    'placeholder' => '-- Select a counselor --'
                ]
            ]);
        });

        ThemeData::extend(function($model) {
            
            $model->addDynamicMethod('getLinkOptions', function($value) use ($model)
            {
                return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
            });

            $model->addDynamicMethod('listIcons', function($value) use ($model) {
                return IconList::getList();
            });

            
        });
    }

    public function registerListColumnTypes()
    {
        return [
            // A local method
            'applied' => [$this, 'appliedJobColumn']
        ];
    }

    public function appliedJobColumn($value, $column, $record)
    {
        
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {

        return [
            'Wwrf\TransitionCenter\Components\JobTracker' => 'JobTracker',
            'Wwrf\TransitionCenter\Components\Questionnaire' => 'Questionnaire'
        ];
    }
}
