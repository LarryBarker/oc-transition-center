<?php namespace Mohsin\User;

use Lang;
use Event;
use Backend;
use System\Classes\PluginBase;
use Mohsin\User\Classes\ProviderManager;
use RainLab\User\Models\User as UserModel;
use Mohsin\Mobile\Models\Install as InstallModel;
use Mohsin\Mobile\Models\Variant as VariantModel;
use Mohsin\User\Models\Settings as SettingsModel;
use RainLab\User\Controllers\Users as UsersController;
use Mohsin\Mobile\Controllers\Apps as AppsController;
use Mohsin\Mobile\Controllers\Installs as InstallsController;

/**
 * User Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['Mohsin.Mobile', 'RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'mohsin.user::lang.plugin.name',
            'description' => 'mohsin.user::lang.plugin.description',
            'author'      => 'Mohsin',
            'icon'        => 'icon-user'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        UserModel::extend(function($model){
            $model -> hasMany['mobileuser_installs'] = ['Mohsin\Mobile\Models\Install'];
        });

        InstallModel::extend(function($model){
            $model -> belongsTo['user'] = ['RainLab\User\Models\User'];
        });

        InstallsController::extendListColumns(function($list, $model){
            if (!$model instanceof InstallModel)
                return;

            $list->addColumns([
                'user' => [
                    'label' => 'rainlab.user::lang.plugin.name',
                    'relation' => 'user',
                    'valueFrom' => 'id',
                    'default' => Lang::get('mohsin.user::lang.installs.unregistered')
                ]
            ]);

        });

        UsersController::extend(function($controller){
            $controller->addCss('/plugins/mohsin/user/assets/css/custom.css');

            if(!isset($controller->implement['Backend.Behaviors.RelationController']))
                $controller->implement[] = 'Backend.Behaviors.RelationController';
            $controller->relationConfig  =  '$/mohsin/user/models/relation.yaml';
        });

        UsersController::extendFormFields(function($form, $model, $context){
            if(!$model instanceof UserModel)
                return;

            if(!$model->exists)
              return;

            $form->addTabFields([
                'mobileuser_installs' => [
                    'label' => 'mohsin.user::lang.users.mobileuser_installs_label',
                    'tab' => 'Mobile',
                    'type' => 'partial',
                    'path' => '$/mohsin/user/assets/partials/_field_mobileuser_installs.htm',
                  ],

              ]);
        });

        AppsController::extendListColumns(function($list, $model){
            if (!$model instanceof VariantModel)
                return;

            $list->addColumns([
                'disable_registration' => [
                    'label' => 'mohsin.user::lang.variants.allow_registration_label',
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
                  'label' => 'mohsin.user::lang.variants.allow_registration_label',
                  'comment' => 'mohsin.user::lang.variants.allow_registration_comment',
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
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'mohsin.user.access_users' => ['tab' => 'Mobile', 'label' => 'rainlab.user::lang.plugin.access_users'],
            'mohsin.user.access_settings' => ['tab' => 'Mobile', 'label' => 'rainlab.user::lang.plugin.access_settings']
        ];
    }

    /**
     * Registers settings controller for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'mohsin.user::lang.settings.name',
                'description' => 'mohsin.user::lang.settings.description',
                'category'    => 'Mobile',
                'icon'        => 'icon-user-plus',
                'class'       => 'Mohsin\User\Models\Settings',
                'order'       => 502,
                'permissions' => ['mohsin.user.access_settings'],
            ]
        ];
    }

    /**
     * Registers any mobile login providers implemented in this plugin.
     * The providers must be returned in the following format:
     * ['className1' => 'alias'],
     * ['className2' => 'anotherAlias']
     */
    public function registerMobileLoginProviders()
    {
        // Note that the DefaultProvider does not need Provider suffix
        //but it's there due to PHP class naming restriction to use the reserved Default keyword.
        return [
            'Mohsin\User\Providers\DefaultProvider' => 'default'
        ];
    }

}
