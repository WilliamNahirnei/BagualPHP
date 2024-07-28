<?php

namespace Server\Routing;

class Endpoint {
    private string $requestType;
    private string $endpoint;
    private string $controllerClass;
    private string $controllerMethod;
    private ?string $authClass;
    private ?string $authMethod;

    public function __construct(
        string $requestType, 
        string $endpoint, 
        string $controllerClass, 
        string $controllerMethod, 
        ?string $authClass, 
        ?string $authMethod
    ) {
        $this->requestType = $requestType;
        $this->endpoint = $endpoint;
        $this->controllerClass = $controllerClass;
        $this->controllerMethod = $controllerMethod;
        $this->authClass = $authClass;
        $this->authMethod = $authMethod;
    }

    public function getRequestType(): string {
        return $this->requestType;
    }

    public function getEndpoint(): string {
        return $this->endpoint;
    }

    public function getControllerClass(): string {
        return $this->controllerClass;
    }

    public function getControllerMethod(): string {
        return $this->controllerMethod;
    }

    public function getAuthClass(): ?string {
        return $this->authClass;
    }

    public function getAuthMethod(): ?string {
        return $this->authMethod;
    }
}
?>