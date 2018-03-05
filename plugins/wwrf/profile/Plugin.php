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
        UserModel::extend(function($model)
        {
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

            $model->belongsToMany['industries'] = ['RainLab\Blog\Models\Category', 'table' => 'wwrf_users_industries', 'otherKey' => 'industry_id'];

            $model->bindEvent('model.beforeValidate', function() use ($model) {
                $model->rules['industries'] = 'max:4';
            });
        });

        CategoryModel::extend(function($model)
        {
            $model->belongsToMany['users'] = ['RainLab\User\Models\User', 'table' => 'wwrf_users_industries'];
        });
      
        UsersController::extendFormFields(function($form, $model, $context)
        {
            if (!$model instanceof UserModel) {
                return;
            }

            $form->removeField('comment');

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

            $form->addSecondaryTabFields([
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
        });

        // Extend all backend list usage
        /*Event::listen('backend.list.extendColumns', function($widget) {
            
            // Only for the User controller
            if (!$widget->getController() instanceof \RainLab\User\Controllers\Users) {
                return;
            }

            // Only for the User model
            if (!$widget->model instanceof \RainLab\User\Models\User) {
                return;
            }

            // Add extra columns for reporting
            $widget->addColumns([
                'eligible_date' => [
                    'label' => 'Eligible',
                ],
                'job_title' => [
                    'label' => 'Title',
                    'type' => 'partial',
                    'path' => '~/plugins/wwrf/profile/models/UserExport/_title.htm',
                    //'valueFrom' => 'title'
                    'sortable' => false
                ],
                'company' => [
                    'label' => 'Company',
                    'type' => 'partial',
                    'path' => '~/plugins/wwrf/profile/models/UserExport/_company.htm',
                    //'valueFrom' => 'title'
                    'sortable' => false
                ],
                'industries' => [
                    'label' => 'Industry',
                    'type' => 'partial',
                    'path' => '~/plugins/wwrf/profile/models/UserExport/_industry.htm',
                    'sortable' => false
                ],
                'start_date' => [
                    'label' => 'Start Date',
                    'type' => 'partial',
                    'path' => '~/plugins/wwrf/profile/models/UserExport/_start_date.htm',
                    //'valueFrom' => 'title'
                    'sortable' => false
                ],
                'start_wage' => [
                    'label' => 'Start Wage',
                    'type' => 'partial',
                    'path' => '~/plugins/wwrf/profile/models/UserExport/_wage.htm',
                    //'valueFrom' => 'title'
                    'sortable' => false
                ],
                'status' => [
                    'label' => 'Status',
                    'sortable' => true,
                ],
                
                'lead_time' => [
                    'label' => 'Lead Time',
                    'type' => 'partial',
                    'path' => '~/plugins/wwrf/profile/models/UserExport/_lead_time.htm',
                    'sortable' => false
                ],
                
            ]);
        });*/

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
