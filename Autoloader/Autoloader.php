<?php
namespace Autoloader;

spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix (one level up)
    $baseDir = dirname(__DIR__) . '/';

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});
?>