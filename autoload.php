<?php

spl_autoload_register(function ($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    if (file_exists(__DIR__.'/'. $className . '.php')) {
        include_once $className . '.php';
    }
});
