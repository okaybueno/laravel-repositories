<?php

namespace OkayBueno\Repositories\Criteria\Eloquent;

use OkayBueno\Repositories\Criteria\CriteriaInterface;

/**
 * Class WhereRelationFieldIn
 * @package OkayBueno\Repositories\Criteria\Eloquent
 */
class WhereRelationFieldIn implements CriteriaInterface
{
    protected $list = [];
    protected $relationName;
    protected $field = 'id';

    /**
     * WhereMoodsIn constructor.
     * @param $relationName
     * @param array $list
     * @param string $field
     */
    public function __construct(  $relationName, array $list, $field = 'id' )
    {
        $this->relationName = $relationName;
        $this->list = $list;
        $this->field = $field;
    }

    /**
     * @param mixed $queryBuilder
     * @return mixed
     */
    public function apply( $queryBuilder )
    {
        // Do something with the query builder and return it.
        return $queryBuilder->whereHas( $this->relationName, function ($query) {
            $query->whereIn( $this->field, $this->list);
        });
    }

}
