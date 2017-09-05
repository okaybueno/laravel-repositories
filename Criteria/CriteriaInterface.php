<?php

namespace OkayBueno\Repositories\Criteria;

/**
 * Interface CriteriaInterface
 * @package OkayBueno\Repositories\Criteria
 */
interface CriteriaInterface
{
    /**
     * The criteria to be applied must go inside this method.
     *
     * @param mixed $queryBuilder Current query builder.
     * @return mixed $queryBuilder Current instance of the query builder with the criteria appplied.
     */
    public function apply( $queryBuilder );
}