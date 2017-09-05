<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repository interfaces namespace
    |--------------------------------------------------------------------------
    |
    | You can specify the namespace used in your repositories interfaces.
    | Once again, I like to put everything under the namespace of my app,
    | so my repository interfaces usually live under the namespace of my
    | application: "MyApp\Repositories".
    |
    */
    'repository_interfaces_namespace' => 'App\Repositories',

    /*
    |--------------------------------------------------------------------------
    | Criterias namespace
    |--------------------------------------------------------------------------
    |
    | Please specify the namespace for the criteria. The implementation will
    | be appended.
    |
    */
    'criterias_namespace' => 'App\Repositories\Criteria',

    /*
    |--------------------------------------------------------------------------
    | Base repositories path
    |--------------------------------------------------------------------------
    |
    | By default the package considers that your interfaces live in
    | App/Repositories. You can however set this path to whatever value
    | you want. I personally like to locate all my project files inside a
    | folder located in "app", something like "app/MyApp/Repositories".
    |
    */
    'repositories_path' => app_path('Repositories'),

    /*
    |--------------------------------------------------------------------------
    | Base criteria path
    |--------------------------------------------------------------------------
    |
    | Your criteria needs to live somewhere, so please specify here the base
    | path of your criteria. Skip the implementation.
    |
    */
    'criterias_path' => app_path('Repositories/Criteria'),

    /*
    |--------------------------------------------------------------------------
    | Implementation bindings
    |--------------------------------------------------------------------------
    |
    | As we can have same interface but different implementations that support
    | our repositories, we can define the implementation that we want to use for
    | each of the repositories that we have in our application. By default,
    | Eloquent is used. Sometimes you might find cases where you have to
    | support several data-stores (like MariaDB, MongoDB and PostgreSQL) at the
    | same time. Although this package supports only Eloquent now, the plan is
    | to add more engines/ORMs/data-stores, so I want to keep this flexible.
    |
    | In this configuration setting you can specific which repository should
    | resolve to each engine. You can't have the same repository bind to more
    | than one engine, but you can have different repositories bind to different
    | engines.
    |
    | All the repository interfaces not bind will be bind to the default
    | engine.
    |
    | Values supported: 'default', array of repository interface names.
    |
    */
    'bindings' => [
        'eloquent' => 'default',
        // 'eloquent' => [
        //     'UserRepositoryInterface',
        //     'PostRepositoryInterface'
        // ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Skip repositories
    |--------------------------------------------------------------------------
    |
    | Sometimes you may wish to skip the auto-binding of some repositories.
    | You can specify here what of those INTERFACES should be skipped from the
    | auto-binder. You must specify the name of the file, as the skip happens
    | when scanning the repositories.
    |
    */
    'skip' => [
        'BaseRepositoryInterface'
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported implementations
    |--------------------------------------------------------------------------
    |
    | Array with the supported implementations. This allow you to extend the
    | package to your needs.
    |
    */
    'supported_implementations' =>  [
        'eloquent' => \OkayBueno\Repositories\src\EloquentRepository::class,
    ]

];