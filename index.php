<?php
    require_once('./Server/Router/Router.php');
    
    use Server\Router\Router;

    $router = new Router();
    $router->executeRequest();

?>