<?php

namespace OkayBueno\Repositories;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Class RepositoriesServiceProvider
 * @package OkayBueno\Repositories
 */
class +RepositoryServiceProvider extends ServiceProvider
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
        
        // And generators.
        $this->registerRepositoryGenerator();
        $this->registerCriteriaGenerator();
    }
    
    
    /**
     *
     */
    private function autoBindRepositories()
    {
        // Load config parameters needed.
        $repositoriesBasePath = config( 'repositories.repositories_path' );
        $baseNamespace = rtrim( config( 'repositories.repository_interfaces_namespace' ), '\\' ) . '\\';
        $skipRepositories = config( 'repositories.skip' );
        $implementationBindings = config( 'repositories.bindings' );
        $defaultImplementation = $this->findDefaultImplementation( $implementationBindings );
        
        if ( \File::exists( $repositoriesBasePath ) )
        {
            $allRepos = \File::files( $repositoriesBasePath );
            
            foreach( $allRepos as $repo )
            {
                $implementation = $defaultImplementation;
                $interfaceName = pathinfo( $repo, PATHINFO_FILENAME );
                if ( in_array( $interfaceName, $skipRepositories ) ) continue;
                else
                {
                    $commonName = str_replace( 'Interface', '', $interfaceName );
                    $interfaceFullClassName = $baseNamespace.$interfaceName;
                    
                    foreach( $implementationBindings as $engine => $bindRepositories )
                    {
                        if ( $bindRepositories === 'default' ) continue;
                        else if ( in_array( $interfaceName, $bindRepositories ) )
                        {
                            $implementation = $engine;
                            break;
                        }
                    }
                    
                    $fullClassName = $baseNamespace.ucfirst( Str::camel( $implementation ) ).'\\'.$commonName;
                    
                    if ( class_exists( $fullClassName ) )
                    {
                        // Bind the class.
                        $this->app->bind( $interfaceFullClassName, function ( $app ) use ( $fullClassName )
                        {
                            return $app->make( $fullClassName );
                        });
                    }
                }
            }
        }
        
    }
    
    /**
     * @param $implementations
     * @return array|mixed|string
     */
    private function findDefaultImplementation( $implementations )
    {
        $filtered = array_filter( $implementations, function( $k ) {
            return $k === 'default';
        });
        
        $default = array_keys($filtered);
        $default = is_array( $default ) ? $default[0] : $default;
        
        return $default ? $default : 'eloquent';
    }
    
    /**
     *
     */
    private function registerRepositoryGenerator()
    {
        $this->app->singleton('command.repository', function ($app)
        {
            return $app['OkayBueno\Repositories\Commands\MakeRepositoryCommand'];
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
            return $app['OkayBueno\Repositories\Commands\MakeCriteriaCommand'];
        });
        
        $this->commands('command.criteria');
    }
}
