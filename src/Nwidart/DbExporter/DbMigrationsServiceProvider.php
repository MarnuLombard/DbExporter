<?php namespace Nwidart\DbExporter;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class DbMigrationsServiceProvider extends ServiceProvider {

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
      /*$this->package('nwidart/db-exporter');*/

	    // As per BarryVDH :

	    // Is it possible to register the config?
	    if (method_exists($this->app['config'], 'package')) {
		    $this->app['config']->package('nwidart/db-exporter', __DIR__ . '/../../config');
	    } else {
		    // Load the config for now..
		    $config = $this->app['files']->getRequire(__DIR__ .'/../../config/config.php');
		    $this->app['config']->set('nwidart/db-exporter::config', $config);
	    }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app['DbMigrations'] = $this->app->share(function()
        {
            $connType = Config::get('database.default');
            $database = Config::get('database.connections.' .$connType );
            return new DbMigrations($database);
        });

        $this->app->booting(function()
            {
                $loader = AliasLoader::getInstance();
                $loader->alias('DbMigrations', 'Nwidart\DbExporter\Facades\DbMigrations');
            });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('DbMigrations');
    }

}
