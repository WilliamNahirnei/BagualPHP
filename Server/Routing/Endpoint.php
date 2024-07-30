<?php

namespace Server\Routing;

use Server\Auth\AbstractAuthenticable;
use Server\Errors\AuthenticationException;
use Server\Suport\TraitSuportValidationClass;

class Endpoint {
    use TraitSuportValidationClass;
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

    public function executeEndpoint() {
        $this->authenticate();
        $this->validateExistenceEndpointExecutable();
        return call_user_func(
            [
                $this->getControllerClass(),
                $this->getControllerMethod()
            ]
        );
    }

    private function authenticate(): bool {
        if(empty($this->getAuthClass()) || empty($this->getAuthMethod())) {
            return true;
        }

        $this->validateExistenceAuthenticationDefined();
        $this->authClassIAutenticatle();

        $autenthicated = call_user_func(
            [
                $this->getAuthClass(),
                $this->getAuthMethod()
            ]
        );
        if (!$autenthicated) {
            throw new AuthenticationException();
        }
        return true;
    }

    private function validateExistenceAuthenticationDefined(): void {
        $this->classExists($this->getAuthClass());
        $this->methodExists($this->getAuthClass(), $this->getAuthMethod());
    }

    private function validateExistenceEndpointExecutable(): void {
        $this->classExists($this->getControllerClass());
        $this->methodExists($this->getControllerClass(), $this->getControllerMethod());
    }

    private function authClassIAutenticatle(){
        $this->classExtends($this->getAuthClass(), AbstractAuthenticable::class);
        $this->methodIsStatic($this->getAuthClass(), $this->getAuthMethod());
        $this->methodHasNoParameters($this->getAuthClass(), $this->getAuthMethod());
    }
}
?>