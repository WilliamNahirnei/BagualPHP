<?php

namespace Server\Errors;

use Server\Constants\ServerMessage;
use Server\Constants\StatusCodes;

class AuthenticationException extends ApiException {
    public function __construct(?array $errorListMessage = [ServerMessage::DEFAULT_AUTH_ERROR], ?int $restCode = StatusCodes::HTTP_UNAUTHORIZED) {
        parent::__construct(null, null, $errorListMessage, $restCode);
    }
}
?>