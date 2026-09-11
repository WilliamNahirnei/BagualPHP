<?php
namespace Router;

use ReflectionProperty;
use Server\Constants\StatusCodes;
use Server\Interfaces\InterfaceResponseContent;
use Server\Router\Response;

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

    // comportamento esperado: sem nenhum setter chamado, a instância nova reflete os defaults da interface
    public function testNewResponseHasDefaultStatusMessageAndContent()
    {
        $response = new Response();

        $this->assertSame(Response::DEFAULT_STATUS_CODE, Response::getStatusCode());
        $this->assertSame(Response::DEFAULT_MESSAGE, Response::getResponseMessage());
        $this->assertSame(Response::DEFAULT_CONTENT, $response->getResponseContent());
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

    // comportamento atual (característica): generateServerResponse() chama json_encode($dados, true) —
    // o segundo argumento é o parâmetro $flags (int), e `true` vira 1 (JSON_HEX_TAG). Isso faz "<"/">"
    // virarem </> no JSON devolvido ao cliente, um efeito colateral não documentado do código atual.
    public function testGenerateServerResponseEscapesAngleBracketsInMessage()
    {
        $response = new Response();
        Response::setResponseMessage('<script>alert(1)</script>');

        $json = $response->generateServerResponse();
        // monta "<script>" programaticamente para não escrever a sequência de escape literal no fonte
        $backslash = chr(92);
        $escapedOpeningTag = $backslash . 'u003Cscript' . $backslash . 'u003E';

        $this->assertStringNotContainsString('<script>', $json);
        $this->assertStringContainsString($escapedOpeningTag, $json);
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
