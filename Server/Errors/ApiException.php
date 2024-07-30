<?php

namespace Server\Errors;

use Server\Constants\ApiExceptionTypes;
use Server\Constants\StatusCodes;

/**
 * Class ApiException
 *
 * Custom exception class for handling API-related errors. This class extends from the base
 * Exception class and provides additional properties and methods for error handling, including
 * a flag to indicate acceptance, an error type, a list of error messages, and an HTTP status code.
 *
 * @package Server\Errors
 */
class ApiException extends \Exception {
    /**
     * @var bool Indicates whether the exception is accepted or not.
     */
    private bool $accept;

    /**
     * @var string The type of the exception, such as error or warning.
     */
    private string $type;

    /**
     * @var string The concatenated error messages.
     */
    private string $errorListMessage;

    /**
     * @var int The HTTP status code associated with the exception.
     */
    private int $restCode;

    /**
     * ApiException constructor.
     *
     * @param bool|null $accept           An optional flag indicating whether the exception is accepted or not.
     *                                    Defaults to true if not provided.
     * @param string|null $type           An optional type of the exception (e.g., error, warning). Defaults to 
     *                                    ApiExceptionTypes::ERROR if not provided.
     * @param array|null $errorListMessage An optional array of error messages. Defaults to an empty array if 
     *                                    not provided.
     * @param int|null $restCode         An optional HTTP status code. Defaults to StatusCodes::HTTP_INTERNAL_SERVER_ERROR 
     *                                    if not provided.
     */
    public function __construct(
        ?bool $accept = null,
        ?string $type = null,
        ?array $errorListMessage = null,
        ?int $restCode = null
    ) {
        $this->setAccept($accept ?? true);
        $this->setType($type ?? ApiExceptionTypes::ERROR);
        $this->setErrorListMessage($errorListMessage ?? []);
        $this->setRestCode($restCode ?? StatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        
        parent::__construct($this->errorListMessage, $this->restCode);
    }

    /**
     * Gets the acceptance flag.
     *
     * @return bool The acceptance flag.
     */
    public function getAccept(): bool {
        return $this->accept;
    }

    /**
     * Gets the type of the exception.
     *
     * @return string The type of the exception.
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * Gets the error messages.
     *
     * @return string The concatenated error messages.
     */
    public function getErrorListMessage(): string {
        return $this->errorListMessage;
    }

    /**
     * Gets the HTTP status code associated with the exception.
     *
     * @return int The HTTP status code.
     */
    public function getRestCode(): int {
        return $this->restCode;
    }

    /**
     * Sets the acceptance flag.
     *
     * @param bool $accept The acceptance flag.
     * @return self The current instance for method chaining.
     */
    protected function setAccept(bool $accept): self {
        $this->accept = $accept;
        return $this;
    }

    /**
     * Sets the type of the exception.
     *
     * @param string $type The type of the exception.
     * @return self The current instance for method chaining.
     */
    protected function setType(string $type): self {
        $this->type = $type;
        return $this;
    }

    /**
     * Sets the error messages.
     *
     * @param array $errorListMessage The array of error messages.
     * @return self The current instance for method chaining.
     */
    protected function setErrorListMessage(array $errorListMessage): self {
        $this->errorListMessage = implode("|", $errorListMessage);
        return $this;
    }

    /**
     * Sets the HTTP status code associated with the exception.
     *
     * @param int $restCode The HTTP status code.
     * @return self The current instance for method chaining.
     */
    protected function setRestCode(int $restCode): self {
        $this->restCode = $restCode;
        return $this;
    }
}
?>