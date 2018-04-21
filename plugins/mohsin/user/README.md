Mobile Front-end user management for October CMS.

** IMPORTANT! — Make sure you have purchased the `Mohsin.Mobile` plugin before purchasing this. **

# About Plugin #

This plugin is similar to the RainLab.User plugin except it’s built to work mainly with mobile front-ends. It exposes RESTful API nodes that enable interaction with the backend thereby allowing users to sign up and login as front-end users.

Tutorial and demonstration of the plugin [here](//www.youtube.com/embed/IkFzSzjoXJ0).

#### Features ####
* Completely extensible.
* Compatible with the multiple mobile apps feature of the mobile plugin.

#### Coming Soon ####
* Dashboard widget to provide user signup and register analytics.
* Forgot password and signing out nodes (Currently, you can do it through website only).
* Multiple Auth Schemes for better security.

### Requirements

* [Mohsin.Mobile plugin](http://octobercms.com/plugin/mohsin-mobile).
* [Rainlab.User plugin](http://octobercms.com/plugin/rainlab-user).
* [Mohsin.RESTful plugin](http://octobercms.com/plugin/mohsin-rest).

Most of the [RainLab User Documentation](https://octobercms.com/plugin/rainlab-user#documentation) applies to this as well, since it’s extended over that.

### Client-side Integration ###

**Note:** Make sure you have finished the client side integration for the mobile plugin first. Any route calls to this plugin’s nodes MUST happen AFTER the **installs** route request of the mobile plugin. Otherwise, an error may be thrown.
On the mobile app, you will have to make a call to the backend using the REST API that has been provided. Right now, the API works like this:

#### POST /account/signin ####

**Resource URL:** [/api/v1/account/signin](/api/v1/account/signin) [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/997ae8398f934757e196)

 | Parameters | Description
------------- | -------------
instance_id  | A unique ID, such as device ID or an ID generated using Google’s Instance ID API. Eg. 573b61d82b4e46e3
package  | The package name of the application. Must match the name specified in the variants. Eg. com.acme.myapp
email (or) username | The login attribute for the user attempting to sign in.
password | The password for the user attempting to sign in.

#### POST /account/register ####

**Resource URL:** [/api/v1/account/register](/api/v1/account/register) [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/997ae8398f934757e196)

 | Parameters | Description
------------- | -------------
name | The name of the user attempting registration.
email | The email for the user attempting registration.
username (optional) | The username for the user attempting registration.
password | The password for the user attempting registration.
