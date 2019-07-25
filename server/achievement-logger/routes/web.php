<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post(
    'auth/login',
    [
        'uses' => 'AuthController@authenticate'
    ]
);

$router->group(
    ['prefix' => 'api', 'middleware' => 'jwt.auth'],
    function () use ($router) {
        $router->get('user', 'ApiController@user');
    }
);
