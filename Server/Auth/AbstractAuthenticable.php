<?php

namespace Server\Auth;

use Server\Errors\AuthenticationException;

/**
 * Class AbstractAuthenticable
 *
 * An abstract class that defines the structure for authentication classes. 
 * Subclasses must implement the authentication logic and handle authentication errors.
 *
 * @package Server\Auth
 * @author William Nahirnei Lopes
 */
abstract class AbstractAuthenticable {

    /**
     * An abstract method that must be implemented by subclasses.
     * This method should handle the authentication logic and throw an 
     * AuthenticationException if authentication fails.
     *
     * @throws AuthenticationException If authentication fails.
     */
    abstract protected static function callAuthError(): void;
}
?>