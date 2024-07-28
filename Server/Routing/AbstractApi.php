<?php
namespace Server\Routing;

use Server\Interfaces\InterfaceRequestMethods;

abstract class AbstractApi implements InterfaceRequestMethods {
    protected ?string $moduleName = null;
    protected ?string $defaultAuthClass = null;
    protected ?string $defaultAuthMethod = null;

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

    protected function addEndpoint(
        string $requestType, 
        ?string $endpoint = null, 
        string $controllerClass, 
        string $controllerMethod, 
        ?string $authClass = null, 
        ?string $authMethod = null
    ) {
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

    private function defineEndpointUri(?string $endpoint = null): string {
        $endpointUri = "";
        $endpointUri .= $this->moduleName ? "/$this->moduleName" : "";
        $endpointUri .= $endpoint ? "/$endpoint" : "";
        return $endpointUri;
    }

    protected abstract function defineEndpointList();
}
?>