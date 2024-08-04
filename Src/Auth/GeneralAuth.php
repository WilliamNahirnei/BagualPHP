<?php

namespace Src\Auth;

use Server\Auth\AbstractAuthenticable;
use Server\Constants\ServerMessage;
use Server\Errors\AuthenticationException;

class GeneralAuth extends AbstractAuthenticable{
    /**
     * This method must be implemented by subclasses and must throw AuthenticationException
     *
     * @throws AuthenticationException
     */
    protected static function callAuthError(): void {
        throw new AuthenticationException([ServerMessage::DEFAULT_AUTH_ERROR]);
    }

    public static function authenticate() {
        return false;
    }
}
?>