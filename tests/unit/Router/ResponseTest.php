<?php
namespace Router;

use ReflectionProperty;
use Server\Constants\ApiExceptionTypes;
use Server\Constants\ServerMessage;
use Server\Constants\StatusCodes;
use Server\Errors\ApiException;
use Server\Interfaces\InterfaceResponseContent;
use Server\Router\Response;
use Server\Router\Router;

class ResponseTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        Response::setStatusCode(Response::DEFAULT_STATUS_CODE);
        Response::setResponseMessage(Response::DEFAULT_MESSAGE);
        $this->resetHeaders();
    }

    protected function _after()
    {
        Response::setStatusCode(Response::DEFAULT_STATUS_CODE);
        Response::setResponseMessage(Response::DEFAULT_MESSAGE);
        $this->resetHeaders();
    }

    // comportamento esperado
    public function testMountCompleteResponseReturnsMessageAndDataKeys()
    {
        $response = new Response();
        Response::setResponseMessage('foo');
        $response->setResponseContent(['x' => 1]);

        $result = $response->mountCompleteResponse();

        $this->assertSame([
            InterfaceResponseContent::RESPONSE_MESSAGE => 'foo',
            InterfaceResponseContent::RESPONSE_DATA => ['x' => 1],
        ], $result);
    }

    // comportamento esperado
    public function testGenerateServerResponseSetsHttpStatusCode()
    {
        $response = new Response();
        Response::setStatusCode(StatusCodes::HTTP_BAD_REQUEST);

        $response->generateServerResponse();

        $this->assertSame(StatusCodes::HTTP_BAD_REQUEST, http_response_code());
    }

    // comportamento esperado
    public function testAddHeaderAccumulatesMultipleValuesForSameHeader()
    {
        Response::addHeader('X-Custom', 'a');
        Response::addHeader('X-Custom', 'b');

        $this->assertSame(['X-Custom' => ['a', 'b']], $this->getHeaders());
    }

    // comportamento esperado (contraste com o teste abaixo)
    public function testApiExceptionMessageDoesNotLeakStackTrace()
    {
        $router = $this->makeRouter();
        $apiException = new ApiException(true, ApiExceptionTypes::ERROR, ['Recurso inválido'], StatusCodes::HTTP_BAD_REQUEST);

        $router->defineApiExceptionErrorResponse($apiException);

        $this->assertSame('Recurso inválido', Response::getResponseMessage());
        $this->assertStringNotContainsString('Stack trace', Response::getResponseMessage());
    }

    // comportamento esperado: expor o \Throwable bruto é intencional (ver PHPDoc de Router::generateInternalErrorMessage())
    public function testInternalErrorResponseMessageContainsStackTrace()
    {
        $router = $this->makeRouter();
        $error = new \Exception('erro genérico');

        $router->defineInternalErrorResponse($error);

        $message = Response::getResponseMessage();
        $this->assertStringContainsString(ServerMessage::INTERNAL_SERVER_ERRO, $message);
        $this->assertStringContainsString('Stack trace', $message);
        $this->assertStringContainsString(__FILE__, $message);
    }

    private function makeRouter(): Router
    {
        $_SERVER['REQUEST_URI'] = '/api/teste';

        return new Router();
    }

    // Response não expõe getter público para $headers, só addHeader(), que sempre acumula
    private function getHeaders(): array
    {
        return $this->headersProperty()->getValue();
    }

    // Response não expõe nenhum setter/reset público para $headers
    private function resetHeaders(): void
    {
        $this->headersProperty()->setValue(null, []);
    }

    private function headersProperty(): ReflectionProperty
    {
        $property = new ReflectionProperty(Response::class, 'headers');
        $property->setAccessible(true);
        return $property;
    }
}
