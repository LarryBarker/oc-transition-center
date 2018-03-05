<?php

namespace Wwrf\Mail\Controllers;

use Backend\Classes\Controller;

use Session;

class AuthController extends Controller
{
  public function signin() 
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    // Initialize the OAuth client
    $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => env('OAUTH_APP_ID'),
      'clientSecret'            => env('OAUTH_APP_PASSWORD'),
      'redirectUri'             => env('OAUTH_REDIRECT_URI'),
      'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
      'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
      'urlResourceOwnerDetails' => '',
      'scopes'                  => env('OAUTH_SCOPES')
    ]);

    $authorizationUrl = $oauthClient->getAuthorizationUrl();
    
    // Save client state so we can validate in response

    \Session::put('oauth_state', $oauthClient->getState());

    // Redirect to authorization endpoint
    header('Location: '.$authorizationUrl);
    //echo \Session::get('oauth_state');
    exit();
  }
  public function gettoken()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  
    // Authorization code should be in the "code" query param
    if (isset($_GET['code'])) {
      // Check that state matches
      /*if (empty($_GET['state']) || ($_GET['state'] !== \Session::get('oauth_state'))) {
        exit('State provided in redirect does not match expected value.');
      }
  
      // Clear saved state
      \Session::forget('oauth_state');*/
  
      // Initialize the OAuth client
    $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => env('OAUTH_APP_ID'),
        'clientSecret'            => env('OAUTH_APP_PASSWORD'),
        'redirectUri'             => env('OAUTH_REDIRECT_URI'),
        'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
        'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
        'urlResourceOwnerDetails' => '',
        'scopes'                  => env('OAUTH_SCOPES')
      ]);
  
      try {
        // Make the token request
        $accessToken = $oauthClient->getAccessToken('authorization_code', [
          'code' => $_GET['code']
        ]);
  
        // Save the access token and refresh tokens in session
        // This is for demo purposes only. A better method would
        // be to store the refresh token in a secured database
        $tokenCache = new \Wwrf\Mail\Classes\TokenCache;
        $tokenCache->storeTokens($accessToken->getToken(), $accessToken->getRefreshToken(),
          $accessToken->getExpires());
  
        // Redirect back to mail page
        //return redirect()->route('mail');
        return redirect('/mail');
      }
      catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        exit('ERROR getting tokens: '.$e->getMessage());
      }
      exit();
    }
    elseif (isset($_GET['error'])) {
      exit('ERROR: '.$_GET['error'].' - '.$_GET['error_description']);
    }
  }
  
  public static function getAfterFilters() {return [];}
  public static function getBeforeFilters() {return [];}
  public static function getMiddleware() {return [];}
  public function callAction($method, $parameters=false) {
      return call_user_func_array(array($this, $method), $parameters);
  }
}