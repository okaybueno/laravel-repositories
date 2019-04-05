<?php

namespace OkayBueno\Repositories\Criteria\Eloquent;

use OkayBueno\Repositories\Criteria\CriteriaInterface;

/**
 * Class WhereNotIn
 * @package OkayBueno\Repositories\Criteria\Eloquent
 */
class WhereNotIn implements CriteriaInterface
{
    protected $field = NULL;
    protected $list = [];

    /**
     * WhereIn constructor.
     * @param $field
     * @param array $list
     */
    public function __construct( $field, array $list )
    {
        $this->field = $field;
        $this->list = $list;
    }

    /**
     * @param mixed $queryBuilder
     * @return mixed
     */
    public function apply( $queryBuilder )
    {
        // Do something with the query builder and return it.
        return $queryBuilder->whereNotIn( $this->field, $this->list );
    }

}
