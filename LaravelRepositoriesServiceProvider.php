<?php

namespace OkayBueno\LaravelRepositories;

use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelRepositoriesServiceProvider
 * @package OkayBueno\LaravelRepositories
 */
class LaravelRepositoriesServiceProvider extends ServiceProvider
{

    private $configPath = '/config/repositories.php';


    /**
     *
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.$this->configPath => config_path('repositories.php'),
        ], 'repositories');
    }


    /**
     *
     */
    public function register()
    {
        // merge default config
        $this->mergeConfigFrom(
            __DIR__.$this->configPath , 'repositories'
        );

        // Bind the repositories.
        $this->autoBindRepositories();
        // And generator.
        $this->registerRepositoryGenerator();
        $this->registerCriteriaGenerator();
    }


    /**
     *
     */
    private function autoBindRepositories()
    {
        // Load config parameters needed.
        $repositoriesBasePath = config( 'repositories.path' );
        $baseNamespace = rtrim( config( 'repositories.namespace' ), '\\' ) . '\\';
        $implementation = config( 'repositories.default_implementation' );
        $skipRepositories = config( 'repositories.skip' );

        $allRepos = \File::files( $repositoriesBasePath );

        foreach( $allRepos as $repo )
        {
            $interface = basename( $repo );
            if ( in_array( $interface, $skipRepositories ) ) continue;
            else
            {
                $interfaceName = str_replace( '.php', '', $interface );
                $commonName = str_replace( 'Interface', '', $interfaceName );

                $interfaceFullClassName = $baseNamespace.$interfaceName;

                $implementationFullClassName = $baseNamespace.$implementation.'\\'.$commonName;

                if ( class_exists( $implementationFullClassName ) )
                {
                    // Bind the class.
                    $this->app->bind( $interfaceFullClassName, function ( $app ) use ( $implementationFullClassName )
                    {
                        return $app->make( $implementationFullClassName );
                    });
                }
            }
        }
    }

    /**
     *
     */
    private function registerRepositoryGenerator()
    {
        $this->app->singleton('command.repository', function ($app)
        {
            return $app['OkayBueno\LaravelRepositories\Commands\MakeRepositoryCommand'];
        });

        $this->commands('command.repository');
    }



    /**
     *
     */
    private function registerCriteriaGenerator()
    {
        $this->app->singleton('command.criteria', function ($app)
        {
            return $app['OkayBueno\LaravelRepositories\Commands\MakeCriteriaCommand'];
        });

        $this->commands('command.criteria');
    }
}