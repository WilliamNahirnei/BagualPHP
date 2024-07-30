<?php
namespace Server\Routing;

use Server\Interfaces\InterfaceRequestMethods;

/**
 * Abstract class AbstractApi
 * 
 * This class provides a base implementation for defining API endpoints.
 * 
 * @package Server\Routing
 */
abstract class AbstractApi implements InterfaceRequestMethods {
    /**
     * @var string|null The name of the module.
     */
    protected ?string $moduleName = null;

    /**
     * @var string|null The default authentication class.
     */
    protected ?string $defaultAuthClass = null;

    /**
     * @var string|null The default authentication method.
     */
    protected ?string $defaultAuthMethod = null;

    /**
     * Constructor for AbstractApi.
     *
     * @param string|null $moduleName The name of the module.
     * @param string|null $defaultAuthClass The default authentication class.
     * @param string|null $defaultAuthMethod The default authentication method.
     */
    public function __construct(
        ?string $moduleName = null,
        ?string $defaultAuthClass = null,
        ?string $defaultAuthMethod = null
    ) {
        if ($moduleName !== null) {
            $this->moduleName = $moduleName;
        }
        if ($defaultAuthClass !== null) {
            $this->defaultAuthClass = $defaultAuthClass;
        }
        if ($defaultAuthMethod !== null) {
            $this->defaultAuthMethod = $defaultAuthMethod;
        }
    }

    /**
     * Adds an endpoint to the route.
     *
     * @param string $requestType The type of the request (GET, POST, PUT, DELETE).
     * @param string|null $endpoint The endpoint URI.
     * @param string $controllerClass The controller class.
     * @param string $controllerMethod The controller method.
     * @param string|null $authClass The authentication class.
     * @param string|null $authMethod The authentication method.
     * @return void
     */
    protected function addEndpoint(
        string $requestType, 
        ?string $endpoint = null, 
        string $controllerClass, 
        string $controllerMethod, 
        ?string $authClass = null, 
        ?string $authMethod = null
    ): void {
        $endpoint = new Endpoint(
            $requestType,
            $this->defineEndpointUri($endpoint),
            $controllerClass,
            $controllerMethod,
            $authClass ?? $this->defaultAuthClass,
            $authMethod ?? $this->defaultAuthMethod
        );

        Route::{$requestType}($endpoint);
    }

    /**
     * Defines the endpoint URI.
     *
     * @param string|null $endpoint The endpoint URI.
     * @return string The defined endpoint URI.
     */
    private function defineEndpointUri(?string $endpoint = null): string {
        $endpointUri = "";
        $endpointUri .= $this->moduleName ? "/$this->moduleName" : "";
        $endpointUri .= $endpoint ? "/$endpoint" : "";
        return $endpointUri;
    }

    /**
     * Abstract method to define the list of endpoints.
     * 
     * This method must be implemented by subclasses to define the API endpoints.
     *
     * @return void
     */
    protected abstract function defineEndpointList(): void;
}
?>