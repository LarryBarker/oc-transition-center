<?php namespace Wwrf\TransitionCenter;

use Backend;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
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
use Wwrf\TransitionCenter\Models\TempService;

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
        // Add scope for to category model to list as job industry
        CategoryModel::extend(function($model) {
            $model->addDynamicMethod('scopeIsJobIndustry', function($query) {
                return $query->where('parent_id', 3)->get();
            });
        });

        // extend user model relations
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

            $model->belongsToMany['company'] = [
                'Wwrf\TransitionCenter\Models\Company',
                'table' => 'wwrf_users_companies',
                'key' => 'user_id'];

            $model->hasMany['jobs'] = [
                'Wwrf\TransitionCenter\Models\Job', 
                'table' => 'wwrf_users_jobs', 
                'key' => 'user_id', 
                'delete' => true
            ];

            $model->hasMany['usersprograms'] = [
                'Wwrf\TransitionCenter\Models\UserProgram',
                'table' => 'wwrf_programtracker_users_programs',
            ];

            $model->hasOne['questionnaire'] = [
                'Wwrf\TransitionCenter\Models\Questionnaire'
            ];

            // add scopes to user model
            $model->addDynamicMethod('scopeListFrontEnd', function($query, $options = []){
                extract(array_merge([
                    'page' => 1,
                    'perPage' => 10,
                    'sort' => 'last_seen desc',
                    'industries' => null,
                    'keywords' => null
                ], $options));

                $query = $query->whereHas('groups', function($q){
                    $q->where('id', '=', '2')->where('status','!=','unavailable');
                });
        
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

                /*if($keywords !== null){
                    $query->orWhere('profile_headline', 'LIKE', "%{$keywords}%")
                    ->orWhere('profile_summary', 'LIKE', "%{$keywords}%")
                    ->orWhere('name', 'LIKE', "%{$keywords}%")
                    ->orWhere('surname', 'LIKE', "%{$keywords}%");
                }*/

                $lastPage = $query->paginate($perPage, $page)->lastPage();
        
                if($lastPage < $page){
                    $page = 1;
                }
                
                return $query->whereIsActivated(true)->orderBy('created_at','desc')->orderBy('last_seen','desc')->orderBy('status','asc')->paginate($perPage, $page);
            });

            $model->addDynamicMethod('scopeIsUnemployed', function($query) {
                return $query->where('status', '=', 'available')
                             ->whereDate('eligible_date', '<', date('Y-m-d').' 00:00:00')
                             ->has('jobs', '<', 1)
                             ->orWhere('is_unemployed', 1);
            });

            $model->addDynamicMethod('scopeIsOffender', function($query) {
                return $query->whereHas('groups', function($q){
                    $q->where('id', '=', '2');
                })->withTrashed();
            });
        });

        // methods to extend user controller with relation controllers
        UsersController::extend(function($controller){         
            // Implement behavior if not already implemented
            if (!$controller->isClassExtendedWith('Backend.Behaviors.RelationController')) {
                $controller->implement[] = 'Backend.Behaviors.RelationController';
            }

            // Define property if not already defined
            if (!isset($controller->relationConfig)) {
                $controller->addDynamicProperty('relationConfig');
            }

            // Safely splice in config for jobs relation
            $myConfigPath = '$/wwrf/transitioncenter/controllers/jobs/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );
            
            // And do the same for the user's viewed jobs
            $myConfigPath = '$/wwrf/transitioncenter/controllers/viewedjobs/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );

            // And again for their programs
            $myConfigPath = '$/wwrf/transitioncenter/controllers/usersprograms/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );

            // Once more for the user's questionnaire relation field
            $myConfigPath = '$/wwrf/transitioncenter/controllers/questionnaires/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );

            // splice in user job activity relation config
            $activityTrackerConfig = '$/wwrf/transitioncenter/controllers/trackers/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $activityTrackerConfig
            );

        });

        UsersController::extendFormFields(function($form, $model, $context){
            
            if (!$model instanceof UserModel)
                return;
            
            if (!$model->exists)
                return;

            QuestionnaireModel::getFromUser($model);

            // add tab fields for activity and programs tracking
            $form->addTabFields([
                'trackers' => [
                    'tab' => 'Activity',
                    'type'  => 'partial',
                    'path' => '$/wwrf/transitioncenter/controllers/trackers/_trackers.htm'
                ],
                'usersprograms' => [
                    'tab' => 'Programs',
                    'type'  => 'partial',
                    'path'  => '$/wwrf/transitioncenter/controllers/usersprograms/_users_programs.htm',
                ]
            ]);

            $form->addTabFields([
                'questionnaire[updated_at]' => [
                    'tab' => 'Surveys',
                    'label' => 'Questionnaire Updated:',
                    'type'  => 'datepicker',
                    'mode'  => 'date',
                    'disabled' => 'true',
                    'span' => 'right'
                ],
                'user_agreement' => [
                    'tab' => 'Surveys',
                    'label' => 'User Agreement signed:',
                    'type'  => 'datepicker',
                    'mode'  => 'date',
                    'span' => 'left'
                ],
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

            // Override post author column if posted by front end user
            if ($column->columnName == 'author' && $model->author_id) {
                $author = UserModel::where('id', '=', $model->author_id)->first(); // Find employer with frontend author id
                $authorCompany = $author->company_name; // Set company name to author company name
                return $authorCompany; // Return company name for column value
            }
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
            
            // Add tab fields for surveys and employment
            $widget->addTabFields([
                'surveys' => [
                    'label'   => 'Questionnaire',
                    'type'    => 'partial',
                    'path' => '$/wwrf/transitioncenter/controllers/questionnaires/_questionnaire.htm',
                    'tab' => 'Surveys',
                ],
                'jobs' => [
                    'label'   => 'Job(s)',
                    'commentAbove' => "Add or update a user's job(s)...",
                    'type'    => 'partial',
                    'path' => '$/wwrf/transitioncenter/controllers/jobs/_jobs.htm',
                    'tab' => 'Employment',
                ]
            ]);

        });

        // Used to add an icon list to the Theme customization page
        ThemeData::extend(function($model) {
            
            $model->addDynamicMethod('getLinkOptions', function($value) use ($model)
            {
                return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
            });

            $model->addDynamicMethod('listIcons', function($value) use ($model) {
                return IconList::getList();
            });

        });

        PostModel::extend(function($model) {
            $model->addFillable([
                'is_featured',
                'apply_online',
                'email_resume',
                'in_person'
            ]);

            $model->addDynamicMethod('scopeIsFeatured', function($query) {
                return $query->where('is_featured', 1)->get();
            });

            $model->bindEvent('model.beforeValidate', function() use ($model) {
                $model->rules['email'] = 'email';
            });
        });

        // Extend User model with behavior
        UserModel::extend(function($model) {
            // Implement behavior if not already implemented
            if (!$model->isClassExtendedWith('Wwrf.TransitionCenter.Behaviors.Trackability')) {
                $model->implement[] = 'Wwrf.TransitionCenter.Behaviors.Trackability';
            }
        });
        
        // Check for RainLab Blog plugin
        if(PluginManager::instance()->exists('RainLab.Blog')) {
            // Extend Post model with behavior
            PostModel::extend(function($model) {
                // Implement behavior if not already implemented
                if (!$model->isClassExtendedWith('Wwrf.TransitionCenter.Behaviors.Trackable')) {
                    $model->implement[] = 'Wwrf.TransitionCenter.Behaviors.Trackable';
                }
            });
        } 
        
        \Event::listen('offline.sitesearch.query', function ($query) {

            // Search your plugin's contents
            $items = TempService::where('name', 'like', "%${query}%")->get();
    
            // Now build a results array
            $results = $items->map(function ($item) use ($query) {
    
                // If the query is found in the title, set a relevance of 2
                $relevance = mb_stripos($item->name, $query) !== false ? 2 : 1;
                
                // Optional: Add an age penalty to older results. This makes sure that
                // never results are listed first.
                // if ($relevance > 1 && $item->published_at) {
                //     $relevance -= $this->getAgePenalty($item->published_at->diffInDays(Carbon::now()));
                // }
    
                return [
                    'title'     => $item->name,
                    'text'      => $item->website,
                    'url'       => '/jobs/temp/'.$item->slug,
                    'relevance' => $relevance, // higher relevance results in a higher
                                               // position in the results listing
                    // 'meta' => 'data',       // optional, any other information you want
                                               // to associate with this result
                    // 'model' => $item,       // optional, pass along the original model
                ];
            });
    
            return [
                'provider' => 'Temp Service', // The badge to display for this result
                'results'  => $results,
            ];
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
            'Wwrf\TransitionCenter\Components\Questionnaire' => 'Questionnaire',
            'Wwrf\TransitionCenter\Components\FeaturedUserPosts' => 'FeaturedUserPosts'
        ];
    }
}
