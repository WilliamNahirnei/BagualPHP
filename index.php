<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    //autoloader
    require_once __DIR__ . '/Autoloader/Autoloader.php';   

    use Server\Router\Router;

    $router = new Router();
    $router->executeRequest();

?>