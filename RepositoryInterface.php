<?php

namespace OkayBueno\Repositories;

use OkayBueno\Repositories\Criteria\CriteriaInterface;

/**
 * Interface RepositoryInterface
 * @package OkayBueno\Repositories
 */
interface RepositoryInterface
{
    /**
     * Finds one item by the provided field.
     *
     * @param $value mixed Value used for the filter. If NULL passed then it will take ONLY the criteria.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @param array $columns Columns to retrieve with the object.
     * @return mixed Model|NULL An Eloquent object when there is a result, NULL when there are no matches.
     */
    public function findOneBy( $value = NULL, $field = 'id', array $columns = ['*'] );

    /**
     * Finds ALL items the repository abstract without any kind of filter.
     *
     * @param array $columns Columns to retrieve with the objects.
     * @return mixed Collection Laravel Eloquent's Collection that may or may not be empty.
     */
    public function findAll( array $columns = ['*'] );

    /**
     * Finds ALL items by the provided field. If NULL specified for the first 2 parameters, then it will take ONLY the
     * criteria.
     *
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @param array $columns Columns to retrieve with the objects.
     * @return mixed Collection Laravel Eloquent's Collection that may or may not be empty.
     */
    public function findAllBy( $value = NULL, $field = NULL, array $columns = ['*'] );

    /**
     * Finds ALL the items in the repository where the given field is inside the given values.
     *
     * @param array $value mixed Array of values used for the filter.
     * @param string $field Field on the database that you will filter by.
     * @param array $columns Columns to retrieve with the objects.
     * @return mixed Collection Laravel Eloquent's Collection that may or may not be empty.
     */
    public function findAllWhereIn( array $value, $field,  array $columns = ['*'] );

    /**
     * Allows you to eager-load entity relationships when retrieving entities, either with or without criterias.
     *
     * @param array|string $relations Relations to eager-load along with the entities.
     * @return mixed The current repository object instance.
     */
    public function with( $relations );

    /**
     * Adds a criteria to the query.
     *
     * @param CriteriaInterface $criteria Object that declares and implements the criteria used.
     * @return mixed The current repository object instance.
     */
    public function addCriteria( CriteriaInterface $criteria );

    /**
     * Skips the current criteria (all of them). Useful when you don't want to reset the object but just not use the
     * filters applied so far.
     *
     * @param bool|TRUE $status If you want to skip the criteria or not.
     * @return mixed The current repository object instance.
     */
    public function skipCriteria( $status = TRUE );

    /**
     * Returns a Paginator that based on the criteria or filters given.
     *
     * @param int $perPage Number of results to return per page.
     * @param array $columns Columns to retrieve with the objects.
     * @return Paginator object with the results and the paginator.
     */
    public function paginate( $perPage, array $columns = ['*'] );

    /**
     * Allows you to set the current page with using the paginator. Useful when you want to overwrite the $_GET['page']
     * parameter and retrieve a specific page directly without using HTTP.
     *
     * @param int $page The page you want to retrieve.
     * @return mixed The current repository object instance.
     */
    public function setCurrentPage( $page );

    /**
     * Creates a new entity of the entity type the repository handles, given certain data.
     *
     * @param array $data Data the entity will have.
     * @return mixed Model|NULL An Eloquent object when the entity was created, NULL in case of error.
     */
    public function create( array $data );

    /**
     * Updates as many entities as the filter matches with the given $data.
     *
     * @param array $data Fields & new values to be updated on the entity/entities.
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @return mixed Model|NULL|integer An Eloquent object representing the updated entity, a number of entities updated if mass updating,
     * or NULL in case of error.
     */
    public function updateBy( array $data, $value = NULL, $field = 'id' );

    /**
     * Removes as many entities as the filter matches. If softdelete is applied, then they will be soft-deleted.
     * Criteria is applied as well, so please be careful with it.
     *
     * @param $value
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @return boolean TRUE It will always return TRUE.
     */
    public function delete( $value = NULL, $field = 'id' );

    /**
     * @return int number of records matching the criteria (or total amount of records).
     */
    public function count();

    /**
     * Resets the current scope of the repository. That is: clean the criteria, and all other properties that could have
     * been modified, like current page, etc.
     *
     * @return mixed The current repository object instance.
     */
    public function resetScope();

    /**
     * Permanently removes a record (or set of records) from the database.
     * Criteria is applied as well, so please be careful with it.
     *
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by.
     * @return mixed
     */
    public function destroy( $value = NULL, $field = 'id' );

}