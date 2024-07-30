<?php

namespace Server\Auth;

use Server\Errors\AuthenticationException;

abstract class AbstractAuthenticable {
    /**
     * This method must be implemented by subclasses and must throw AuthenticationException
     *
     * @throws AuthenticationException
     */
    abstract protected static function callAuthError(): void;
}
?>