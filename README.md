XenforoBridge
=============

Simple to use XenForo bridge libary. The goal of this package is to allow developer to easily integrate their existing/new application with XenForo Forum Platfrom. This package is still heavily underdevelopment so use with caution. I have also included a ServiceProvider to use within a Laravel application.

Installation
------------

Install the XenforoBridge package with Composer.

```json
{
    "require": {
        "urb/xenforobridge": "dev-master"
    }
}
```

To install XenforoBridge into Laravel 4 simple add the following service provider to your 'app/config/app.php' in the 'providers' array:

```php
'providers' => array(
		'XenforoBridge\XenforoBridgeServiceProvider',
)

```
Then publish the config file with 'php artisan config:publish urb/xenforobridge'. This will add the file 'app/config/packages/urb/xenforobridge/config.php'. This is where you will place the needed configurations to use the Xenforo Bridge.

Within this config file you will need to supply the full directory path to your XenForo installation and the base url path like the example below

```php
return array(
		'xenforo_directory_path' => '/var/www/html/public/forums',
		'xenforo_base_url_path'  => '//example.com/forums/', //Default '/'
	);
```

Credits
-------

Special thanks to [VinceG](https://github.com/VinceG), the idea and much of my work is based on his package [xenforo-sdk](https://github.com/VinceG/xenforo-sdk) which was previously integrated within an ongoing project.
