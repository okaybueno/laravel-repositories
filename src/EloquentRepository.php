<?php

namespace OkayBueno\Repositories\src;

use OkayBueno\Repositories\Criteria\CriteriaInterface;
use OkayBueno\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentRepository
 * @package OkayBueno\Repositories\src
 */
abstract class EloquentRepository implements RepositoryInterface
{
    
    protected $model;
    protected $with;
    protected $skipCriteria;
    protected $criteria;
    private $modelClassName;
    
    /**
     * @param Model $model
     */
    public function __construct( Model $model )
    {
        $this->model = $model;
        
        // A clean copy of the model is needed when the scope needs to be reset.
        $reflex = new \ReflectionClass( $model );
        $this->modelClassName = $reflex->getName();
        
        $this->skipCriteria = FALSE;
        $this->criteria = [];
    }
    
    /**
     * @param $value
     * @param string $field
     * @param array $columns
     * @return mixed
     */
    public function findOneBy($value = NULL, $field = 'id', array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();
        
        if ( !is_null( $value ) ) $this->model = $this->model->where( $field, $value );
        
        $result = $this->model->first( $columns );
        
        $this->resetScope();
        
        return $result;
    }
    
    /**
     * @param null $value
     * @param null $field
     * @param array $columns
     * @return mixed
     */
    public function findAllBy($value = NULL, $field = NULL, array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();
        
        if ( !is_null( $value ) && !is_null( $field ) ) $this->model = $this->model->where( $field, $value );
        
        $result = $this->model->get( $columns );
        
        $this->resetScope();
        
        return $result;
    }
    
    /**
     * @param array $value
     * @param string $field
     * @param array $columns
     * @return mixed
     */
    public function findAllWhereIn(array $value, $field, array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();
        $result = $this->model->whereIn( $field, $value )->get( $columns );
        
        $this->resetScope();
        
        return $result;
    }
    
    /**
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll( array $columns = ['*'] )
    {
        $this->eagerLoadRelations();
        $result = $this->model->all( $columns );
        
        $this->resetScope();
        
        return $result;
    }
    
    /**
     * @param array|string $relations
     * @return $this
     */
    public function with( $relations )
    {
        if ( is_string( $relations ) ) $relations = func_get_args();
        
        $this->with = $relations;
        
        return $this;
    }
    
    
    /**
     * @param CriteriaInterface $criteria
     * @return $this
     */
    public function addCriteria( CriteriaInterface $criteria)
    {
        $this->criteria[] = $criteria;
        
        return $this;
    }
    
    
    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria( $status = TRUE )
    {
        $this->skipCriteria = $status;
        return $this;
    }
    
    
    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage, array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();
        $result = $this->model->paginate( $perPage, $columns );
        
        $this->resetScope();
        
        return $result;
    }
    
    
    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage( $currentPage )
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function() use ( $currentPage )
        {
            return $currentPage;
        });
        
        return $this;
    }
    
    
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $cleanFields = $this->cleanUnfillableFields( $data );
        
        $createdObject = $this->model->create( $cleanFields );
        
        $this->resetScope();
        
        return $createdObject;
    }
    
    /**
     * @param array $data
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function updateBy(array $data, $value = NULL, $field = 'id')
    {
        $cleanFields = $this->cleanUnfillableFields( $data );
        
        if ( !is_null( $value ) )
        {
            // Single update.
            $this->model->where( $field, $value)->update( $cleanFields );
            
            foreach( $cleanFields as $F => $V ) $this->model->{$F} = $V;
            
            $returnedVal = $this->model;
        } else
        {
            // Mass update.
            $this->applyCriteria();
            
            $returnedVal = $this->model->update( $cleanFields );
        }
        
        $this->resetScope();
        
        return $returnedVal;
    }
    
    /**
     * @param null $value
     * @param string $field
     * @return bool
     */
    public function delete( $value = null, $field = 'id' )
    {
        $this->applyCriteria();
        
        if ( !is_null( $value ) ) $result = $this->model->where( $field, $value )->delete();
        else
        {
            if ( !empty( $this->criteria ) ) $result = $this->model->delete();
            else $result = FALSE;
        }
        
        $this->resetScope();
        
        return (bool)$result;
    }
    
    /**
     * @return mixed
     */
    public function count()
    {
        $this->applyCriteria();
        $result = $this->model->count();
        
        $this->resetScope();
        
        return $result;
    }
    
    /**
     * @return $this
     */
    public function resetScope()
    {
        $this->criteria = [];
        $this->skipCriteria( FALSE );
        $this->model = new $this->modelClassName();
        return $this;
    }
    
    /**
     * @param null $value
     * @param string $field
     * @return mixed
     */
    public function destroy($value = null, $field = 'id')
    {
        $this->applyCriteria();
        
        if ( !is_null( $value ) ) $result = $this->model->where( $field, $value )->forceDelete();
        else
        {
            if ( !empty( $this->criteria ) ) $result = $this->model->forceDelete();
            else $result = FALSE;
        }
        
        $this->resetScope();
        
        return (bool)$result;
    }
    
    
    /*******************************************************************************************************************
     *******************************************************************************************************************
     *******************************************************************************************************************/
    
    /**
     *
     */
    protected function eagerLoadRelations()
    {
        if ( is_array( $this->with ) ) $this->model->with( $this->with );
    }
    
    
    /**
     * @param array $data
     * @return array
     */
    private function cleanUnfillableFields( array $data )
    {
        $fillableFields = $this->model->getFillable();
        
        foreach( $data as $key => $value )
        {
            if ( !in_array( $key, $fillableFields ) ) unset( $data[ $key ] );
        }
        
        return $data;
    }
    
    /**
     * @return $this
     */
    private function applyCriteria()
    {
        if( !$this->skipCriteria )
        {
            foreach( $this->criteria as $criteria )
            {
                if( $criteria instanceof CriteriaInterface ) $this->model = $criteria->apply( $this->model, $this );
            }
        }
        
        return $this;
    }
    
    
}