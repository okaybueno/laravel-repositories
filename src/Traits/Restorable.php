<?php

namespace OkayBueno\Repositories\src\Traits;

/**
 * Class Restorable
 * @package OkayBueno\Repositories\Traits
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