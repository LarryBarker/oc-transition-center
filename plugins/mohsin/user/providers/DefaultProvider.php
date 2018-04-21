<?php namespace Mohsin\User\Providers;

use Mail;
use Lang;
use Auth;
use Event;
use Validator;
use Cms\Classes\Page;
use ValidationException;
use October\Rain\Auth\AuthException;
use Mohsin\User\Models\Settings;
use Mohsin\Mobile\Models\Install;
use Mohsin\Mobile\Models\Variant;
use October\Rain\Database\ModelException;
use Mohsin\User\Classes\ProviderBase;
use RainLab\User\Models\Settings as UserSettings;

class DefaultProvider extends ProviderBase
{
    /**
     * {@inheritDoc}
     */
    public function providerDetails()
    {
        return [
            'name'        => 'Default Provider',
            'description' => 'The default login provider'
        ];
    }

    /**
     * Returns a user object, after signing in
     */
    public function signin()
    {
        /*
         * Validate input
         */
        $data = post();
        $rules = [];

        $rules['instance_id'] = 'required|max:16|string|exists:mohsin_mobile_installs,instance_id';

        $rules['package'] = 'required|regex:/^[a-z0-9]*(\.[a-z0-9]+)+[0-9a-z]$/|exists:mohsin_mobile_variants,package';

        $rules['login'] = $this->loginAttribute() == UserSettings::LOGIN_USERNAME
            ? 'required|between:2,255'
            : 'required|email|between:6,255';

        $rules['password'] = 'required|between:4,255';

        if (!array_key_exists('login', $data)) {
            $data['login'] = post('username', post('email'));
        }

        $validation = Validator::make($data, $rules);
        if ($validation->fails()) {
            return response()->json($validation->messages()->first(), 400);
        }

        /*
         * Authenticate user
         */
        $credentials = [
            'login'    => array_get($data, 'login'),
            'password' => array_get($data, 'password')
        ];

        Event::fire('tempestronics.user.beforeAuthenticate', [$this, $credentials]);

        try {

          $install = Install::where('instance_id', '=', array_get($data, 'instance_id')) -> first();

          $user = Auth::authenticate($credentials, true);
          $user -> mobileuser_installs() -> save($install);

           /*
            * Return the user record on successful login
            */
            return response()->json($user, 200);
        } catch(AuthException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch(Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    /**
     * Returns a user object, after registration
     */
    public function register()
    {
        try {
            if (!UserSettings::get('allow_registration', true)) {
                return response()->json('registration-disabled', 400);
            }

            /*
             * Validate input
             */
            $data = post();

            if (!array_key_exists('password_confirmation', $data)) {
                $data['password_confirmation'] = post('password');
            }

            $rules = [
                'email'       => 'required|email|between:6,255',
                'password'    => 'required|between:4,255',
                'instance_id' => 'required|max:16|string|exists:mohsin_mobile_installs,instance_id',
                'package'     => 'required|regex:/^[a-z0-9]*(\.[a-z0-9]+)+[0-9a-z]$/|exists:mohsin_mobile_variants|registration_enabled'
            ];

            if ($this->loginAttribute() == UserSettings::LOGIN_USERNAME) {
                $rules['username'] = 'required|between:2,255';
            }

            Validator::extend('registration_enabled', function ($attribute, $value, $parameters, $validator)
            {
                if($variant = Variant::where('package', '=', $value) -> first()) {
                  if($variant -> disable_registration)
                    throw new ValidationException(['package' => trans('mohsin.user::lang.variants.registration_disabled')]);
                  return true;
                }
                return false;
            });

            $validation = Validator::make($data, $rules);

            if ($validation->fails()) {
                return response()->json($validation->messages()->first(), 400);
            }

            /*
             * Register user
             */
            $requireActivation = UserSettings::get('require_activation', true);
            $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
            $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
            $user = Auth::register($data, $automaticActivation);

            /*
             * Activation is by the user, send the email
             */
            if ($userActivation) {
                $this->sendActivationEmail($user);
            }

            /*
             * Return the created record on successful registration
             */
             return response()->json($user, 200);
        }
        catch (ModelException $ex) {
            return response()->json($ex->getMessage(), 400);
        }
        catch (ValidationException $ex) {
            return response()->json($ex->getMessage(), 400);
        }
        catch (Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    /*
     * Used internally, ripped off from RainLab.User
     */
    /**
     * Returns the login model attribute.
     */
    public function loginAttribute()
    {
        return UserSettings::get('login_attribute', UserSettings::LOGIN_EMAIL);
    }

    protected function sendActivationEmail($user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);

        $page = Settings::get('activation_page', '404');

        $link = Page::url($page) . '/' . $code;

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.activate', $data, function($message) use ($user) {
            $message->to($user->email, $user->name);
        });
    }
}
