<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Models namespace
    |--------------------------------------------------------------------------
    |
    | If you want to use the generators shipped with the package, you need
    | to specify the namespace where you models are living at.
    |
    */
    'models_namespace' => 'MyApp\Models',

    /*
    |--------------------------------------------------------------------------
    | Interfaces & repositories base path
    |--------------------------------------------------------------------------
    |
    | By default the package considers that your interfaces live in
    | app/Repositories. You can however set this path to whatever value
    | you want. I personally like to locate all my project files inside a
    | folder located in "app", something like "app/MyApp/Repositories".
    |
    */
    'path' => app_path('MyApp/Repositories'),

    /*
    |--------------------------------------------------------------------------
    | Interfaces namespace
    |--------------------------------------------------------------------------
    |
    | You can specify the namespace used in your repositories interfaces.
    | Once again, I like to put everything under the namespace of my app,
    | so my repository interfaces usually live under the namespace of my
    | application: "MyApp\Repositories".
    |
    */
    'namespace' => 'MyApp\Repositories',


    /*
    |--------------------------------------------------------------------------
    | Criteria base path
    |--------------------------------------------------------------------------
    |
    | Your criteria needs to live somewhere, so please specify here the base
    | path of your criteria. Skip the implementation.
    |
    */
    'criteria_path' => app_path('MyApp/Repositories/Criteria'),


    /*
    |--------------------------------------------------------------------------
    | Criteria namespace
    |--------------------------------------------------------------------------
    |
    | Please specify the namespace for the criteria. The implementation will
    | be appended.
    |
    */
    'criteria_namespace' => 'MyApp\Repositories\Criteria',

    /*
    |--------------------------------------------------------------------------
    | Implementation
    |--------------------------------------------------------------------------
    |
    | As we can have same interface but different implementations that support
    | our repositories, we can define the implementation that we want to use.
    | For now, only Eloquent is supported. This is important because by default
    | our repositories should live in a sub-directory located in 'path' and
    | name the same way than this "implementation" variable. This is done
    | because the repositories are automatically mapped and loaded based on
    | a combination of the implementation and the path. So to say: if you have
    | your repositories under "app/Repositories", and the implementation is
    | 'Eloquent', the system will automatically try to bind every file
    | located in 'app/Repositories/MyCustomRepositoryInterface.php' to a file
    | located in 'app/Repositories/Eloquent/MyCustomRepository.php'. The namespace
    | of the implementation must also be preceded by this value. In the previous
    | example, the namespace of our repository would be: "App\Repositories\Eloquent".
    | Name this configuration value exactly the same way that your folder.
    |
    | Values supported: Eloquent.
    |
    */
    'default_implementation' => 'Eloquent',

    /*
    |--------------------------------------------------------------------------
    | Skip repositories
    |--------------------------------------------------------------------------
    |
    | Sometimes you may wish to skip the auto-binding of some repositories.
    | You can specify here what of those INTERFACES should be skipped from the
    | auto-binder. You must specify the name of the file, as the skip happens
    | when scanning the repository.
    |
    */
    'skip' => [ 'BaseRepositoryInterface.php' ],

];