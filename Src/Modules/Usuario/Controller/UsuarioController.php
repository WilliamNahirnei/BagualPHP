<?php
namespace Src\Modules\Usuario\Controller;

use Server\Constants\StatusCodes;
use Server\Router\Response;
use stdClass;

/**
 * Class UsuarioController
 * 
 * This class handles the operations for the "usuario" module including listing, creating, updating, and deleting users.
 *
 * @package Src\Modules\Usuario\Controller
 * @author William Nahirnei Lopes
 */
class UsuarioController {
    /**
     * Lists users with predefined data.
     * 
     * DOC TESTES alterados documentacao
     * 
     * @return array An array of users, where each user is an instance of stdClass.
     */
    public function listar(): array {
        $response = [
            (object) ['id' => 1, 'nome' => 'Usuário 1', 'email' => 'usuario1@example.com'],
            (object) ['id' => 2, 'nome' => 'Usuário 2', 'email' => 'usuario2@example.com']
        ];

        // Configure response status code
        Response::setStatusCode(StatusCodes::HTTP_OK);

        // Configure response message
        Response::setResponseMessage('Usuários listados com sucesso');

        // Configure response headers
        Response::addHeader(Response::HEADER_CONTENT_TYPE, Response::CONTENT_TYPE_JSON);
        Response::addHeader("X-Custom-Header1", "HeaderValue1");
        Response::addHeader("X-Custom-Header1", "HeaderValue2");

        return $response;
    }

    /**
     * Creates a user with predefined data.
     * 
     * TESTES DOC  testes documentado
     * 
     * 
     * @return stdClass An object representing the created user.
     */
    public function criar(): stdClass {
        // Configure response status code
        Response::setStatusCode(StatusCodes::HTTP_CREATED);

        // Configure response message
        Response::setResponseMessage('Usuário criado com sucesso');

        return (object) ['id' => 1, 'nome' => 'Usuário 1', 'email' => 'usuario1@example.com'];
    }

    /**
     * Updates a user with predefined data.
     * 
     * outr oteste
     * 
     * @return stdClass An object representing the updated user.
     */
    public function atualizar(): stdClass {
        Response::setStatusCode(StatusCodes::HTTP_OK);

        // Configure response message
        Response::setResponseMessage('Usuário atualizado com sucesso');

        return (object) ['id' => 1, 'nome' => 'Usuário 1', 'email' => 'usuario1@example.com'];
    }

    /**
     * Deletes a user.
     * 
     * Sets the response status and message before returning null.
     * 
     * @return void
     */
    public function deletar() {
        Response::setStatusCode(StatusCodes::HTTP_OK);

        // Configure response message
        Response::setResponseMessage('Usuário deletado com sucesso');

        return null;
    }

    /**
     * A public route that returns a simple message.
     * 
     * Sets the response status and message before returning null.
     * 
     * @return void
     */
    public function publico() {
        Response::setStatusCode(StatusCodes::HTTP_OK);

        // Configure response message
        Response::setResponseMessage('Esta é uma rota pública');
        return null;
    }
}
?>