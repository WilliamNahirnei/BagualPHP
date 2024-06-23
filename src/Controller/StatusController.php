<?php

    namespace Controller;

    // require_once('./Server/Routes/RouteParams.php');
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
    
            $response = json_encode($response);
            return $response;
        }

        public static function testePost()
        {
            $pessoa = [
                "id" => 1,
                "nome" => "USU1",
                "email" => "email",
                "senha" => "senmha",
                "status" => ["id" =>1, "descricao"=> "DESCRICAO"]
            ];
            http_response_code(200);
            $response = [
                'Message'=> "TESTE",
                'data'=> $pessoa
            ];
    
            $response = json_encode($response);
            return $response;
        }
    }
