# Laravel Repositories

A package that provides a neat implementation for integrating the Repository pattern with Laravel &amp; Eloquent.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/okaybueno/laravel-repositories.svg?style=flat-square)](https://packagist.org/packages/okaybueno/laravel-repositories)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/okaybueno/laravel-repositories.svg?style=flat-square)](https://scrutinizer-ci.com/g/okaybueno/laravel-repositories)
[![Total Downloads](https://img.shields.io/packagist/dt/okaybueno/laravel-repositories.svg?style=flat-square)](https://packagist.org/packages/okaybueno/laravel-repositories)

## Disclaimer

This package was originally released [here](https://github.com/followloop/laravel-repositories), but since the future 
of that package is not clear, it has been forked and re-worked under this repository.

## Goal

Working with repositories can provide a great way to not only decouple your code but also separate concerns and
isolate stuff, as well as separate and group responsibilities. Most of the time we will perform really generic actions
on our database tables, like create, update, filter or delete.  

However, using repositories is not always a good idea, specially with Laravel and its ORM, Eloquent, as it -sometimes-
forces you to give up some great features in favor of *better architecture* (it depends). For some projects this may be
an overkill and therefore is up to the developer to see if this level of complexity is needed or not.

This package aims to provide a boilerplate when implementing the Repository pattern on Laravel's Eloquent ORM. The way
it provides it it's using a `RepositoryInterface` and a basic `Repository` implementation that is able to work with Eloquent models,
 providing basic methods that will cover 85% if the Database operations that you will probably do in your application.

## Installation

1. Install this package by adding it to your `composer.json` or by running `composer require okaybueno/laravel-repositories` in your project's folder.
2. For Laravel 5.5 the Service provider is automatically registered, but if you're using Laravel 5.4, then you must add the 
provider to your `config/app.php` file: `OkayBueno\Repositories\RepositoryServiceProvider::class`
3. Publish the configuration file by running `php artisan vendor:publish --provider="OkayBueno\Repositories\RepositoryServiceProvider"`
4. Open the configuration file (`config/repositories.php`) and configure paths according to your needs.
5. Ready to go!


## Usage

To start using the package, you just need to create a folder where you will place all your repository interfaces and the
repository implementations and extend every single repository from the `EloquentRepository` class offered by the package.  

The Eloquent model to be handled by the repository that you have created must also be injected via the Repo constructor.  

The package then will try to load all the repository interfaces and bind them to a repository implementation according to
the parameters specified in the `config/repositories.php` file.


## Examples

*NOTE: Although the package includes a generator, please read all the docs carefully as some things may look just "magic".*

Let's consider we will have all our repositories in a folder called "Repositories", under a folder called "MyWebbApp" inside
the "app" folder: app/MyWebApp/Repositories.

At the root of this folder we'll have all our interfaces following the next name convention: `[RepositoryName]Interface.php`

**NOTE**: It does not really matter the name that we use as long as we use "Interface" as suffix. This is important because the 
auto binder will try to find all files matching this pattern.

Inside this Repositories folder, we must have another folder called Eloquent, that will contain all our implementations for
the repositories, following the next name convention: `[RepositoryName].php`.  

We should have a structure like this:  

```
+-- app
|   +-- MyApp
|       +-- Repositories
|           +-- UserRepositoryInterface.php
|           +-- RoleRepositoryInterface.php
|           +-- CommentRepositoryInterface.php
|           +-- PostRepositoryInterface.php
|           +-- Eloquent
|               +-- UserRepository.php
|               +-- RoleRepository.php
|               +-- CommentRepository.php
|               +-- PostRepository.php  
```

Let's see what the `UserRepositoryInterface.php` and the `UserRepository.php` would have.

```php

<?php

namespace MyApp\Repositories;

use OkayBueno\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface {

    // here you would write the contract for methods that your repository will implement.
}

```

```php

<?php

namespace MyApp\Repositories\Eloquent;

use OkayBueno\Repositories\src\EloquentRepository;
use MyApp\Repositories\UserRepositoryInterface;
use MyApp\Models\User;

class UserRepository extends EloquentRepository implements UserRepositoryInterface  {

    public function __construct( User $user ) {
        parent::__construct( $user );
    }
    
    // methods that your repository should implement...
}

```

Now we need to configure the `config/repositories.php` file to match our paths and namespace:  

```php

    'repository_interfaces_namespace' => 'MyApp\Repositories',
    
    'criterias_namespace' => 'MyApp\Repositories\Criteria',

    'repositories_path' => app_path('MyApp/Repositories'),
    
    'criterias_path' => app_path('MyApp/Repositories/Criteria'),

```

All the parameters are properly explained in the file.

Now the repository is ready to be used and injected in other services or controllers:

```php

<?php

namespace MyApp\Services\Users;

use MyApp\Repositories\UserRepositoryInterface;

class UsersService implements UserServicesInterface  {

    protected $usersRepository;

    public function __construct( UserRepositoryInterface $usersRepositoryInterface ) {
        $this->usersRepository = $usersRepositoryInterface;
    }
   
    // other methods in your service.
}

```

**NOTE**: This example assumes that you have configured your `composer.json` to autoload the files on app/MyApp with the MyApp
namespace.


## Methods shipped by default.

The repository package offers a series of methods by default. These are:

```php

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

```


## Criteria

To avoid having our repository full of methods like `findActiveUsers()`, `findActiveUsersOlderThan( $date )` and so on,
we'll be using Criteria to apply filters to our searches or queries.  

A Criteria is just a PHP Class that implements the CriteriaInterface provided by this package and that operates on the
Eloquent model to apply the Query or set of queries that we want to apply for an specific search.  

To create your own criterias, place them wherever you want and make them implement the CriteriaInterface provided.  

For instance: Imagine we have an application where users can register via Facebook, Twitter or email. We would need
to retrieve all users based on the method they used for registering. We would have a criteria like this:  

```php

<?php

namespace MyApp\Repositories\Criteria\Eloquent\Users;

use OkayBueno\Repositories\Criteria\CriteriaInterface;
use MyApp\Models\User;

class RegisteredVia implements CriteriaInterface  {

    protected $registeredVia;
    protected $onlyActive;

    public function __construct( $registeredVia, $onlyActive = TRUE ) {
    
        $this->registeredVia = $registeredVia;
        $this->onlyActive = $onlyActive;
        
    }
    
    
    public function apply( $queryBuilder ) {
    
        if ( $this->onlyActive ) $queryBuilder = $queryBuilder->where( 'active', TRUE );
        
        return $queryBuilder->where( 'registered_via', $this->registered_via );
        
    }
   
}
```

Now in your services or controllers you can use this criteria like this:


```php

    $registeredViaFacebookCriteria = new RegisteredVia( 'facebook' );
    
    return $this->userRepository->addCriteria( $registeredViaFacebookCriteria )->findAllBy();
```

We could even chain different criterias:


```php

    $registeredViaFacebookCriteria = new RegisteredVia( 'facebook' );
    $orderByCreationDate = new OrderBy( 'created_at', 'ASC' );
    
    return $this->userRepository
                ->addCriteria( $registeredViaFacebookCriteria )
                ->addCriteria( $orderByCreationDate )
                ->findAllBy();
```

### Criterias shipped by default


- *OrderBy( $orderBy, $direction = 'ASC' )*: It sorts the results by the given column and direction.
- *FilterByColumns( array $filter )*: It filters (with AND filter) by more than one column. The filter applied can be complex. Example:

```php

    $maxDate = Carbon::now()->subDays( 7 );
    $filter = [
        [ 'balance', '>=', 10 ],
        [ 'created_at', '<=', $date ],
        [ 'is_premium_user', TRUE ]
    ];
    
    $filterCriteria = new FilterByColumns( $filter );
    
    // This will return all premium users created more than 1 week ago and that have more than 10 (€,$, whatever) of balance.
    $users = $this->userRepository->addCriteria( $filterCriteria )->findAllBy();
```
    
For more complex queries, a custom Criteria must be created.

And now let's jump directly into the cool stuff that will save some time in your end...


## Generators!

Yeah, creating all those files, link them up, inject models and so on it's a bummer. Also for me. So I create a generator
that creates the interface and the Eloquent implementation (basic) for our models. Cool, huh?

There are 2 generators provided: for repositories and for criteria

#### Repositories

Just execute `php artisan make:repository Namespace\\ModelName --implementation=eloquent`

Being `ModelName` the name of the model (class) that you want to generate the repository for. This will generate an 
interface for this repository, an eloquent implementation implementing that interface and will also inject the model 
to the repository. The files will be placed in the directory that you configure in your `repositories.php` config file,
and will also have the namespace specified there. You can specify the implementation that you want to use, although for now
just Eloquent is supported out of the box.


#### Criteria

Execute `php artisan make:criteria Namespace\\ClassName --implementation=eloquent`

This will generate a criteria, using the parameters configured in your `repositories.php` config file. Additionally,
if you want to place the criteria inside a specific folder (inside the one already configured), you can specify that
as an option to the command: `php artisan make:criteria Accounts\\FilterByCreationDate`.

Play a bit around and see how they behave when you pass different parameters!

Generators do lot of magic, but they are also descriptive with errors, so if it fails, just pay attention to the error ;D.

## Traits

There are some functions included that do not necessarily need to be included in all repositories but that depend
on the model injected. These are the traits available for the different types of entities involved:

#### Repositories

- *Restorable*: If you are using SoftDelete in your models and want to be able to restore them via the repository, just 
include this trait in your repository. You must also implement the *RestorableInterface* in your repository and your
repository interface.
- *Countable*: If you any any column that acts as a counter, you can use this trait to add counting functionalities to 
 your repository (incremente/decrement value in the given amount). You must also implement the *CountableInterface* in 
 your repository and your repository interface.
 
 ## Extending the package
 
 You can create your own implementations to support, for instance, SQLite or MongoDB. The plan is to add more and more
 engines, but if you need something on your own you can create your own engines.
 
 To add a new engine, its class needs to implement the `RepositoryInterface` interface and all the methods defined there.
 You can place this class wherever you want, as the binding is done manually in the config (`config/repositories.php`) file:
 
 ```php
 
     'supported_implementations' =>  [
             'eloquent' => \OkayBueno\Repositories\src\EloquentRepository::class,
             'mongodb' => \MyApp\Repositories\Custom\MongoDbRepository::Class
         ]
 
 ```

You can also manually specify which repositories should be mapped by this engine. All the repositories which are not
explicitly mapped will be mapped to the default driver (eloquent):
 
  ```php
  
      'bindings' => [
              'eloquent' => 'default',
              'mongodb' => [
                   'LogRepositoryInterface',
                   'EventTrackerRepositoryInterface'
              ]
          ],
  
  ```
  
  Of course, you can use the generators to create criterias and repositories:
 
   ```php
   
       php artisan make:repository MyApp\\Models\\Log --implementation=mongodb
       
       php artisan make:criteria Events\\FilterLogsOlderThanAWeek --implementation=mongodb
   
   ```

## Changelog

##### v1.0.5:
- Fixes bug when using with() to eager-load relations.

##### v1.0.4:
- Fixes bug when loading different engines simultaneously on Laravel 5.5.

##### v1.0.3:
- Bug on generators on Laravel 5.5 was fixed.

##### v1.0.2:
- Error on calling updateBy() with laravel 5.5 was fixed.

##### v1.0.1:
- Small bug that made auto-load a pain in the ass was fixed.

##### v1.0.0:
- First public official version released. Basic repository functionality inherited from main package.


## Credits

- [okay bueno - A fully transparent digital product studio](http://okaybueno.com)
- [Jesús Espejo](https://github.com/jespejoh) ([Twitter](https://twitter.com/jespejo89))
- This great article by Bosnadev was source of inspiration lot of the things included in this package: https://bosnadev.com/2015/03/07/using-repository-pattern-in-laravel-5/


## Bugs & contributing

* Found a bug? That's good (and bad). Let me know using the Issues on Github.
* Need a feature or have something interesting to contribute with? Great! Open a pull request.

## To-dos

- Automated tests: Although this package has been heavily tested (even on production), there are no automated tests in place.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.