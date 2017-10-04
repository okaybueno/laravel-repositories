<?php

namespace OkayBueno\Repositories\Commands;

/**
 * Class MakeRepositoryCommand
 * @package OkayBueno\Repositories\Commands
 */
class MakeRepositoryCommand extends MakeBaseCommand
{
    protected $signature = 'make:repository {model} {--implementation=}';
    protected $description = 'Create a new repository for the given model. This will create a repository interface, the implementation for Eloquent and will inject the model on it.';
    
    private $implementation;
    private $modelClassShortName;
    private $modelClassNamespace;
    private $repositoryInterfaceName;
    private $repositoryInterfaceNamespace;
    private $repositoryClassName;
    private $repositoryClassNamespace;
    
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = $this->argument('model');
        $implementation = strtolower( $this->option('implementation') );
        
        if ( class_exists( $model ) )
        {
            $supportedImplementations = array_keys( config( 'repositories.supported_implementations' ) );
            
            if ( $implementation )
            {
                if ( !in_array( $implementation, $supportedImplementations ) )
                {
                    $this->error("The implementation '$implementation' is not supported at this moment. Want me to provide support? Open an issue :).");
                    
                    return FALSE;
                }
            } else
            {
                $implementation = $this->findDefaultImplementation();
            }
            
            // Populate the properties with the right values.
            $this->populateValuesForProperties( $model, $implementation );
            
            $this->createInterface();
            $this->createRepository();
            
            $this->info('Generating autoload...');
            $this->composer->dumpAutoloads();
            $this->info('Done!');
            
            return true;
            
        }
        
        $this->error( "The '$model' model does not exist. Please check that the namespace and the class name are valid.");
        
    }
    
    /**
     * @param $model
     * @param $implementation
     */
    protected function populateValuesForProperties( $model, $implementation )
    {
        $modelClass = new \ReflectionClass( $model );
        
        $this->implementation = $implementation;
        
        $this->modelClassShortName = $modelClass->getShortName();
        $this->modelClassNamespace = $modelClass->getNamespaceName();
        
        $this->repositoryClassName = $this->modelClassShortName.'Repository';
        $this->repositoryInterfaceName = $this->repositoryClassName.'Interface';
        
        $this->repositoryInterfaceNamespace = rtrim( config( 'repositories.repository_interfaces_namespace' ), '\\' );
        $this->repositoryClassNamespace = $this->repositoryInterfaceNamespace.'\\'.ucfirst( $implementation );
    }
    
    /**
     *
     */
    protected function createRepository()
    {
        $basePath = config( 'repositories.repositories_path' ).'/'.ucfirst( $this->implementation );
        
        $classFilePath = $basePath.'/'.$this->repositoryClassName.'.php';
        
        $this->makeDirectory( $basePath );
        
        if ( !$this->filesystem->exists( $classFilePath ) )
        {
            // Read the stub and replace
            $this->filesystem->put( $classFilePath, $this->compileRepositoryStub() );
            $this->info("'".ucfirst( $this->implementation )."' implementation created successfully for '$this->modelClassShortName'.");
            $this->composer->dumpAutoloads();
        } else
        {
            $this->error("The repository '$classFilePath' already exists, so it was skipped.");
        }
    }
    
    
    /**
     *
     */
    protected function createInterface()
    {
        $repositoriesBasePath = config( 'repositories.repositories_path' );
        
        $interfaceFilePath = $repositoriesBasePath.'/'.$this->repositoryInterfaceName.'.php';
        
        $this->makeDirectory( $repositoriesBasePath );
        
        if ( !$this->filesystem->exists( $interfaceFilePath ) )
        {
            // Read the stub and replace
            $this->filesystem->put( $interfaceFilePath, $this->compileRepositoryInterfaceStub() );
            $this->info("Interface created successfully for '$this->modelClassShortName'.");
            $this->composer->dumpAutoloads();
        } else
        {
            $this->error("The interface '$this->repositoryInterfaceName' already exists, so it was skipped.");
        }
    }
    
    
    /**
     * @return MakeRepositoryCommand|string
     */
    protected function compileRepositoryInterfaceStub()
    {
        $stub = $this->filesystem->get(__DIR__ . '/../stubs/repository-interface.stub');
        
        $stub = $this->replaceInterfaceNamespace( $stub );
        $stub = $this->replaceInterfaceName( $stub );
        
        return $stub;
    }
    
    /**
     * @return mixed|MakeRepositoryCommand|string
     */
    protected function compileRepositoryStub()
    {
        $stub = $this->filesystem->get(__DIR__ . '/../stubs/repository.stub');
        
        $stub = $this->replaceInterfaceNamespace( $stub );
        $stub = $this->replaceInterfaceName( $stub );
        $stub = $this->replaceParentRepositoryClassNamespaceAndName( $stub );
        $stub = $this->replaceRepositoryClassNamespaceAndName( $stub );
        $stub = $this->replaceModelClassNamespaceAndName( $stub );
        
        return $stub;
    }
    
    /**
     * @param $stub
     * @return $this
     */
    private function replaceInterfaceNamespace( $stub )
    {
        return str_replace('{{repositoryInterfaceNamespace}}', $this->repositoryInterfaceNamespace, $stub);
    }
    
    /**
     * @param $stub
     * @return mixed
     */
    private function replaceInterfaceName( $stub )
    {
        return str_replace('{{repositoryInterfaceName}}', $this->repositoryInterfaceName, $stub);
    }
    
    
    /**
     * @param $stub
     * @return mixed
     */
    private function replaceParentRepositoryClassNamespaceAndName( $stub )
    {
        $implementations = config( 'repositories.supported_implementations' );
        
        $parentClassImplementation = $implementations[ $this->implementation ];
        
        $reflex = new \ReflectionClass($parentClassImplementation);
        
        $stub = str_replace('{{parentRepositoryClassNamespace}}', $reflex->getNamespaceName(), $stub);
        
        return str_replace('{{parentRepositoryClassName}}', $reflex->getShortName(), $stub);
    }
    
    /**
     * @param $stub
     * @return mixed
     */
    private function replaceRepositoryClassNamespaceAndName( $stub )
    {
        $stub = str_replace('{{repositoryClassName}}', $this->repositoryClassName, $stub);
        
        return str_replace('{{repositoryClassNamespace}}', $this->repositoryClassNamespace, $stub);
    }
    
    
    /**
     * @param $stub
     * @return mixed
     */
    private function replaceModelClassNamespaceAndName( $stub )
    {
        $stub = str_replace('{{modelName}}', $this->modelClassShortName, $stub);
        
        return  str_replace('{{modelNamespace}}', $this->modelClassNamespace, $stub);
    }
    
}