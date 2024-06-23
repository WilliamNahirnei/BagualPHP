<?php
    namespace Server\Routes;
    
    include_once('Server/Routes/Route.php');
    require_once('./src/Controller/StatusController.php');

use Controller\StatusController;

    Route::get('/status',[StatusController::class, 'status']);
    Route::post('/testePost',[StatusController::class, 'testePost']);

?>