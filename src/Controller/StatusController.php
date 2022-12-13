<?php

    namespace Controller;

    require_once('./Server/Routes/RouteParams.php');
    use Server\Routes\RouteParams;
    use src\Services\ProductService;

    class StatusController
    {
        public static function status()
        {
            http_response_code(200);
            $response = [
                'Message'=> "Online",
                'data'=> "Online"
            ];
    
            $response = json_encode($Response);
            return $Response;
        }
    }
