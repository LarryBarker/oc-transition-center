<?php namespace Wwrf\MobilePlugin\Tests;

require_once "../../../tests/PluginTestCase.php";

use App;
use PluginTestCase;
use Wwrf\MobilePlugin\Plugin as MobilePlugin;
use Wwrf\MobilePlugin\Models\App as AppModel;
use Wwrf\MobilePlugin\Models\Platform;
use Wwrf\MobilePlugin\Models\Install;
use Wwrf\MobilePlugin\Http\Installs;
use Wwrf\MobilePlugin\Models\Variant;
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
        $this->seeInDatabase('wwrf_mobile_apps', [
          'name' => 'Sample App',
          'description' => 'This is a sample app.',
          'maintenance_message' => 'Sorry, our servers are under maintenance. Please try again in a couple hours.'
        ]);

        $this->seeInDatabase('wwrf_mobile_variants', [
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

        $this->seeInDatabase('wwrf_mobile_installs', [
          'instance_id' => '573b61d82b4e46e7',
          'variant_id' => $variant -> id
        ]);
    }
}
