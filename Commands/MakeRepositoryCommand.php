<?php

namespace OkayBueno\LaravelRepositories\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Class MakeRepositoryCommand
 * @package OkayBueno\LaravelRepositories\Commands
 */
class MakeRepositoryCommand extends Command
{

    protected $signature = 'make:repository {model-name}';
    protected $description = 'Create a new repository for the given model. This will create a repository interface, the implementation for Eloquent and will inject the model on it.';

    protected $filesystem;
    private $composer;


    /**
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    )
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $modelName = $this->argument('model-name');
        $defaultImplementation = config( 'repositories.default_implementation' );
        $modelsNamespace = rtrim( config( 'repositories.models_namespace' ), '\\' ) . '\\';

        $modelToBeInjected = $modelsNamespace.$modelName;

        if ( class_exists( $modelToBeInjected ) )
        {
            $this->createInterface( $modelName );
            $this->createRepository( $modelName, $defaultImplementation );

            $this->info('Generating autoload...');
            $this->composer->dumpAutoloads();
            $this->info('Done!');

        } else
        {
            $this->error( "The '$modelName' ('$modelToBeInjected') model does not exist. Please check that the namespace and the class name are valid.");
        }
    }


    /**
     * @param $path
     */
    protected function makeDirectory( $path )
    {
        if ( !$this->filesystem->isDirectory( $path ) )
        {
            $this->filesystem->makeDirectory( $path, 0775, true, true);
        }
    }

    /**
     * @param $modelName
     * @param string $implementation
     */
    protected function createRepository( $modelName, $implementation = 'Eloquent' )
    {
        $className = $modelName.'Repository';
        $interfaceName = $modelName.'RepositoryInterface';

        $basePath = config( 'repositories.path' ).'/'.$implementation;

        $classFilePath = $basePath.'/'.$className.'.php';

        $this->makeDirectory( $basePath );

        if ( !$this->filesystem->exists( $classFilePath ) )
        {
            // Read the stub and replace
            $this->filesystem->put( $classFilePath, $this->compileRepositoryEloquentStub( $interfaceName, $className, $modelName ) );
            $this->info("'$implementation' implementation created successfully for '$modelName'.");
            $this->composer->dumpAutoloads();
        } else
        {
            $this->error("The repository '$basePath' already exists, so it was skipped.");
        }
    }

    /**
     * @param $modelName
     */
    protected function createInterface( $modelName )
    {
        $interfaceName = $modelName.'RepositoryInterface';

        $repositoriesBasePath = config( 'repositories.path' );

        $interfaceFilePath = $repositoriesBasePath.'/'.$interfaceName.'.php';

        $this->makeDirectory( $repositoriesBasePath );

        if ( !$this->filesystem->exists( $interfaceFilePath ) )
        {
            // Read the stub and replace
            $this->filesystem->put( $interfaceFilePath, $this->compileRepositoryInterfaceStub( $interfaceName ) );
            $this->info("Interface created successfully for '$modelName'.");
            $this->composer->dumpAutoloads();
        } else
        {
            $this->error("The interface '$interfaceName' already exists, so it was skipped.");
        }
    }


    /**
     * @param $interfaceName
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function compileRepositoryInterfaceStub( $interfaceName )
    {
        $stub = $this->filesystem->get(__DIR__ . '/../stubs/repository-interface.stub');

        $stub = $this->replaceInterfaceNamespace( $stub );
        $stub = $this->replaceInterfaceName( $stub, $interfaceName );

        return $stub;
    }

    /**
     * @param $interfaceName
     * @param $eloquentImplementationClassName
     * @param $modelName
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function compileRepositoryEloquentStub( $interfaceName, $eloquentImplementationClassName, $modelName )
    {
        $stub = $this->filesystem->get(__DIR__ . '/../stubs/eloquent-repository.stub');

        $stub = $this->replaceInterfaceNamespace( $stub );
        $stub = $this->replaceInterfaceName( $stub, $interfaceName );
        $stub = $this->replaceEloquentImplementationNamespace( $stub );
        $stub = $this->replaceEloquentImplementationClassName( $stub, $eloquentImplementationClassName );
        $stub = $this->replaceModelNamespace( $stub );
        $stub = $this->replaceModelName( $stub, $modelName );

        return $stub;
    }


    /**
     * @param $stub
     * @return $this
     */
    private function replaceInterfaceNamespace( $stub )
    {
        $interfaceNamespace = rtrim( config( 'repositories.namespace' ), '\\' );

        return str_replace('{{interfaceNamespace}}', $interfaceNamespace, $stub);
    }

    /**
     * @param $stub
     * @param $interfaceName
     * @return $this
     */
    private function replaceInterfaceName( $stub, $interfaceName )
    {
        return str_replace('{{interfaceName}}', $interfaceName, $stub);
    }


    /**
     * @param $stub
     * @return $this
     */
    private function replaceEloquentImplementationNamespace( $stub )
    {
        $eloquentImplementationNamespace = rtrim( config( 'repositories.namespace' ), '\\' ) . '\\Eloquent';
        return str_replace('{{eloquentImplementationNamespace}}', $eloquentImplementationNamespace, $stub);
    }


    /**
     * @param $stub
     * @return $this
     */
    private function replaceModelNamespace( $stub )
    {
        $modelNamespace = rtrim( config( 'repositories.models_namespace' ), '\\' );

        return str_replace('{{modelNamespace}}', $modelNamespace, $stub);
    }

    /**
     * @param $stub
     * @param $modelName
     * @return $this
     */
    private function replaceModelName( $stub, $modelName )
    {
        return str_replace('{{modelName}}', $modelName, $stub);
    }

    /**
     * @param $stub
     * @param $eloquentImplementationClassName
     * @return $this
     */
    private function replaceEloquentImplementationClassName( $stub, $eloquentImplementationClassName )
    {
        return str_replace('{{eloquentImplementationClassName}}', $eloquentImplementationClassName, $stub);
    }


}