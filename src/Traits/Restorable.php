<?php

namespace OkayBueno\LaravelRepositories\src\Traits;

/**
 * Class Restorable
 * @package OkayBueno\LaravelRepositories\Traits
 */
trait Restorable
{

    /**
     * @param $value
     * @param string $field
     */
    public function restore( $value, $field = 'id' )
    {
        $this->model->where( $field, $value )->restore();
    }
}