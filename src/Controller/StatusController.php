<?php

    namespace src\Controller;

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
