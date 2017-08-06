<?php

namespace OkayBueno\LaravelRepositories\Criteria\src;

use OkayBueno\LaravelRepositories\Criteria\CriteriaInterface;

/**
 * Class FilterByColumns
 * @package OkayBueno\LaravelRepositories\Criteria\src
 */
class FilterByColumns implements CriteriaInterface
{
    private $filter;


    /**
     * @param array $filter
     */
    public function __construct( array $filter )
    {
        $this->filter = $filter;
    }


    /**
     * @param $modelOrBuilder
     * @return mixed
     */
    public function apply( $modelOrBuilder )
    {
        foreach( $this->filter as $filter )
        {
            $nElements = count( $filter );

            // Apply filter based on then number of items in the array.
            if ( $nElements === 2 )
            {
                $column = $filter[0];
                $operation = NULL;
                $value = $filter[1];
            } else if ( $nElements === 3 )
            {
                $column = $filter[0];
                $operation = $filter[1];
                $value = $filter[2];
            } else continue;

            $modelOrBuilder = $this->applyFilter( $modelOrBuilder, $column, $value, $operation );
        }

        return $modelOrBuilder;
    }


    /**
     * @param $modelOrBuilder
     * @param $column
     * @param $value
     * @param null $operation
     */
    private function applyFilter( $modelOrBuilder, $column, $value, $operation = NULL )
    {
        if ( is_null( $operation ) ) $modelOrBuilder = $modelOrBuilder->where( $column, $value );
        else $modelOrBuilder = $modelOrBuilder->where( $column, $operation, $value );

        return $modelOrBuilder;
    }
}