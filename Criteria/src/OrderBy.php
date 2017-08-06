<?php

namespace OkayBueno\LaravelRepositories\Criteria\src;

use OkayBueno\LaravelRepositories\Criteria\CriteriaInterface;

/**
 * Class OrderBy
 * @package OkayBueno\LaravelRepositories\Criteria\src
 */
class OrderBy implements CriteriaInterface
{
    protected $orderBy;
    protected $direction;


    /**
     * @param $orderBy
     * @param string $direction
     */
    public function __construct( $orderBy, $direction = 'ASC' )
    {
        $this->orderBy = $orderBy;
        $this->direction = $direction;
    }


    /**
     * @param mixed $queryBuilder
     * @return mixed
     */
    public function apply( $queryBuilder )
    {
        return $queryBuilder->orderBy( $this->orderBy, $this->direction );
    }


}