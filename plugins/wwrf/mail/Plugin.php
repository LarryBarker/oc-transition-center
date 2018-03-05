<?php namespace Wwrf\Mail;



use Backend;

use Config;

use App;

use System\Classes\PluginBase;

use Microsoft\Graph\Graph;

use Microsoft\Graph\Model;



/**

 * Mail Plugin Information File

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

            'name'        => 'Mail',

            'description' => 'Plugin adds mail functionality to front-end.',

            'author'      => 'Wwrf',

            'icon'        => 'icon-envelop'

        ];

    }



    /**

     * Register method, called when the plugin is first registered.

     *

     * @return void

     */

    public function register()

    {



    }



    /**

     * Boot method, called right before the request route.

     *

     * @return array

     */

    public function boot()

    {



    }



    /**

     * Registers any front-end components implemented in this plugin.

     *

     * @return array

     */

    public function registerComponents()

    {

        //return []; // Remove this line to activate

        return [

            'Wwrf\Mail\Components\Mail' => 'userMail',

            'Wwrf\Mail\Components\MailMessage' => 'mailMessage',

            'Wwrf\Mail\Components\SendMail' => 'sendMail',

            'Wwrf\Mail\Components\Reply' => 'replyMail'

        ];

    }



    /**

     * Registers any back-end permissions used by this plugin.

     *

     * @return array

     */

    public function registerPermissions()

    {

        return []; // Remove this line to activate



        return [

            'wwrf.employers.some_permission' => [

                'tab' => 'Employers',

                'label' => 'Some permission'

            ],

        ];

    }



    /**

     * Registers back-end navigation items for this plugin.

     *

     * @return array

     */

    public function registerNavigation()

    {

        return []; // Remove this line to activate



        /*return [

            'mail' => [

                'label'       => 'Mail',

                'url'         => Backend::url('wwrf/mail/mycontroller'),

                'icon'        => 'icon-envelope',

                'permissions' => ['wwrf.mail.*'],

                'order'       => 500,

            ],

        ];*/

    }



    /**

    * Boots (configures and registers) any packages found within this plugin's packages.load configuration value

    *

    * @see https://luketowers.ca/blog/how-to-use-laravel-packages-in-october-plugins

    * @author Luke Towers <octobercms@luketowers.ca>

    */

    public function bootPackages()

    {

        // Get the namespace of the current plugin to use in accessing the Config of the plugin

        $pluginNamespace = str_replace('\\', '.', strtolower(__NAMESPACE__));

        

        // Instantiate the AliasLoader for any aliases that will be loaded

        $aliasLoader = AliasLoader::getInstance();

        

        // Get the packages to boot

        $packages = Config::get($pluginNamespace . '::packages');

        

        // Boot each package

        foreach ($packages as $name => $options) {

            // Setup the configuration for the package, pulling from this plugin's config

            if (!empty($options['config']) && !empty($options['config_namespace'])) {

                Config::set($options['config_namespace'], $options['config']);

            }

            

            // Register any Service Providers for the package

            if (!empty($options['providers'])) {

                foreach ($options['providers'] as $provider) {

                    App::register($provider);

                }

            }

            

            // Register any Aliases for the package

            if (!empty($options['aliases'])) {

                foreach ($options['aliases'] as $alias => $path) {

                    $aliasLoader->alias($alias, $path);

                }

            }

        }

    }

}