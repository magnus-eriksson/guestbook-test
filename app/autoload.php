<?php
spl_autoload_register(function($class) {
    $namespace = 'App\\';
    $path      = __DIR__;

    if (strpos($class, $namespace) !== 0) {
        // Not a match for the registered namespace, let's bail
        return;
    }

    // Get the sub namespace, if there is any
    $subNameSpace = substr($class, strlen($namespace));

    // Convert the namespace to file path
    $file = $path . '/' . str_replace('\\', '/', $subNameSpace) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
