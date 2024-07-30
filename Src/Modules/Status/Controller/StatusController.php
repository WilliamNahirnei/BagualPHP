<?php

namespace Src\Modules\Status\Controller;

use Server\Router\Request;

class StatusController
{
    public static function status()
    {
        return ['status' => "online"];
    }

    public static function testePost()
    {
        // throw new \Exception("TESTE");
        $pessoa = [
            "id" => 1,
            "nome" => "USU1",
            "email" => "email",
            "senha" => "senmha",
            "status" => ["id" => 1, "descricao" => "DESCRICAO"]
        ];

        return $pessoa;
    }
}
?>