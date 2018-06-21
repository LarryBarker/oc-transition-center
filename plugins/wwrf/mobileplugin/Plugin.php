<?php namespace Wwrf\MobilePlugin;

use App;
use Lang;
use Event;
use Backend;
use System\Classes\PluginBase;
use Wwrf\MobilePlugin\Models\App as AppModel;
use Felixkiss\UniqueWithValidator\ValidatorExtension;
use Wwrf\MobilePlugin\Classes\ProviderManager;
use RainLab\User\Models\User as UserModel;
use Wwrf\MobilePlugin\Models\Install as InstallModel;
use Wwrf\MobilePlugin\Models\Variant as VariantModel;
use Wwrf\MobilePlugin\Models\Settings as SettingsModel;
use RainLab\User\Controllers\Users as UsersController;
use Wwrf\MobilePlugin\Controllers\Apps as AppsController;
use Wwrf\MobilePlugin\Controllers\Installs as InstallsController;

/**
 * MobilePlugin Information File
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
            'name'        => 'MobilePlugin',
            'description' => 'No description provided yet...',
            'author'      => 'Wwrf',
            'icon'        => 'icon-cloud'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConsoleCommand('create.restcontroller', 'Wwrf\MobilePlugin\Console\CreateRestController');
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot()
    {
        // Register ServiceProviders
        App::register('\Felixkiss\UniqueWithValidator\UniqueWithValidatorServiceProvider');
        // Registering the validator extension with the validator factory
        $this->app['validator']->resolver(function($translator, $data, $rules, $messages)
        {
            // Set custom validation error messages
            $messages['unique_with'] = $translator->get('uniquewith-validator::validation.unique_with');

            return new ValidatorExtension($translator, $data, $rules, $messages);
        });

        UserModel::extend(function($model){
            $model->hasMany['mobileuser_installs'] = ['Wwrf\MobilePlugin\Models\Install'];
        });

        InstallModel::extend(function($model){
            $model->belongsTo['user'] = ['RainLab\User\Models\User'];
        });

        InstallsController::extendListColumns(function($list, $model){
            if (!$model instanceof InstallModel)
                return;

            $list->addColumns([
                'user' => [
                    'label' => 'rainlab.user::lang.plugin.name',
                    'relation' => 'user',
                    'valueFrom' => 'id',
                    'default' => Lang::get('wwrf.mobileplugin::lang.installs.unregistered')
                ]
            ]);

        });

        UsersController::extend(function($controller){
            $controller->addCss('/plugins/wwrf/mobileuser/assets/css/custom.css');
            // And again for their programs

            $mobileRelationConfig = '$/wwrf/mobileplugin/models/relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $mobileRelationConfig
            );
        });

        UsersController::extendFormFields(function($form, $model, $context){
            if(!$model instanceof UserModel)
                return;

            if(!$model->exists)
              return;

            $form->addTabFields([
                'mobileuser_installs' => [
                    'label' => 'wwrf.mobileplugin::lang.users.mobileuser_installs_label',
                    'tab' => 'Mobile',
                    'type' => 'partial',
                    'path' => '$/wwrf/mobileplugin/assets/partials/_field_mobileuser_installs.htm',
                  ],

              ]);
        });

        AppsController::extendListColumns(function($list, $model){
            if (!$model instanceof VariantModel)
                return;

            $list->addColumns([
                'disable_registration' => [
                    'label' => 'wwrf.mobileplugin::lang.variants.allow_registration_label',
                    'type' => 'switch'
                ]
            ]);

        });

        AppsController::extendFormFields(function($form, $model, $context) {
          if(!$model instanceof VariantModel)
              return;

          $form->getField('is_maintenance')->span = 'left';

          $form->addFields([
              'disable_registration' => [
                  'label' => 'wwrf.mobileplugin::lang.variants.allow_registration_label',
                  'comment' => 'wwrf.mobileplugin::lang.variants.allow_registration_comment',
                  'type' => 'checkbox',
                  'span' => 'right'
                ],
            ]);
        });

        Event::listen('backend.form.extendFields', function ($form) {
          
           if (!$form->model instanceof SettingsModel)
                return;

            $providers = ProviderManager::instance()->listProviderObjects();
            foreach($providers as $provider)
              {
                  $config = $provider -> getFieldConfig();
                  if(!is_null($config))
                      $form->addFields($config);
              }
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Wwrf\MobilePlugin\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'wwrf.mobileplugin.view_installs' => [
                'tab' => 'wwrf.mobileplugin::lang.plugin.name',
                'label' => 'wwrf.mobileplugin::lang.install.view_installs'
            ],
            'wwrf.mobileplugin.manage_apps' => [
                'tab' => 'Mobile',
                'label' => 'Manage apps'
            ],
            'wwrf.mobileplugin.access_users' => ['tab' => 'Mobile', 'label' => 'rainlab.user::lang.plugin.access_users'],
            'wwrf.mobileplugin.access_settings' => ['tab' => 'Mobile', 'label' => 'rainlab.user::lang.plugin.access_settings']
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'apps' => [
                'label'       => 'Apps',
                'description' => 'Manage the apps and their variants.',
                'category'    => 'Mobile',
                'icon'        => 'icon-mobile',
                'url'         => Backend::url('wwrf/mobileplugin/apps'),
                'order'       => 500,
                'keywords'    => 'apps builds variants',
                'permissions' => ['wwrf.mobileplugin.manage_apps']
            ],
            'platforms' => [
                'label'       => 'Platforms',
                'description' => 'Manage the available platforms.',
                'category'    => 'Mobile',
                'icon'        => 'icon-th-large',
                'url'         => Backend::url('wwrf/mobileplugin/platforms'),
                'order'       => 501,
                'keywords'    => 'apps builds variants',
                'permissions' => ['wwrf.mobileplugin.manage_apps']
            ],
            'settings' => [
                'label'       => 'wwrf.mobileplugin::lang.settings.name',
                'description' => 'wwrf.mobileplugin::lang.settings.description',
                'category'    => 'Mobile',
                'icon'        => 'icon-user-plus',
                'class'       => 'Wwrf\MobilePlugin\Models\Settings',
                'order'       => 502,
                'permissions' => ['wwrf.mobileplugin.access_settings'],
            ]
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'mobileplugin' => [
                'label'       => 'wwrf.mobileplugin::lang.plugin.name',
                'url'         => Backend::url('wwrf/mobileplugin/installs'),
                'icon'        => 'icon-cloud',
                'permissions' => ['wwrf.mobileplugin.*'],
                'order'       => 500,

                'sideMenu' => [
                    'installs' => [
                        'label'       => 'wwrf.mobileplugin::lang.plugin.name',
                        'icon'        => 'icon-cloud-download',
                        'url'         => Backend::url('wwrf/mobileplugin/installs'),
                        'order'       => 100,
                        'permissions' => ['wwrf.mobileplugin.view_installs']
                    ]
                ]
            ],
        ];
    }

    public function registerReportWidgets()
    {
        if(AppModel::count() > 0) {
          return [
              'Wwrf\MobilePlugin\ReportWidgets\InstallsOverview'=>[
                  'label'   => 'App Installs Overview',
                  'context' => 'dashboard'
              ]
          ];
        } else return [];
    }

    /**
     * Registers mobile login providers implemented by this plugin.
     * 
     * @return array ['className' => 'alias']
     */
    public function registerMobileLoginProviders()
    {
        // Note: the DefaultProvider does not need Provider suffix
        // it's there due to PHP class naming restriction to prevent reserved keyword use.
        return [
            'Wwrf\MobilePlugin\Providers\DefaultProvider' => 'default'
        ];
    }
}
