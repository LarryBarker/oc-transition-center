<?php namespace Mohsin\User\Classes;

use Yaml;
use File;
use ApplicationException;

/**
 * Represents the generic login provider.
 * All other login providers must be derived from this class
 */
abstract class ProviderBase
{
    use \System\Traits\ConfigMaker;

    /**
     * @var mixed Extra field configuration for the login provider.
     */
    protected $fieldConfig = null;

    /**
     * Constructor
     */
    function __construct()
    {
        $extraFieldsConfig = $this->guessConfigPathFrom($this) . '/fields.yaml';
        if (File::exists($extraFieldsConfig))
        {
            $config = Yaml::parse(File::get($extraFieldsConfig));
            foreach($config as $key => $value)
             {
                 $config[$key]['trigger'] = [
                     'action' => 'show',
                     'field' => 'provider',
                     'condition' => 'value[accountkit]'
                   ];
             }
             $this -> fieldConfig = $config;
        }
    }

    /**
     * Returns the field configuration used by this model.
     * Returns null if no fields YAML file is defined.
     */
    public function getFieldConfig()
    {
        return $this->fieldConfig;
    }

    /**
     * Returns information about the login provider
     * Must return array:
     *
     * [
     *      'name'        => 'AccountKit',
     *      'description' => 'Facebook AccountKit Mobile Login provider.'
     * ]
     *
     * @return array
     */
    abstract protected function providerDetails();

    /**
     * Sign in the user
     */
    abstract protected function signin();

    /**
     * Register the user
     */
    abstract protected function register();
}
