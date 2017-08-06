<?php

namespace OkayBueno\LaravelRepositories\src\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Countable
 * @package OkayBueno\LaravelRepositories\Traits
 */
trait Countable
{

    /**
     * @param Model $model
     * @param $counter
     * @param int $amount
     * @return Model
     */
    public function incrementCounter( Model $model, $counter, $amount = 1 )
    {
        $amount = $this->getAmount( $amount );

        if ( $amount ) $model->increment( $counter, $amount );
        else $model->increment( $counter );

        return $model;
    }


    /**
     * @param Model $model
     * @param $counter
     * @param int $amount
     * @param bool|FALSE $allowNegative
     * @return Model
     */
    public function decrementCounter( Model $model, $counter, $amount = 1, $allowNegative = FALSE )
    {
        $amount = $this->getAmount( $amount, 1 );

        $currentValue = $model->{$counter};
        $final = $model->{$counter} - $amount;

        if ( !$allowNegative )
        {
            if ( $currentValue && $final < 0 )
            {
                $model->{$counter} = 0;
                $model->save();
            }

        } else $model->decrement( $counter, $amount );

        return $model;
    }


    /**
     * @param $amount
     * @param null $default
     * @return int|null
     */
    private function getAmount( $amount, $default = NULL )
    {
        return is_int( $amount ) && $amount > 1 ? $amount : $default;
    }
}