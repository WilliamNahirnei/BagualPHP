<?php
    namespace Server\Routes;
    

    use Server\Routes\Route;
    use src\Controller\StatusController;

    Route::get('/status',[StatusController::class, 'status']);
    Route::post('/testePost',[StatusController::class, 'testePost']);

?>