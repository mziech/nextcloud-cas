<?php
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
