<?php namespace Urb\XenforoBridge;

use Illuminate\Support\ServiceProvider;

class XenforoBridgeServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        $this->app->singleton(XenforoBridge::class, function($app) {
            //Set Bridge loaded to true
            $app['XenforoBridge.loaded'] = true;

            $xenforoDir = config('xenforobridge.xenforo_directory_path');
            $xenforoBaseUrl = config('xenforobridge.xenforo_base_url_path');

            return new XenforoBridge($xenforoDir, $xenforoBaseUrl);
        });
    }
        
    public function boot()
    {
    	$configPath = __DIR__ .'/../config/xenforobridge.php';
    	$this->publishes([$configPath => config_path('xenforobridge.php')], 'config');


    	if(config('xenforobridge.use_xenforo_auth') === true)
        {
            \Auth::extend('xenforo',function($app) {

                return new XenforoGuard($app->make(XenforoBridge::class));
            });
        }
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('xenforobridge', XenforoBridge::class);
	}

}
