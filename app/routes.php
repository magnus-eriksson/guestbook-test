<?php

$app->router->get(
    '/',
    'App\Controllers\EntriesController@showEntries'
);


/**
 * User routes
 */

// Log in / Log out
$app->router->get(
    '/login',
    'App\Controllers\UsersController@showLogin'
);

$app->router->post(
    '/login',
    'App\Controllers\UsersController@login'
);

$app->router->get(
    '/logout',
    'App\Controllers\UsersController@logout'
);

// Register
$app->router->get(
    '/register',
    'App\Controllers\UsersController@showRegister'
);

$app->router->post(
    '/register',
    'App\Controllers\UsersController@register'
);


/**
 * Entries
 */
$app->router->get(
    '/entry/new/form',
    'App\Controllers\EntriesController@showNewEntry',
    'App\Middlewares\Auth@requireAuth'
);

$app->router->get(
    '/entry/reply/form',
    'App\Controllers\EntriesController@showReplyEntry',
    'App\Middlewares\Auth@requireAuth'
);

$app->router->get(
    '/entry/edit/form',
    'App\Controllers\EntriesController@showEditEntry',
    'App\Middlewares\Auth@requireAuth'
);

$app->router->post(
    '/entry/create',
    'App\Controllers\EntriesController@saveNewEntry',
    'App\Middlewares\Auth@requireAuth'
);

$app->router->post(
    '/entry/reply',
    'App\Controllers\EntriesController@saveReplyEntry',
    'App\Middlewares\Auth@requireAuth'
);

$app->router->post(
    '/entry/update',
    'App\Controllers\EntriesController@updateEntry',
    'App\Middlewares\Auth@requireAuth'
);

$app->router->post(
    '/entry/delete',
    'App\Controllers\EntriesController@deleteEntry',
    'App\Middlewares\Auth@requireAuth'
);
