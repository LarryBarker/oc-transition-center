<?php namespace Mohsin\Mobile\Http;

use Event;
use Input;
use Validator;
use ApplicationException;
use Backend\Classes\Controller;
use Mohsin\Mobile\Models\Install;
use Mohsin\Mobile\Models\Variant;

/**
 * Installs Back-end Controller
 */
class Installs extends Controller
{
    public $implement = [
        'Mohsin.Rest.Behaviors.RestController'
    ];

    public $restConfig = 'config_rest.yaml';

    public function store()
    {
      $data = Input::all();

      $instance_id = array_get($data, 'instance_id');
      $package = array_get($data, 'package');

      // Extensibility - Fire beforeSave Event
      $beforeSaveResponses = Event::fire('mohsin.mobile.beforeSave', [$instance_id, $package]);
      foreach($beforeSaveResponses as $beforeSaveResponse)
        {
          if(!$beforeSaveResponse instanceof \Illuminate\Http\JsonResponse)
            throw new ApplicationException('The event mohsin.mobile.beforeSave can only return JsonResponse');

          if($beforeSaveResponse -> getStatusCode() == 400)
            return $beforeSaveResponse;
        }

      /*
      * Validate input
      */
      $rules = [];

      $rules['instance_id'] = 'required|max:16|string';
      $rules['package'] = 'required|regex:/^[a-z0-9]*(\.[a-z0-9]+)+[0-9a-z]$/|exists:mohsin_mobile_variants,package';

      $validation = Validator::make($data, $rules);
      if ($validation->fails()) {
          return response()->json($validation->messages()->first(), 400);
      }

      // Maintenance mode logic
      $variant = Variant::where('package', '=', $package) -> first();
      if($variant->is_maintenance)
        return response()->json($variant->app->maintenance_message, 503);

      $install = new Install;
      $install -> instance_id = $instance_id;
      $install -> variant_id = $variant -> id;
      $install -> last_seen = $install -> freshTimestamp();

      if($install -> save()) {

        // Extensibility - Fire afterSave Event
        $afterSaveResponses = Event::fire('mohsin.mobile.afterSave', [$install]);
        foreach($afterSaveResponses as $afterSaveResponse)
          {
            if(!$afterSaveResponse instanceof \Illuminate\Http\JsonResponse)
              throw new ApplicationException('The event mohsin.mobile.afterSave can only return JsonResponse');

            if($afterSaveResponse -> getStatusCode() == 400)
              return $afterSaveResponse;
          }

          return response()->json('new-install', 200);
        }
      else {
          // See if this is due to conflict, if so update the last_login time and return success
          if(($existingInstall = Install::where('instance_id','=',$instance_id)->where('variant_id','=',$variant->id)->first()) != null)
            {
                $existingInstall -> touchLastSeen();
                return response()->json('existing-install', 200);
            }

          return response()->json($install->errors()->first(), 400);
        }
    }

}
