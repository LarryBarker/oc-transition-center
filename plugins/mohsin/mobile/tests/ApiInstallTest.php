<?php namespace Mohsin\Mobile\Tests;

require_once "../../../tests/PluginTestCase.php";

use App;
use PluginTestCase;
use Mohsin\Mobile\Models\App as AppModel;
use Mohsin\Mobile\Models\Platform;
use Mohsin\Mobile\Models\Install;
use Mohsin\Mobile\Http\Installs;
use Mohsin\Mobile\Models\Variant;
use System\Classes\PluginManager;
use Felixkiss\UniqueWithValidator\ValidatorExtension;

class ApiInstallTest extends PluginTestCase
{
    protected $baseUrl = 'http://acme.dev';

    public function setUp()
    {
        parent::setUp();

        // Register ServiceProviders
        App::register('\Felixkiss\UniqueWithValidator\UniqueWithValidatorServiceProvider');
        // Registering the validator extension with the validator factory
        $this->app['validator']->resolver(function($translator, $data, $rules, $messages)
        {
            // Set custom validation error messages
            $messages['unique_with'] = $translator->get('uniquewith-validator::validation.unique_with');

            return new ValidatorExtension($translator, $data, $rules, $messages);
        });

        $platform = Platform::create(['name' => 'Acme']);

        $app = AppModel::create(['name' => 'Sample App', 'description' => 'This is a sample app.', 'maintenance_message' => 'Sorry, our servers are under maintenance. Please try again in a couple hours.']);
        $variant = Variant::create(['app_id' => $app -> id, 'package' => 'com.acme.test', 'platform_id' => $platform -> id, 'description' => 'Sample Prod']);

        $app -> variants() -> save($variant);
    }

    public function testInstallApiForNewInstall()
    {
      $data = [
        'instance_id' => '573b61d82b4e46e7',
        'package' => 'com.acme.test'
      ];

      $response = $this -> json('POST', 'api/v1/installs', [
          'instance_id' => array_get($data, 'instance_id'),
          'package' => array_get($data, 'package'),
      ]);

      // Verify if response is correct
      $this->assertResponseOk();
      $this->receiveJson();
      $this->seeJson(['new-install']);

      // Verify if data has been added to database
      $variant = Variant::where('package', '=', array_get($data, 'package')) -> first();
      $this->seeInDatabase('mohsin_mobile_installs', [
        'instance_id' => array_get($data, 'instance_id'),
        'variant_id' => $variant -> id
      ]);

    }
}
