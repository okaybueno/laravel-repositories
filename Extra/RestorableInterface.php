<?php

namespace OkayBueno\LaravelRepositories\Extra;

/**
 * Interface RestorableInterface
 * @package OkayBueno\LaravelRepositories\Extra
 */
interface RestorableInterface
{

    /**
     * Restores a model (or mor than one) by the given field and value.
     *
     * @param $value mixed Value to filter by.
     * @param string $field Column in the DB used for the filter.
     * @return void
     */
    public function restore( $value, $field = 'id' );
}