<?php

namespace Server\Routing;

use Server\Auth\AbstractAuthenticable;
use Server\Auth\AuthConfig;
use Server\Errors\AuthenticationException;
use Server\Suport\TraitSuportValidationClass;

/**
 * Class Endpoint
 * 
 * Represents an API endpoint with authentication and validation capabilities.
 *
 * @package Server\Routing
 * @author William Nahirnei Lopes
 */
class Endpoint {
    use TraitSuportValidationClass;


    /**
     * The default authentication method used when no specific method is provided.
     *
     * @var string The name of the default authentication method.
     */
    const DEFAULT_AUTH_METHOD = "authenticate";

    /**
     * @var string The type of HTTP request (GET, POST, PUT, DELETE).
     */
    private string $requestType;

    /**
     * @var string The endpoint URI.
     */
    private string $endpoint;

    /**
     * @var string The controller class handling the request.
     */
    private string $controllerClass;

    /**
     * @var string The method in the controller class to be called.
     */
    private string $controllerMethod;

    /**
     * @var ?string The authentication class.
     */
    private ?string $authClass;

    /**
     * @var ?string The method in the authentication class.
     */
    private ?string $authMethod;

    /**
     * @var AuthConfig instance to use auth configs.
     */
    private AuthConfig $authConfig;

    /**
     * @var bool Indicates whether authentication should be ignored.
     * 
     */
    private bool $ignoreAuth;
    /**
     * Endpoint constructor.
     * 
     * @param string $requestType The type of HTTP request (GET, POST, PUT, DELETE).
     * @param string $endpoint The endpoint URI.
     * @param string $controllerClass The controller class handling the request.
     * @param string $controllerMethod The method in the controller class to be called.
     * @param ?string $authClass The authentication class.
     * @param ?string $authMethod The method in the authentication class.
     * @param bool $ignoreAuth Indicates whether authentication should be ignored.
    */
    public function __construct(
        string $requestType, 
        string $endpoint, 
        string $controllerClass, 
        string $controllerMethod, 
        ?string $authClass, 
        ?string $authMethod,
        bool $ignoreAuth
    ) {
        $this->requestType = $requestType;
        $this->endpoint = $endpoint;
        $this->controllerClass = $controllerClass;
        $this->controllerMethod = $controllerMethod;
        $this->authClass = $authClass;
        $this->authMethod = $authMethod;
        $this->authConfig = AuthConfig::getInstance();
        $this->ignoreAuth = $ignoreAuth;
    }

    /**
     * Gets the type of HTTP request.
     *
     * @return string The request type.
     */
    public function getRequestType(): string {
        return $this->requestType;
    }

    /**
     * Gets the endpoint URI.
     *
     * @return string The endpoint URI.
     */
    public function getEndpoint(): string {
        return $this->endpoint;
    }

    /**
     * Gets the controller class handling the request.
     *
     * @return string The controller class.
     */
    public function getControllerClass(): string {
        return $this->controllerClass;
    }

    /**
     * Gets the method in the controller class to be called.
     *
     * @return string The controller method.
     */
    public function getControllerMethod(): string {
        return $this->controllerMethod;
    }

    /**
     * Gets the authentication class.
     *
     * @return ?string The authentication class.
     */
    public function getAuthClass(): ?string {
        return $this->authClass;
    }

    /**
     * Gets the method in the authentication class.
     *
     * @return ?string The authentication method.
     */
    public function getAuthMethod(): ?string {
        return $this->authMethod;
    }

    /**
     * Retrieves the instance of AuthConfig.
     *
     * @return AuthConfig Returns the instance of the authentication configuration.
     */
    private function getAuthConfig(): AuthConfig {
        return $this->authConfig;
    }

    /**
     * Gets the value of the ignoreAuth property.
     *
     * @return bool|null Returns true if authentication is to be ignored, false otherwise. 
     *                   Returns null if the ignoreAuth property is not explicitly set.
     */
    private function ignoreAuth(): ?bool {
        return $this->ignoreAuth;
    }

    /**
     * Executes the endpoint by authenticating and then calling the controller method.
     *
     * @return mixed The result of the controller method.
     * @throws AuthenticationException If authentication fails.
     */
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

    /**
     * Authenticates the request using the specified authentication class and method.
     *
     * @return bool True if authentication is successful, otherwise throws an exception.
     * @throws AuthenticationException If authentication fails.
     */
    private function authenticate(): bool {
        if($this->ignoreAuth()) {
            return true;
        }
        if (empty($this->getAuthClass())) {
            $this->loadDefaultAuthApp();
        }
        if (empty($this->getAuthClass()) || empty($this->getAuthMethod())) {
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

    /**
     * Loads the default authentication class and method.
     * 
     * This method retrieves the default authentication class from the
     * authentication configuration using the namespace specified by
     * `AuthConfig::DEFAULT_CLASS_NAMESPACE`. It also sets the default
     * authentication method using the `DEFAULT_AUTH_METHOD` constant.
     * 
     * @return void
     */
    private function loadDefaultAuthApp(): void {
        $this->authClass = $this->getAuthConfig()->getConfig(AuthConfig::DEFAULT_CLASS_NAMESPACE);
        $this->authMethod = self::DEFAULT_AUTH_METHOD;
    }

    /**
     * Validates the existence of the authentication class and method.
     *
     * @return void
     * @throws \Exception If the authentication class or method does not exist.
     */
    private function validateExistenceAuthenticationDefined(): void {
        $this->classExists($this->getAuthClass());
        $this->methodExists($this->getAuthClass(), $this->getAuthMethod());
    }

    /**
     * Validates the existence of the controller class and method.
     *
     * @return void
     * @throws \Exception If the controller class or method does not exist.
     */
    private function validateExistenceEndpointExecutable(): void {
        $this->classExists($this->getControllerClass());
        $this->methodExists($this->getControllerClass(), $this->getControllerMethod());
    }

    /**
     * Validates that the authentication class extends AbstractAuthenticable, and that the authentication method is static and has no parameters.
     *
     * @return void
     * @throws \Exception If the authentication class or method does not meet the required conditions.
     */
    private function authClassIAutenticatle() {
        $this->classExtends($this->getAuthClass(), AbstractAuthenticable::class);
        $this->methodIsStatic($this->getAuthClass(), $this->getAuthMethod());
        $this->methodHasNoParameters($this->getAuthClass(), $this->getAuthMethod());
    }
}
?>