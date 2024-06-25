<?php

    namespace Controller;

    // require_once('./Server/Routes/RouteParams.php');

use Server\Router\Response;
use Server\Routes\RouteParams;
    use src\Services\ProductService;

    class StatusController
    {
        public static function status()
        {
            return ['status' => "online"];
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
            return $pessoa;
        }
    }
