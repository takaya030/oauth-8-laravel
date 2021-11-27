# OAuth wrapper for Laravel 8

oauth-8-laravel is a simple Laravel 8 service provider (wrapper) for [Lusitanian/PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib) 
which provides oAuth support in PHP 7.2+ and is very easy to integrate with any project which requires an oAuth client.

Was first developed by [Artdarek](https://github.com/artdarek/oauth-4-laravel) for Laravel 4 and I ported it to Laravel 8.

---

- [Supported services](#supported-services)
- [Installation](#installation)
- [Registering the Package](#registering-the-package)
- [Configuration](#configuration)
- [Usage](#usage)
- [Basic usage](#basic-usage)
- [More usage examples](#more-usage-examples)

## Supported services

The library supports both oAuth 1.x and oAuth 2.0 compliant services. A list of currently implemented services can be found below.

Included service implementations:

- OAuth1
    - 500px
    - BitBucket
    - Etsy
    - FitBit
    - Flickr
    - QuickBooks
    - Scoop.it!
    - Tumblr
    - Twitter
    - Xing
    - Yahoo
- OAuth2
    - Amazon
    - BitLy
    - Bitrix24
    - Box
    - Buffer
    - Dailymotion
    - Delicious
    - Deezer
    - DeviantArt
    - Dropbox
    - Eve Online
    - Facebook
    - Foursquare
    - GitHub
    - Google
    - Harvest
    - Heroku
    - Hubic
    - Instagram
    - Jawbone UP
    - LinkedIn
    - Mailchimp
    - Microsoft
    - Mondo
    - Nest
    - Netatmo
    - Parrot Flower Power
    - PayPal
    - Pinterest
    - Pocket
    - Reddit
    - RunKeeper
    - Salesforce
    - SoundCloud
    - Spotify
    - Strava
    - Ustream
    - Vimeo
    - Vkontakte
    - Yahoo
    - Yammer
- more to come!

## Installation

Add oauth-8-laravel to your composer.json file:

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/takaya030/oauth-8-laravel"
    }
],
"require": {
  "takaya030/oauth-8-laravel": "dev-master"
}
```

Use composer to install this package.

```
$ composer update
```

### Registering the Package

Register the service provider within the ```providers``` array found in ```config/app.php```:

```php
'providers' => [
	// ...

    Takaya030\OAuth\OAuthServiceProvider::class,
]
```

Add an alias within the ```aliases``` array found in ```config/app.php```:


```php
'aliases' => [
	// ...

    'OAuth'     => Takaya030\OAuth\Facade\OAuth::class,
]
```

## Configuration

There are two ways to configure oauth-5-laravel.
You can choose the most convenient way for you. 
You can use package config file which can be 
generated through command line by artisan (option 1) or 
you can simply create a config file called ``oauth-8-laravel.php`` in 
your ``config`` directory (option 2).

#### Option 1

Create configuration file for package using artisan command

```
$ php artisan vendor:publish --provider="Takaya030\OAuth\OAuthServiceProvider"
```

#### Option 2

Create configuration file manually in config directory ``config/oauth-8-laravel.php`` and put there code from below.

```php
<?php

return [

	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => '\\OAuth\\Common\\Storage\\Session',
	//'storage' => '\\Takaya030\\OAuth\\OAuthLaravelSession',

	/**
	 * Consumers
	 */
	'consumers' => [

		'Google' => [
			'client_id'     => '',
			'client_secret' => '',
			'scope'         => [],
		],

	]

];
```

### Credentials

Add your credentials to ``config/oauth-8-laravel.php`` (depending on which option of configuration you choose)

The `Storage` attribute is optional and defaults to `Session`. 
Other [options](https://github.com/Lusitanian/PHPoAuthLib/tree/master/src/OAuth/Common/Storage).

## Usage

### Basic usage

Just follow the steps below and you will be able to get a [service class object](https://github.com/Lusitanian/PHPoAuthLib/tree/master/src/OAuth/OAuth2/Service) with this one rule:

```php
$service = \OAuth::consumer('Google');
```

Optionally, add a second parameter with the URL which the service needs to redirect to, otherwise it will redirect to the current URL.

```php
$service = \OAuth::consumer('Google', 'http://url.to.redirect.to');
```

## Usage examples

###Google

Configuration:
Add your Google credentials to ``config/oauth-8-laravel.php``

```php
'Google' => [
    'client_id'     => 'Your Google client ID',
    'client_secret' => 'Your Google Client Secret',
    'scope'         => ['userinfo_email', 'userinfo_profile'],
],	
```

In your Controller use the following code:

```php

public function loginWithGoogle(Request $request)
{
	// get data from request
	$code = $request->get('code');
	
	// get google service
	$googleService = \OAuth::consumer('Google');
	
	// check if code is valid
	
	// if code is provided get user data and sign in
	if ( ! is_null($code))
	{
		// This was a callback request from google, get the token
		$token = $googleService->requestAccessToken($code);
		
		// Send a request with it
		$result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
		
		$message = 'Your unique Google user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
		echo $message. "<br/>";
		
		//Var_dump
		//display whole array.
		dd($result);
	}
	// if not ask for permission first
	else
	{
		// get googleService authorization
		$url = $googleService->getAuthorizationUri();
		
		// return to google login url
		return redirect((string)$url);
	}
}
```

### More usage examples:

For examples go [here](https://github.com/Lusitanian/PHPoAuthLib/tree/master/examples)

## How to use Custom Services

Google Servie added score of Datastore as a sample custom service

app/OAuth/Service/MyGoogle.php

```php
<?php

namespace App\OAuth\Service;

use \OAuth\OAuth2\Service\Google;

class MyGoogle extends Google
{
    const SCOPE_DATASTORE = 'https://www.googleapis.com/auth/datastore';
}
```

Create oauth service provider ```app/Providers/OAuthServiveProvider.php```

```php
<?php

namespace App\Providers;

/**
 * @author     Dariusz Prz?da <artdarek@gmail.com>
 * @copyright  Copyright (c) 2013
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('oauth-8-laravel.php'),
        ], 'config');
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register 'oauth'
        $this->app->singleton(\Takaya030\OAuth\OAuth::class, function ($app) {
            // create oAuth instance
            $oauth = new \Takaya030\OAuth\OAuth();

			// register custom service
			$oauth->registerService('MyGoogle', \App\OAuth\Service\MyGoogle::class);

            // return oAuth instance
            return $oauth;
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
```

Register the service provider within the ```providers``` array found in ```config/app.php```:

```php
'providers' => [
	// ...

    App\Providers\OAuthServiceProvider::class,
]
```

Configuration:
Add your custom Google credentials to ``config/oauth-8-laravel.php``

```php
'MyGoogle' => [
    'client_id'     => 'Your Google client ID',
    'client_secret' => 'Your Google Client Secret',
    'scope'         => ['userinfo_email', 'userinfo_profile'],
],	
```

You will be able to get the custom Google Service.

```php
$service = \OAuth::consumer('MyGoogle');
```
