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
        // var_export(Request::getInstance()->getHeaders());
        $pessoa = [
            "id" => 1,
            "nome" => "USU1",
            "email" => "email",
            "senha" => "senmha",
            "status" => ["id" => 1, "descricao" => "DESCRICAO"]
        ];

        $pessoa = new \stdClass();
        $pessoa->id = 1;
        $pessoa->nome = "USU1";
        $pessoa->email = "email";
        $pessoa->senha = "senmha";
        
        $pessoa->status = new \stdClass();
        $pessoa->status->id = 1;
        $pessoa->status->descricao = "DESCRICAO";
        
        $pessoa->lista2 = [];
        
        for ($i = 0; $i < 3; $i++) {
            $item = new \stdClass();
            $attributesCount = rand(2, 3); // Aleatoriamente escolhe 2 ou 3 atributos
        
            $item->atributo1 = "valor1-" . ($i + 1);
        
            if ($attributesCount > 2) {
                $item->atributo2 = "valor2-" . ($i + 1);
            }
        
            if ($attributesCount > 2) {
                $item->atributo3 = "valor3-" . ($i + 1);
            }
        
            $pessoa->lista2[$i] = $item;
        }
        

        return $pessoa;
    }
}
?>