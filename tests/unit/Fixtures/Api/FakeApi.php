<?php
namespace Fixtures\Api;

use Server\Routing\AbstractApi;

class FakeApi extends AbstractApi {
    public function callAddEndpoint(
        string $requestType,
        string $controllerClass,
        string $controllerMethod,
        ?string $endpoint = null,
        ?string $authClass = null,
        ?string $authMethod = null,
        ?bool $ignoreAuth = null
    ): void {
        $this->addEndpoint($requestType, $endpoint, $controllerClass, $controllerMethod, $authClass, $authMethod, $ignoreAuth);
    }

    protected function defineEndpointList(): void {
    }
}
