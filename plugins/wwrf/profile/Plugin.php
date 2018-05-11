<?php namespace WWRF\Profile;

use System\Classes\PluginBase;
use RainLab\User\Controllers\Users as UsersController;
use RainLab\User\Models\User as UserModel;
use Wwrf\Surveys\Models\Questionnaire as QuestionnaireModel;
use RainLab\Blog\Models\Category as CategoryModel;
use Lang;
use Model;
use DB;
use Event;
use Backend;

class Plugin extends PluginBase
{
    public $require = ['RainLab.User'];

    public function pluginDetails()
    {
        return [
            'name'        => 'wwrf.profile::lang.plugin.name',
            'description' => 'wwrf.profile::lang.plugin.description',
            'author'      => 'wwrf.profile::lang.plugin.author',
            'icon'        => 'icon-file-text',
            'homepage'    => 'https://www.wwrfresource.com'
        ];
    }

    public function registerReportWidgets()
    {
        return [];
    }

    public function registerComponents()
    {
        return [
            'Wwrf\Profile\Components\Modal' => 'Modal',
            'Wwrf\Profile\Components\UserAgreement' => 'UserAgreement',
            'Wwrf\Profile\Components\JobCategories' => 'jobCategories'
        ];
    }

    public function boot()
    {
        UserModel::extend(function($model) {
            $model->addFillable([
                'kdoc_number',
                'status',
                'profile_phone',
                'profile_headline',
                'profile_summary',
                'profile_skills',
                'company_name',
                'company_title',
                'company_phone',
                'user_agreement',
                'questionnaire',
            ]);

            $model->belongsToMany['industries'] = ['RainLab\Blog\Models\Category', 'table' => 'wwrf_users_industries', 'key' => 'user_id', 'otherKey' => 'industry_id'];

            $model->attachMany['documents'] = [
                \System\Models\File::class
            ];

            $model->bindEvent('model.beforeValidate', function() use ($model) {
                $model->rules['industries'] = 'max:4';
            });

            $model->addDynamicMethod('getYearOptions', function($value) use ($model)
            {

                    $query = Users::all();
            
                    $years = [];
            
                    foreach($query as $user) {
                        $year = date('Y', strtotime($user->created_at));
            
                        $years[$year] = $year;
                    }
            
                    $years = array_unique($years);
            
                    return $years;

            });

        });

        CategoryModel::extend(function($model) {
            $model->belongsToMany['users'] = ['RainLab\User\Models\User', 'table' => 'wwrf_users_industries', 'key' => 'user_id'];
        });
      
        UsersController::extendFormFields(function($form, $model, $context) {
            if (!$model instanceof UserModel) {
                return;
            }

            $form->removeField('comment');

            $form->removeField('avatar');

            $form->addSecondaryTabFields([
                'avatar' => [
                    'label' => 'Resume',
                    'type' => 'fileupload',
                    'mode' => 'file'
                ],
                'documents' => [
                    'label' => 'Portfolio Documents',
                    'type' => 'fileupload',
                    'mode' => 'file',
                    'useCaption' => 'true'
                ],
                'user_agreement' => [
                    'label' => 'User Agreement signed:',
                    'type'  => 'datepicker',
                    'mode'  => 'date',
                    //'disabled' => 'true'
                ],
                'profile_comment' => [
                    'label' => 'wwrf.profile::lang.comment',
                    'type'  => 'textarea',
                    'size'  => 'small'
                ]
            ]);

            $form->addTabFields([
                'kdoc_number' => [
                    'label' => 'wwrf.profile::lang.profile.kdoc_number',
                    'tab'     => 'wwrf.profile::lang.profile.tab',
                    'span'  => 'left'
                ],
                'profile_phone' => [
                    'label' => 'wwrf.profile::lang.profile.phone',
                    'tab'   => 'wwrf.profile::lang.profile.tab',
                    'default' => '(316) 265-5211 ext. 208',
                    'span'  => 'right'
                ],
                'arrival_date' => [
                    'label' => 'Arrival Date',
                    'tab' => 'wwrf.profile::lang.profile.tab',
                    'span' => 'auto',
                    'type' => 'datepicker',
                    'mode' => 'date'
                ],
                'release_date' => [
                    'label' => 'Release Date',
                    'tab' => 'wwrf.profile::lang.profile.tab',
                    'span' => 'auto',
                    'type' => 'datepicker',
                    'mode' => 'date',
                    'default' => NULL,
                ],
                'eligible_date' => [
                    'label' => 'Eligible Date',
                    'tab' => 'Employment',
                    'span' => 'auto',
                    'type' => 'datepicker',
                    'mode' => 'date',
                ],
                'status' => [
                    'label'   => 'wwrf.profile::lang.profile.status',
                    'tab'     => 'Employment',
                    'type'    => 'dropdown',
                    'options' => [
                        'available' => Lang::get('wwrf.profile::lang.status.available'),
                        'employed'  => Lang::get('wwrf.profile::lang.status.employed'),
                        'unavailable'    => Lang::get('wwrf.profile::lang.status.unavailable')
                    ],
                    'span'    => 'auto'
                ],
                'profile_headline' => [
                    'label' => 'wwrf.profile::lang.profile.headline',
                    'tab'   => 'wwrf.profile::lang.profile.tab',
                    'commentAbove' => 'Please create a one-line statement for the employer to see...',
                    'span'  => 'left'
                ],
                'industries' => [
                    'label'   => 'Industry(ies)',
                    'commentAbove' => "Please specify atleast one industry...",
                    'type'    => 'taglist',
                    'mode' => 'relation',
                    'customTags' => false,
                    'tab' => 'wwrf.profile::lang.profile.tab',
                    'span' => 'right',
                ],
                'profile_summary' => [
                    'tab'   => 'wwrf.profile::lang.profile.tab',
                    'type' => 'richeditor',
                    'size' => 'giant',
                    'span'  => 'full'
                ]
            ]);

        });

        Event::listen('backend.menu.extendItems', function ($manager) {
			$manager->addSideMenuItems('RainLab.User', 'user', [
				'exmployers' => [
					'label' => 'Employers',
					'icon' => 'icon-building',
					'code' => 'employers',
					'owner' => 'RainLab.User',
					'url' => Backend::url('wwrf/transitioncenter/employers')
                ],
                'staff' => [
                    'label' => 'Staff',
                    'icon' => 'icon-male',
                    'code' => 'staff',
                    'owner' => 'RainLab.User',
                    'url' => Backend::url('wwrf/transitioncenter/staffmembers')
                ]
            ]);
            $manager->removeSideMenuItem('RainLab.User', 'user', 'passage_keys');
        });
        
        Event::listen('backend.list.injectRowClass', function ($lists, $record) {
            return $this->listInjectRowClass($record);
        });

        // Add import/export controller to user controller
        UsersController::extend(function($controller){
            
            // Implement behavior if not already implemented
            if (!$controller->isClassExtendedWith('Backend.Behaviors.ImportExportController')) {
                $controller->implement[] = 'Backend.Behaviors.ImportExportController';
            }

            // Define property if not already defined
            if (!isset($controller->relationConfig)) {
                $controller->addDynamicProperty('importExportConfig');
            }
            
            // Splice in configuration safely
            $myConfigPath = '$/wwrf/profile/controllers/profiles/config_import_export.yaml';

            $controller->importExportConfig = $controller->mergeConfig(
                $controller->importExportConfig,
                $myConfigPath
            );

            $configUserListPath = '$/wwrf/profile/controllers/profiles/config_users_list.yaml';

            $controller->listConfig = $controller->makeConfig($configUserListPath);
        });

        UserModel::extend(function($model) {

            $model->addFillable([
                'company_title',
                'company_phone',
                'company_name'
            ]);

            $model->rules['avatar'] = 'nullable';
            
            $model->addDynamicMethod('getYearOptions', function($value) use ($model)
            {

                    $query = Users::all();
            
                    $years = [];
            
                    foreach($query as $user) {
                        $year = date('Y', strtotime($user->created_at));
            
                        $years[$year] = $year;
                    }
            
                    $years = array_unique($years);
            
                    return $years;

            });

            $model->addDynamicMethod('scopeIsEmployed', function($query, $filter)
            {
                if($filter == 2){
                    return $query->has('jobs', '>', 0);
                }elseif($filter == 1) {
                    return $query->has('jobs', '<', 1);
                }
            });

            $model->addDynamicMethod('scopeFilterByGroup', function($query, $filter)
            {
                return $query->whereHas('groups', function($group) use ($filter) {
                    $group->whereIn('id', $filter);
                });
            });

            $model->addDynamicMethod('scopeFilterByStatus', function($query, $filter)
            {
                return $query->where('status', '=', $filter);
            });

            /**
             * Scope a query to only filter according to date.
             */
            $model->addDynamicMethod('scopeFilterByStartDate', function($query, $after, $before)
            {
                $countJobs =  $query->whereHas('jobs', function($q) use ($after, $before) {
                    $q->whereBetween('start_date', array($after, $before));
                });

                return $query->whereHas('jobs', function($q) use ($after, $before) {
                        $q->whereBetween('start_date', array($after, $before));
                });
            });

        });

    }

    public function registerListColumnTypes()
    {
      return [
        'dateformat' => function($value) {
                        $date = date_create($value); 
                        return date_format($date, 'M d, Y');
                    }
        ];
    }

    public function listInjectRowClass($record)
    {
        // return the css class
        // see https://octobercms.com/docs/ui/list at Row classes
    }

    public function getEligibleDate()
    {
        if ($this->orientation_date)
            $this->eligible_date = $this->orientation_date + 7;
    }
}
