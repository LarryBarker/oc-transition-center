<?php namespace Mohsin\User\Classes;

use File;
use Yaml;
use Event;
use Response;
use Cms\Classes\Theme;
use Cms\Classes\Partial;
use System\Classes\PluginManager;
use October\Rain\Support\Collection;

/**
 * Manages payment providers
 *
 * @package Responsiv.Pay
 * @author Responsiv Internet
 */
class ProviderManager
{
    use \October\Rain\Support\Traits\Singleton;

    /**
     * @var array Cache of registration callbacks.
     */
    private $callbacks = [];

    /**
     * @var array List of registered providers.
     */
    private $providers;

    /**
     * @var System\Classes\PluginManager
     */
    protected $pluginManager;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->pluginManager = PluginManager::instance();
    }

    /**
     * Loads the menu items from modules and plugins
     * @return void
     */
    protected function loadProviders()
    {
        /*
         * Load module items
         */
        foreach ($this->callbacks as $callback) {
            $callback($this);
        }

        /*
         * Load plugin items
         */
        $plugins = $this->pluginManager->getPlugins();

        foreach ($plugins as $id => $plugin) {
            if (!method_exists($plugin, 'registerMobileLoginProviders'))
                continue;

            $providers = $plugin->registerMobileLoginProviders();
            if (!is_array($providers))
                continue;

            $this->registerProviders($id, $providers);
        }
    }

    /**
     * Registers a callback function that defines a payment provider.
     * The callback function should register providers by calling the manager's
     * registerProviders() function. The manager instance is passed to the
     * callback function as an argument. Usage:
     * <pre>
     *   GatewayManager::registerCallback(function($manager){
     *       $manager->registerProviders([...]);
     *   });
     * </pre>
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }

    /**
     * Registers the payment providers.
     * The argument is an array of the provider classes.
     * @param string $owner Specifies the menu items owner plugin or module in the format Author.Plugin.
     * @param array $classes An array of the payment provider classes.
     */
    public function registerProviders($owner, array $classes)
    {
        if (!$this->providers)
            $this->providers = [];

        foreach ($classes as $class => $alias) {
            $provider = (object)[
                'owner' => $owner,
                'class' => $class,
                'alias' => $alias,
            ];

            $this->providers[$alias] = $provider;
        }
    }

    /**
     * Returns a list of the payment provider classes.
     * @param boolean $asObject As a collection with extended information found in the class object.
     * @return array
     */
    public function listProviders($asObject = true)
    {
        if ($this->providers === null) {
            $this->loadProviders();
        }

        if (!$asObject) {
            return $this->providers;
        }

        /*
         * Enrich the collection with provider objects
         */
        $collection = [];
        foreach ($this->providers as $provider) {
            if (!class_exists($provider->class))
                continue;

            $providerObj = new $provider->class;
            $providerDetails = $providerObj->providerDetails();
            $collection[$provider->alias] = (object)[
                'owner'       => $provider->owner,
                'class'       => $provider->class,
                'alias'       => $provider->alias,
                'object'      => $providerObj,
                'name'        => array_get($providerDetails, 'name', 'Undefined'),
                'description' => array_get($providerDetails, 'description', 'Undefined'),
            ];
        }

        return new Collection($collection);
    }

    /**
     * Returns a list of the login provider objects
     * @return array
     */
    public function listProviderObjects()
    {
        $collection = [];
        $providers = $this->listProviders();
        foreach ($providers as $provider) {
            $collection[$provider->alias] = $provider->object;
        }

        return $collection;
    }

    /**
     * Returns a provider based on its unique alias.
     */
    public function findByAlias($alias)
    {
        $providers = $this->listProviders(false);
        if (!isset($providers[$alias]))
            return false;

        return $providers[$alias];
    }

}
