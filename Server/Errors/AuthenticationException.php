<?php

namespace Server\Errors;

use Server\Constants\ServerMessage;
use Server\Constants\StatusCodes;

/**
 * Class AuthenticationException
 *
 * Custom exception class for handling authentication-related errors. This exception extends
 * from ApiException and provides default error messages and status codes specific to authentication issues.
 *
 * @package Server\Errors
 */
class AuthenticationException extends ApiException {
    /**
     * AuthenticationException constructor.
     *
     * @param array|null $errorListMessage An optional list of error messages to be associated with the exception.
     *                                      Defaults to a predefined authentication error message if not provided.
     * @param int|null   $restCode         An optional HTTP status code to be associated with the exception.
     *                                      Defaults to HTTP 401 Unauthorized if not provided.
     */
    public function __construct(
        ?array $errorListMessage = [ServerMessage::DEFAULT_AUTH_ERROR],
        ?int $restCode = StatusCodes::HTTP_UNAUTHORIZED
    ) {
        parent::__construct(null, null, $errorListMessage, $restCode);
    }
}
?>