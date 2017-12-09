XenforoBridge
=============

Simple to use XenForo bridge library. The goal of this package is to allow developer to easily integrate their existing/new application with XenForo Forum Platfrom. This package is still heavily underdevelopment so use with caution. I have also included a ServiceProvider to use within a Laravel application.

If you would like to see this package in action we are currently using for [AstronomyConnect.com - ItemHub Database](http://astronomyconnect.com/items). This is a Laravel 5 app with Xenforo sitting within the public folder. Templates have been designed and implemented in Xenforo and are being rendered by this bridge instead of using Blade.

Installation
------------

Install the XenforoBridge package with Composer by adding the folowing to your composer.json file.

```json
{
    "require": {
        "urb/xenforobridge": "dev-master"
    }
}
```
Or by using the composer require command

```
composer require urb/xenforobridge:dev-master
```

To install XenforoBridge into Laravel 5 simple add the following service provider to your 'config/app.php' in the 'providers' array:

```php
'providers' => array(
		'Urb\XenforoBridge\XenforoBridgeServiceProvider::class',
)

```

Then publish the config file with

```
php artisan vendor:publish
```

This will add the file 'config/xenforobridge.php'. This is where you will place the needed configurations to use the Xenforo Bridge.

Within this config file you will need to supply the full directory path to your XenForo installation and the base url path like the example below

```php
return array(
		'xenforo_directory_path' => '/var/www/html/public/forums',
		'xenforo_base_url_path'  => '//example.com/forums/', //Default '/'
	);
```

Installing Middleware
---------------------
To install Middleware you wil need to open up the app\Http\Kernel.php and the following middleware to either global middleware array
or the routeMiddleware array.

Here is an example adding to the routeMiddleware array

```php
protected $routeMiddleware = [
		'xen.auth' => 'Urb\XenforoBridge\Middleware\XenAuthMiddleware',
		'xen.auth.admin' => 'Urb\XenforoBridge\Middleware\XenAuthAdminMiddleware',
	];

```

You can then use them in your routes like so

```php
Route::get('/example', ['middleware' => 'xen.auth',function(){
	//Do stuff
}]);
```

or you can use them in your controllers themselves

```php
class SampleController extends Controller {


    function __construct()
    {

        $this->middleware('xen.auth');
    }

}

```

For more information on Middleware development an installation check out [Laravel Docs - Middleware](http://laravel.com/docs/5.1/middleware)

Credits
-------

Special thanks to [VinceG](https://github.com/VinceG), the idea and much of my work is based on his package [xenforo-sdk](https://github.com/VinceG/xenforo-sdk) which was previously integrated within an ongoing project.
