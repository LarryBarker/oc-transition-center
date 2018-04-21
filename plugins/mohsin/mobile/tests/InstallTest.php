<?php namespace Mohsin\Mobile\Tests;

require_once "../../../tests/PluginTestCase.php";

use App;
use PluginTestCase;
use Mohsin\Mobile\Plugin as MobilePlugin;
use Mohsin\Mobile\Models\App as AppModel;
use Mohsin\Mobile\Models\Platform;
use Mohsin\Mobile\Models\Install;
use Mohsin\Mobile\Http\Installs;
use Mohsin\Mobile\Models\Variant;
use System\Classes\PluginManager;
use Felixkiss\UniqueWithValidator\ValidatorExtension;

class InstallTest extends PluginTestCase
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

    public function testSetup()
    {
        $this->seeInDatabase('mohsin_mobile_apps', [
          'name' => 'Sample App',
          'description' => 'This is a sample app.',
          'maintenance_message' => 'Sorry, our servers are under maintenance. Please try again in a couple hours.'
        ]);

        $this->seeInDatabase('mohsin_mobile_variants', [
          'app_id' => 1,
          'package' => 'com.acme.test',
          'platform_id' => 1,
          'description' => 'Sample Prod'
        ]);
    }

    public function testInstall()
    {
        $variant = Variant::where('package', '=', 'com.acme.test') -> first();

        $install = new Install;
        $install -> instance_id = '573b61d82b4e46e7';
        $install -> variant_id = $variant -> id;
        $install -> last_seen = $install -> freshTimestamp();
        $install -> save(); // Shouldn't be a force save

        $this->seeInDatabase('mohsin_mobile_installs', [
          'instance_id' => '573b61d82b4e46e7',
          'variant_id' => $variant -> id
        ]);
    }
}
