<?php
namespace Router;

use Fixtures\Controllers\FakeController;
use ReflectionProperty;
use Server\Constants\ApiExceptionTypes;
use Server\Constants\ServerMessage;
use Server\Constants\StatusCodes;
use Server\Errors\ApiException;
use Server\Router\Request;
use Server\Router\Response;
use Server\Router\Router;
use Server\Router\RouterConfig;
use Server\Routing\Endpoint;
use Server\Routing\Route;

class RouterRequestFlowTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private array $originalServer;
    private array $originalGet;
    private array $originalRouterConfig;

    protected function _before()
    {
        $this->originalServer = $_SERVER;
        $this->originalGet = $_GET;
        $this->originalRouterConfig = $this->routerConfigProperty()->getValue(RouterConfig::getInstance());

        $this->resetRoutes();
        $this->resetRequestSingleton();
        $this->resetResponseState();
    }

    protected function _after()
    {
        $_SERVER = $this->originalServer;
        $_GET = $this->originalGet;
        $this->routerConfigProperty()->setValue(RouterConfig::getInstance(), $this->originalRouterConfig);

        $this->resetRoutes();
        $this->resetRequestSingleton();
        $this->resetResponseState();
    }

    // comportamento esperado
    public function testValidPrefixDoesNotThrowWhenPrefixApiIsConfigured()
    {
        $this->setPrefixApi('api');
        $this->setServerVars('/api/fake', '/api/fake', 'GET');
        Route::get($this->makeEndpoint('GET', '/fake'));

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_OK, Response::getStatusCode());
    }

    // comportamento esperado
    public function testInvalidPrefixThrowsNotFoundWhenPrefixApiIsConfigured()
    {
        $this->setPrefixApi('api');
        $this->setServerVars('/outro-prefixo/x', '/outro-prefixo/x', 'GET');

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_NOT_FOUND, Response::getStatusCode());
        $this->assertStringContainsString('api ' . ServerMessage::NOT_FOUND, Response::getResponseMessage());
    }

    // comportamento esperado
    public function testAnyPrefixIsAcceptedWhenPrefixApiIsEmpty()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/x', '/x', 'GET');
        Route::get($this->makeEndpoint('GET', '/x'));

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_OK, Response::getStatusCode());
    }

    // comportamento esperado
    public function testPrefixIsStrippedFromRouteBeforeMatching()
    {
        $this->setPrefixApi('api');
        $this->setServerVars('/api/users', '/api/users', 'GET');
        Route::get($this->makeEndpoint('GET', '/users'));

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_OK, Response::getStatusCode());
    }

    // comportamento esperado
    public function testRegisteredRouteExecutesControllerAndSetsResponseContent()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/fake', '/fake', 'GET');
        Route::get($this->makeEndpoint('GET', '/fake'));

        $output = $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_OK, Response::getStatusCode());
        $this->assertSame('executed', json_decode($output, true)['data']);
    }

    // comportamento esperado: o pipeline completo também despacha corretamente métodos além de GET
    public function testRegisteredNonGetRouteExecutesControllerAndSetsResponseContent()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/fake-post', '/fake-post', 'POST');
        Route::post($this->makeEndpoint('POST', '/fake-post'));

        $output = $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_OK, Response::getStatusCode());
        $this->assertSame('executed', json_decode($output, true)['data']);
    }

    // comportamento esperado
    public function testUnregisteredRouteThrowsNotFoundApiException()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/does-not-exist', '/does-not-exist', 'GET');

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_NOT_FOUND, Response::getStatusCode());
        $this->assertStringContainsString(ServerMessage::ROUTE . ' ' . ServerMessage::NOT_FOUND, Response::getResponseMessage());
    }

    // comportamento esperado
    public function testOptionsRequestSucceedsWhenMatchingPostRouteExists()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/fake-options-post', '/fake-options-post', 'OPTIONS');
        Route::post($this->makeEndpoint('POST', '/fake-options-post'));

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_OK, Response::getStatusCode());
        $this->assertSame(Response::DEFAULT_MESSAGE, Response::getResponseMessage());
    }

    // comportamento esperado: mesmo ramo (OR de POST/PUT/DELETE), caminho independente com PUT
    public function testOptionsRequestSucceedsWhenMatchingPutRouteExists()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/fake-options-put', '/fake-options-put', 'OPTIONS');
        Route::put($this->makeEndpoint('PUT', '/fake-options-put'));

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_OK, Response::getStatusCode());
        $this->assertSame(Response::DEFAULT_MESSAGE, Response::getResponseMessage());
    }

    // comportamento esperado: mesmo ramo (OR de POST/PUT/DELETE), caminho independente com DELETE
    public function testOptionsRequestSucceedsWhenMatchingDeleteRouteExists()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/fake-options-delete', '/fake-options-delete', 'OPTIONS');
        Route::delete($this->makeEndpoint('DELETE', '/fake-options-delete'));

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_OK, Response::getStatusCode());
        $this->assertSame(Response::DEFAULT_MESSAGE, Response::getResponseMessage());
    }

    // comportamento esperado
    public function testOptionsRequestThrowsNotFoundWhenNoNonGetRouteExists()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/fake-options-sem-rota', '/fake-options-sem-rota', 'OPTIONS');

        $this->runRequest();

        $this->assertSame(StatusCodes::HTTP_NOT_FOUND, Response::getStatusCode());
    }

    // comportamento esperado: expor o \Throwable bruto é intencional (ver PHPDoc de Router::generateInternalErrorMessage())
    public function testUnhandledThrowableFromControllerFallsBackToInternalErrorResponse()
    {
        $this->setPrefixApi('');
        $this->setServerVars('/throws', '/throws', 'GET');
        Route::get($this->makeEndpoint('GET', '/throws', 'staticThrowsException'));

        $this->runRequest();

        $message = Response::getResponseMessage();
        $this->assertStringContainsString(ServerMessage::INTERNAL_SERVER_ERRO, $message);
        $this->assertStringContainsString('Stack trace', $message);
    }

    // comportamento esperado (contraste com o teste abaixo)
    public function testApiExceptionMessageDoesNotLeakStackTrace()
    {
        $this->setServerVars('/api/teste', '/api/teste', 'GET');
        $router = new Router();
        $apiException = new ApiException(true, ApiExceptionTypes::ERROR, ['Recurso inválido'], StatusCodes::HTTP_BAD_REQUEST);

        $router->defineApiExceptionErrorResponse($apiException);

        $this->assertSame('Recurso inválido', Response::getResponseMessage());
        $this->assertStringNotContainsString('Stack trace', Response::getResponseMessage());
    }

    // comportamento esperado: expor o \Throwable bruto é intencional (ver PHPDoc de Router::generateInternalErrorMessage())
    public function testInternalErrorResponseMessageContainsStackTrace()
    {
        $this->setServerVars('/api/teste', '/api/teste', 'GET');
        $router = new Router();
        $error = new \Exception('erro genérico');

        $router->defineInternalErrorResponse($error);

        $message = Response::getResponseMessage();
        $this->assertStringContainsString(ServerMessage::INTERNAL_SERVER_ERRO, $message);
        $this->assertStringContainsString('Stack trace', $message);
        $this->assertStringContainsString(__FILE__, $message);
    }

    private function makeEndpoint(string $requestType, string $uri, string $controllerMethod = 'staticNoParams'): Endpoint
    {
        return new Endpoint($requestType, $uri, FakeController::class, $controllerMethod, null, null, true);
    }

    private function setServerVars(string $requestUri, string $pathInfo, string $requestMethod): void
    {
        $_SERVER['REQUEST_URI'] = $requestUri;
        $_SERVER['PATH_INFO'] = $pathInfo;
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
        $_GET = [];
    }

    private function setPrefixApi(string $value): void
    {
        $this->routerConfigProperty()->setValue(RouterConfig::getInstance(), [RouterConfig::PREFIX_API => $value]);
    }

    private function runRequest(): string
    {
        $this->resetRequestSingleton();

        ob_start();
        (new Router())->executeRequest();
        return ob_get_clean();
    }

    private function routerConfigProperty(): ReflectionProperty
    {
        $property = new ReflectionProperty(RouterConfig::class, 'config');
        $property->setAccessible(true);
        return $property;
    }

    private function resetRoutes(): void
    {
        $property = new ReflectionProperty(Route::class, 'allRoutesRoutesFunctions');
        $property->setAccessible(true);
        $property->setValue(null, [
            'GET'    => [],
            'POST'   => [],
            'PUT'    => [],
            'DELETE' => [],
        ]);
    }

    private function resetRequestSingleton(): void
    {
        $property = new ReflectionProperty(Request::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    private function resetResponseState(): void
    {
        Response::setStatusCode(Response::DEFAULT_STATUS_CODE);
        Response::setResponseMessage(Response::DEFAULT_MESSAGE);

        $property = new ReflectionProperty(Response::class, 'headers');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }
}
