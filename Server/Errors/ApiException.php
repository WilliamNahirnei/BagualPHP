<?php
namespace Server\Errors;

use Server\Constants\StatusCodes;

class ApiException extends \Exception {
    private bool $accept;
    private string $type;
    private string $errorListMessage;
    private int $restCode;

    public function __construct(bool $accept = true, string $type = 'error', array $errorListMessage = [], int $restCode = StatusCodes::HTTP_INTERNAL_SERVER_ERROR) {
        $this->setAccept($accept);
        $this->setType($type);
        $this->setErrorListMessage($errorListMessage);
        $this->setRestCode($restCode);
        
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
    private function setAccept(bool $accept): self {
        $this->accept = $accept;
        return $this;
    }

    private function setType(string $type): self {
        $this->type = $type;
        return $this;
    }

    private function setErrorListMessage(array $errorListMessage): self {
        $this->errorListMessage = implode("|", $errorListMessage);
        return $this;
    }

    private function setRestCode(int $restCode): self {
        $this->restCode = $restCode;
        return $this;
    }
}
?>