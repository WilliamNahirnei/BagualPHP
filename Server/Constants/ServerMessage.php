<?php

namespace Server\Constants;

/**
 * Class ServerMessage
 *
 * Defines a set of constants representing standard server messages used throughout the application. 
 * These messages are intended to provide standardized error and status information.
 *
 * @package Server\Constants
 */
class ServerMessage {

    /**
     * The key used for the message in server responses.
     */
    const MESSAGE = "message";

    /**
     * Message indicating that a resource could not be found.
     */
    const NOT_FOUND = "Not Found";

    /**
     * Message used to indicate issues related to routing.
     */
    const ROUTE = "Route";

    /**
     * Message prefix used to indicate an internal server error.
     */
    const INTERNAL_SERVER_ERRO = "internal server error:";

    /**
     * Default error message indicating an unauthorized access or authentication issue.
     */
    const DEFAULT_AUTH_ERROR = "Unauthorized, authentication error";

}
?>