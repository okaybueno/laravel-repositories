<?php

namespace OkayBueno\Repositories\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Class MakeCriteriaCommand
 * @package OkayBueno\Repositories\Commands
 */
class MakeCriteriaCommand extends MakeBaseCommand
{

    protected $signature = 'make:criteria {criteria} {--implementation}';
    protected $description = 'Create a new criteria.';

    private $implementation;
    private $criteriaClassName;
    private $criteriaClassNamespace;
    private $criteriaFolder;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $criteria = $this->argument('criteria');
        $implementation = strtolower( $this->option('implementation') );

        $this->populateValuesForProperties( $criteria, $implementation );
        $this->createCriteria();

        $this->info('Generating autoload...');
        $this->composer->dumpAutoloads();
        $this->info('Done!');
    }


    /**
     * @param $criteria
     * @param $implementation
     */
    protected function populateValuesForProperties( $criteria, $implementation )
    {
        $this->implementation = $implementation ? $implementation : $this->findDefaultImplementation();

        $criteriaNameForFolder = str_replace('\\', '/', $criteria );
        $this->criteriaFolder = ucfirst( $this->implementation );

        $folder = pathinfo( $criteriaNameForFolder , PATHINFO_DIRNAME );;
        if ( $folder ) $this->criteriaFolder .= '/'.$folder;

        $this->criteriaClassName = pathinfo( $criteriaNameForFolder , PATHINFO_FILENAME );
        $this->criteriaClassNamespace = rtrim( config( 'repositories.criterias_namespace' ), '\\' ) . '\\' . str_replace( '/', '\\', $this->criteriaFolder );
    }

    /**
     *
     */
    protected function createCriteria()
    {
        $basePath = config( 'repositories.criterias_path' );

        if ( $this->criteriaFolder ) $basePath .= '/'.$this->criteriaFolder;

        $this->makeDirectory( $basePath );

        $criteriaFilePath = $basePath .'/'.$this->criteriaClassName.'.php';

        if ( !$this->filesystem->exists( $criteriaFilePath ) )
        {
            // Read the stub and replace
            $this->filesystem->put( $criteriaFilePath, $this->compileCriteriaStub() );
            $this->info("Criteria '$this->criteriaClassName' created successfully in '$criteriaFilePath'.");
            $this->composer->dumpAutoloads();
        } else
        {
            $this->error("The criteria '$this->criteriaClassName' already exists in '$criteriaFilePath.");
        }
    }


    /**
     * @return mixed|string
     */
    protected function compileCriteriaStub( )
    {
        $stub = $this->filesystem->get(__DIR__ . '/../stubs/eloquent-criteria.stub');

        $stub = $this->replaceCriteriaNamespace( $stub );
        $stub = $this->replaceCriteriaName( $stub );

        return $stub;
    }


    /**
     * @param $stub
     * @return mixed
     */
    private function replaceCriteriaNamespace( $stub )
    {
        return str_replace('{{criteriaNamespace}}', $this->criteriaClassNamespace, $stub);
    }

    /**
     * @param $stub
     * @return mixed
     */
    private function replaceCriteriaName( $stub )
    {
        return str_replace('{{criteriaClassName}}', $this->criteriaClassName, $stub);
    }

}