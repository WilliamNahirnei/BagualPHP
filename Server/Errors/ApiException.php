<?php
namespace Server\Errors;

use Server\Constants\ApiExceptionTypes;
use Server\Constants\StatusCodes;

class ApiException extends \Exception {
    private bool $accept;
    private string $type;
    private string $errorListMessage;
    private int $restCode;

    public function __construct(?bool $accept = null, ?string $type = null, ?array $errorListMessage = null, ?int $restCode = null) {
        $this->setAccept($accept ?? true);
        $this->setType($type ?? ApiExceptionTypes::ERROR);
        $this->setErrorListMessage($errorListMessage ?? []);
        $this->setRestCode($restCode ?? StatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        
        parent::__construct($this->errorListMessage, $this->restCode);
    }

    // Getters
    public function getAccept(): bool {
        return $this->accept;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getErrorListMessage(): string {
        return $this->errorListMessage;
    }

    public function getRestCode(): int {
        return $this->restCode;
    }

    // Setters
    protected function setAccept(bool $accept): self {
        $this->accept = $accept;
        return $this;
    }

    protected function setType(string $type): self {
        $this->type = $type;
        return $this;
    }

    protected function setErrorListMessage(array $errorListMessage): self {
        $this->errorListMessage = implode("|", $errorListMessage);
        return $this;
    }

    protected function setRestCode(int $restCode): self {
        $this->restCode = $restCode;
        return $this;
    }
}
?>