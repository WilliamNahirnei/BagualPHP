<?php

namespace Src\Auth;

use Server\Auth\AbstractAuthenticable;
use Server\Errors\AuthenticationException;

class GeneralAuth extends AbstractAuthenticable{
    /**
     * This method must be implemented by subclasses and must throw AuthenticationException
     *
     * @throws AuthenticationException
     */
    protected static function callAuthError(): void {
        throw new AuthenticationException(["NAO AUTORIZADO"]);
    }

    public static function teste1() {
        return true;
    }

    public static function teste2() {
        return true;
        // self::callAuthError();
    }
}
?>