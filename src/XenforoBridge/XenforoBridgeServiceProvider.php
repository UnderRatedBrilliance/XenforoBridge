<?php namespace XenforoBridge;

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

		$this->app['xenforobridge'] = $this->app->share(
			function($app) {
				$app['XenforoBridge.loaded'] = true;

				$xenforoDir = config('xenforobridge.xenforo_directory_path');
				$xenforoBaseUrl = config('xenforobridge.xenforo_base_url_path');
				
				return new XenforoBridge($xenforoDir, $xenforoBaseUrl);
			}
		);

		$this->app->alias('xenforobridge', 'XenforoBridge\XenforoBridge');
	}
        
    public function boot()
    {
    	$app = $this->app;

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
		return array('xenforobridge');
	}

}
