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

		$this->app['xenforobridge'] = $this->app->singleton(
			function($app) {
				$app['XenforoBridge.loaded'] = true;

				$xenforoDir = config('xenforobridge.xenforo_directory_path');
				$xenforoBaseUrl = config('xenforobridge.xenforo_base_url_path');
				
				return new XenforoBridge($xenforoDir, $xenforoBaseUrl);
			}
		);

		$this->app->alias('xenforobridge', 'Urb\XenforoBridge\XenforoBridge');
	}

	public function register()
    {
        $this->app->singleton(XenforoBridge::class, function($app) {
            //Set Bridge loaded to true
            $app['XenforoBridge.loaded'] = true;

            $xenforoDir = config('xenforobridge.xenforo_directory_path');
            $xenforoBaseUrl = config('xenforobridge.xenforo_base_url_path');

            return new XenforoBridge($xenforoDir, $xenforoBaseUrl);
        });

        //Set XenforoBridge Alias
        $this->app->alias('xenforobridge', XenforoBridge::class);
    }
        
    public function boot()
    {
    	$configPath = __DIR__ .'/../config/xenforobridge.php';
    	$this->publishes([$configPath => config_path('xenforobridge.php')], 'config');
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
