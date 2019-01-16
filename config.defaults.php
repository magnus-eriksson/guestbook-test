<?php
/**
 * This is the main app configuration
 * If you want to override any of these settings, copy this file as "config.php"
 * and make changed to that file instead. "config.php" is gitignored so you can
 * have different settings in different environments.
 */
return [
    /**
     * Database settings
     * ------------------------------------
     */
    'db' => [
        'hostname' => '',
        'database' => '',
        'username' => '',
        'password' => '',
    ],

    'views' => [
        'path' => __DIR__ . '/views',
    ],
];
