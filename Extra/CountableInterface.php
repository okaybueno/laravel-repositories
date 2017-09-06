<?php

namespace OkayBueno\Repositories\Extra;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface CountableInterface
 * @package OkayBueno\Repositories
 */
interface CountableInterface
{
    /**
     * Increments the given counter in the given model with the given amount. By default: 1.
     *
     * @param Model $model
     * @param $counter
     * @param int $amount
     * @return mixed
     */
    public function incrementCounter( Model $model, $counter, $amount = 1 );


    /**
     * Decrements the given counter in the given model with the given amount. If allow negative is disabled, then no
     * negative values are allowed and the counter will be set to 0 if this happens.
     *
     * @param Model $model
     * @param $counter
     * @param int $amount
     * @param bool|FALSE $allowNegative
     * @return mixed
     */
    public function decrementCounter( Model $model, $counter, $amount = 1, $allowNegative = FALSE );
}