<?php

namespace OkayBueno\Repositories\Criteria\Eloquent;

use OkayBueno\Repositories\Criteria\CriteriaInterface;

/**
 * Class With
 * @package OkayBueno\Repositories\Criteria\Eloquent
 */
class With implements CriteriaInterface
{

    protected $with = NULL;

    /**
     * With constructor.
     * @param array $with
     */
    public function __construct( array $with  = [] )
    {
        $this->with = $with;
    }

    /**
    * @param mixed $queryBuilder
    * @return mixed
    */
    public function apply( $queryBuilder )
    {
        // Do something with the query builder and return it.
        if ( $this->with ) $queryBuilder->with( $this->with );

        return $queryBuilder;
    }

}
