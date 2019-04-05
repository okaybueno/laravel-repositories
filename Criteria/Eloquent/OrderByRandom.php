<?php

namespace OkayBueno\Repositories\Criteria\Eloquent;

use OkayBueno\Repositories\Criteria\CriteriaInterface;

/**
 * Class OrderByRandom
 * @package OkayBueno\Repositories\Criteria\Eloquent
 */
class OrderByRandom implements CriteriaInterface
{
    /**
     * @param mixed $queryBuilder
     * @return mixed
     */
    public function apply( $queryBuilder )
    {
        return $queryBuilder->inRandomOrder();
    }

}
