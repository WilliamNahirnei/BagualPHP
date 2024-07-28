<?php

namespace Src\Modules\Status\Controller;

use Server\Router\Request;

class StatusController
{
    public static function status()
    {
        var_export("HERE CALLED");
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
        var_export(Request::getInstance());

        return $pessoa;
    }
}
?>