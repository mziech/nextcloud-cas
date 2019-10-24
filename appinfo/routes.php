<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\cas\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
        ['name' => 'Admin#get', 'url' => '/admin', 'verb' => 'GET'],
        ['name' => 'Admin#post', 'url' => '/admin', 'verb' => 'POST'],

        ['name' => 'Cas#login', 'url' => '/login', 'verb' => 'GET'],
        ['name' => 'Cas#validate', 'url' => '/validate', 'verb' => 'GET'],
        ['name' => 'Cas#serviceValidateV2', 'url' => '/serviceValidate', 'verb' => 'GET'],
        ['name' => 'Cas#serviceValidateV3', 'url' => '/p3/serviceValidate', 'verb' => 'GET'],
    ]
];
