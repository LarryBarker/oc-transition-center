# [RESTful-Plugin](https://github.com/SaifurRahmanMohsin/Restful-Plugin) #
Mobile plugin for October CMS

## Introduction ##

This plugin enables you to manage mobile applications. You can add your iOS, Android and other store app listing into the backend. It also lets you monitor individual app installs by adding some code on the client-side (see video or documentation).

Tutorial and demonstration of the plugin [here](http://www.youtube.com/embed/VJOH1x967ag).

[Note: Right now the Android plugin is under development so I have disabled the reserved plugin feature, you can manually create **Android** using Platforms settings directly]

## Features ##
* Add multiple variants and platforms.

## Coming Soon ##
* Dashboard widget to provide app install analytics.
* Maintenance mode features to block variants.
* Add custom fields and track those values.

## Requirements ##

* [RESTful](http://octobercms.com/plugin/mohsin-rest) plugin

## API Details ##

### Client-side Integration ###

Once you have created the plugin entry in the backend, you can do the client side integration. On the mobile app, you will have to make a call to the backend using the REST API that has been provided. RIght now, the API works like this:

#### POST /installs ####

**Resource URL:** [/api/v1/installs](/api/v1/installs) [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/1f9efcfa94c93810e739)

| Parameters | Description
------------- | -------------
instance_id  | A unique ID, such as device ID or an ID generated using Google’s Instance ID API. Eg. 573b61d82b4e46e3
package  | The package name of the application. Must match the name specified in the variants. Eg. com.acme.myapp
