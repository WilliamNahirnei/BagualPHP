<?php
namespace Autoloader;

/**
 * Registers an autoloader function that handles automatic loading of class files.
 *
 * This function is registered with `spl_autoload_register` to automatically include class files 
 * based on the namespace and class name.
 *
 * The function performs the following actions:
 * - Defines the base directory for class files, which is one level up from the current directory.
 * - Converts the namespace prefix to a directory path by replacing namespace separators with 
 *   directory separators.
 * - Appends the `.php` extension to the file path.
 * - Requires the file if it exists.
 */
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