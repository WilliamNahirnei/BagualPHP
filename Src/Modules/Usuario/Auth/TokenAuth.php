<?php
namespace Src\Modules\Usuario\Auth;

use Server\Auth\AbstractAuthenticable;
use Server\Errors\AuthenticationException;
use Server\Router\Request;

/**
 * Class TokenAuth
 * 
 * This class handles token-based authentication for the "usuario" module.
 * It validates the token provided in the request headers.
 *
 * @package Src\Modules\Usuario\Auth
 * @author William Nahirnei Lopes
 */
class TokenAuth extends AbstractAuthenticable {

    /**
     * Throws an AuthenticationException when authentication fails.
     * 
     * @throws AuthenticationException When the token is invalid.
     * @return void
     */
    protected static function callAuthError(): void {
        throw new AuthenticationException(['Token inválido']);
    }

    /**
     * Authenticates the request based on the token provided in the headers.
     * 
     * Checks if the 'token' header exists and matches the expected value.
     * If authentication fails, an exception is thrown.
     * 
     * @return bool Returns true if authentication is successful.
     * @throws AuthenticationException When the token is invalid.
     */
    public static function authenticate(): bool {
        $headers = Request::getInstance()->getHeaders();
        if (!isset($headers['token']) || !($headers['token'] == 'Bearer 123456789')) {
            self::callAuthError();
        }
        return true;
    }
}
?>