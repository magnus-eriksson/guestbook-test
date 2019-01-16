<?php
/**
 * Include our PSR-4 autoloader to make things easier
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/app/autoload.php';


/**
 * Register the IoC container
 * ----------------------------------------------------------------------------
 */
$app = new App\Container;


/**
 * Register the config instance
 * ----------------------------------------------------------------------------
 */
$app->bind('App\Libraries\Config', function () {
    return new App\Libraries\Config([
        __DIR__ . '/config.defaults.php',
        __DIR__ . '/config.php',
    ]);
}, 'config');


/**
 * Register the database
 * ----------------------------------------------------------------------------
 */
$app->bind('App\Libraries\Database', function ($app) {
    return new App\Libraries\Database(new PDO(
        'mysql:host=' . $app->config->get('db')['hostname'] . ';dbname=' . $app->config->get('db')['database'],
        $app->config->get('db')['username'],
        $app->config->get('db')['password']
    ));
}, 'db');


/**
 * Register the session
 * ----------------------------------------------------------------------------
 */

$app->bind('App\Libraries\Session', null, 'session');


/**
 * Register request
 * ----------------------------------------------------------------------------
 */
$app->bind('App\Libraries\Request', null, 'request');


/**
 * Register views
 * ----------------------------------------------------------------------------
 */
$app->bind('App\Libraries\Views', function ($app) {
    return new App\Libraries\Views(
        $app->config->get('views', 'path'),
        $app->session
    );
}, 'views');


/**
 * Register the router
 * ----------------------------------------------------------------------------
 */
$app->bind('App\Libraries\Router', function ($app) {
    return (new App\Libraries\Router($app->request))->setResolver(function ($class) use ($app) {
        return $app->make($class);
    });
}, 'router');


/**
 * Register services
 * ----------------------------------------------------------------------------
 */
$app->bind('App\Servies\Users', function ($app) {
    return new App\Services\Users(
        $app->db,
        $app->session
    );
}, 'users');

$app->bind('App\Services\Entries', function ($app) {
    return new App\Services\Entries($app->db);
}, 'entries');


/**
 * Register the controllers
 * ----------------------------------------------------------------------------
 */
$app->bind('App\Controllers\EntriesController', function ($app) {
    return new App\Controllers\EntitiesController(
        $app->request,
        $app->views,
        $app->entries,
        $app->users,
        $app->session
    );
});


$app->bind('App\Controllers\UsersController', function ($app) {
    return new App\Controllers\UsersController(
        $app->request,
        $app->views,
        $app->users,
        $app->session
    );
});


/**
 * Register middlewares
 * ----------------------------------------------------------------------------
 */
$app->bind('App\Middlewares\Auth', function ($app) {
    return new App\Middlewares\Auth(
        $app->users
    );
});


/**
 * Dev stuff
 * ----------------------------------------------------------------------------
 */
//$app->session->set('currentUser', ['id' => 1, 'name' => 'Magnus']);


/**
 * Dispatch the router and echo the result
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/app/routes.php';

echo $app->router->dispatch();