<?php
    //autoloader
    require_once __DIR__ . '/Autoloader/Autoloader.php';   

    use Server\Router\Router;

    $router = new Router();
    $router->executeRequest();

?>