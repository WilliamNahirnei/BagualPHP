<?php
/**
 * Autoloader for including necessary classes and dependencies.
 * 
 * This script initializes the autoloader and starts the router to handle incoming requests.
 * It requires the Autoloader script and then creates an instance of the Router class.
 * The Router is responsible for processing and executing the request.
 */

// Autoloader
require_once __DIR__ . '/Autoloader/Autoloader.php';   

use Server\Router\Router;

/**
 * Initialize and execute the Router.
 * 
 * Creates an instance of the Router class and calls the executeRequest method to handle
 * and route the incoming HTTP request.
 */
$router = new Router();
$router->executeRequest();
?>