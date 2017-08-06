<?php

namespace OkayBueno\LaravelRepositories\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Class MakeCriteriaCommand
 * @package OkayBueno\LaravelRepositories\Commands
 */
class MakeCriteriaCommand extends Command
{

    protected $signature = 'make:criteria {criteria-name} {--folder=}';
    protected $description = 'Create a new criteria.';

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
        $criteriaName = $this->argument('criteria-name');
        $folderName = $this->option('folder');

        $this->createCriteria( $criteriaName, $folderName );

        $this->info('Generating autoload...');
        $this->composer->dumpAutoloads();
        $this->info('Done!');
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
     * @param $criteriaName
     * @param null $folder
     * @param string $implementation
     */
    protected function createCriteria( $criteriaName, $folder = NULL, $implementation = 'Eloquent' )
    {
        $basePath = config( 'repositories.criteria_path' ).'/'.$implementation;

        if ( $folder ) $basePath .= '/'.$folder;

        $this->makeDirectory( $basePath );

        $criteriaFilePath = $basePath .'/'.$criteriaName.'.php';

        if ( !$this->filesystem->exists( $criteriaFilePath ) )
        {
            // Read the stub and replace
            $this->filesystem->put( $criteriaFilePath, $this->compileCriteriaStub( $criteriaName, $folder, $implementation ) );
            $this->info("Criteria '$criteriaName' created successfully in '$criteriaFilePath'.");
            $this->composer->dumpAutoloads();
        } else
        {
            $this->error("The criteria '$criteriaName' already exists in '$criteriaFilePath.");
        }
    }


    /**
     * @param $criteriaName
     * @param null $folder
     * @param string $implementation
     * @return mixed|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function compileCriteriaStub( $criteriaName, $folder = NULL, $implementation = 'Eloquent' )
    {
        $stub = $this->filesystem->get(__DIR__ . '/../stubs/eloquent-criteria.stub');

        $stub = $this->replaceCriteriaNamespace( $stub, $folder, $implementation );
        $stub = $this->replaceCriteriaName( $stub, $criteriaName );

        return $stub;
    }


    /**
     * @param $stub
     * @param null $folder
     * @param string $implementation
     * @return mixed
     */
    private function replaceCriteriaNamespace( $stub, $folder = NULL, $implementation = 'Eloquent' )
    {
        $interfaceNamespace = rtrim( config( 'repositories.criteria_namespace' ), '\\' );

        if ( $implementation ) $interfaceNamespace .= '\\' . $implementation;
        if ( $folder ) $interfaceNamespace .= '\\' . $folder;

        return str_replace('{{criteriaNamespace}}', $interfaceNamespace, $stub);
    }

    /**
     * @param $stub
     * @param $criteriaName
     * @return mixed
     */
    private function replaceCriteriaName( $stub, $criteriaName )
    {
        return str_replace('{{criteriaClassName}}', $criteriaName, $stub);
    }

}