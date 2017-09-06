<?php

namespace OkayBueno\Repositories\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Class MakeBaseCommand
 * @package OkayBueno\Repositories\Commands
 */
class MakeBaseCommand extends Command
{
    protected $filesystem;
    protected $composer;


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
     * @return array|mixed|string
     */
    protected function findDefaultImplementation()
    {
        $implementationBindings = config( 'repositories.bindings' );

        $filtered = array_filter( $implementationBindings, function( $k ) {
            return $k === 'default';
        });

        $default = array_keys($filtered);
        $default = is_array( $default ) ? $default[0] : $default;

        return $default ? $default : 'eloquent';
    }

}