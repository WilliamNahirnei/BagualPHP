<?php
namespace Router;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Server\Router\Request;

class RequestTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // item 6 do DIVIDA_TECNICA.md: PATTERN_OR não tem \b no início, então "or" isolado em texto legítimo é apagado
    public function testIsolatedOrWordRemovedFromLegitimateText()
    {
        $result = $this->sanitize(['nome' => 'Rafael or Silva']);

        $this->assertSame('Rafael  Silva', $result['nome']);
    }

    // item 6: qualquer palavra terminada em "or" tem o sufixo apagado, não só a palavra isolada "or"
    public function testWordEndingInOrGetsTruncated()
    {
        $result = $this->sanitize(['profissao' => 'motor']);

        $this->assertSame('mot', $result['profissao']);
    }

    // item 6: substantivos comuns em português terminados em "-or"/"-dor" são truncados da mesma forma
    public function testCommonPtBrWordsEndingInOrAreTruncated()
    {
        $result = $this->sanitize([
            'a' => 'professor',
            'b' => 'doutor',
            'c' => 'administrador',
        ]);

        $this->assertSame('profess', $result['a']);
        $this->assertSame('dout', $result['b']);
        $this->assertSame('administrad', $result['c']);
    }

    // item 6: "@" conta como fronteira de palavra, então e-mails começando com "user"/"admin" são corrompidos
    public function testEmailStartingWithReservedWordIsCorrupted()
    {
        $result = $this->sanitize([
            'email1' => 'user@example.com',
            'email2' => 'admin@example.com',
        ]);

        $this->assertSame('@example.com', $result['email1']);
        $this->assertSame('@example.com', $result['email2']);
    }

    // comportamento esperado: payloads reais de SQL injection continuam sendo neutralizados
    public function testActualSqlInjectionPayloadIsStillStripped()
    {
        $result = $this->sanitize([
            'a' => '1 OR 1=1',
            'b' => "'; DROP TABLE users; --",
        ]);

        $this->assertStringNotContainsStringIgnoringCase('or', $result['a']);
        $this->assertStringNotContainsStringIgnoringCase('drop', $result['b']);
        $this->assertStringNotContainsString(';', $result['b']);
        $this->assertStringNotContainsString('--', $result['b']);
    }

    // comportamento esperado: sanitizeParams() sanitiza recursivamente valores dentro de arrays aninhados
    public function testNestedArraysAreSanitizedRecursively()
    {
        $result = $this->sanitize([
            'filtro' => [
                'nome' => 'Rafael or Silva',
                'sub' => [
                    'cargo' => 'motor',
                ],
            ],
        ]);

        $this->assertSame('Rafael  Silva', $result['filtro']['nome']);
        $this->assertSame('mot', $result['filtro']['sub']['cargo']);
    }

    // comportamento esperado: getAllMergedParams() combina query params e body params num único array
    public function testGetAllMergedParamsMergesQueryAndBody()
    {
        $request = $this->makeRequestWithoutConstructor();
        $this->setPrivateProperty($request, 'queryParams', ['a' => '1']);
        $this->setPrivateProperty($request, 'bodyParams', ['b' => '2']);

        $this->assertSame(['a' => '1', 'b' => '2'], $request->getAllMergedParams());
    }

    // item 6 (resolvido por remoção): a chamada a sanitizeParams() foi removida do construtor de Request,
    // então valores que antes eram corrompidos (sufixo "-or", "@" após "user"/"admin", símbolos --/#/;/*)
    // agora chegam intactos em getQueryParams(), pois passam direto de $_GET sem qualquer sanitização.
    public function testQueryParamsArriveUnalteredNowThatSanitizationWasRemovedFromTheFlow()
    {
        $originalGet = $_GET;
        $originalServer = $_SERVER;
        $_GET = [
            'nome' => 'Rafael or Silva',
            'profissao' => 'motor',
            'email' => 'admin@example.com',
            'obs' => 'Apto #302, Rua 5 de Outubro; *importante* -- confirmar',
        ];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/teste';

        $this->resetSingletonInstance();
        $request = Request::getInstance();

        $this->assertSame($_GET, $request->getQueryParams());

        $_GET = $originalGet;
        $_SERVER = $originalServer;
        $this->resetSingletonInstance();
    }

    // Request lê superglobais no construtor (item 7 do DIVIDA_TECNICA.md), então instanciamos sem passar por ele
    private function makeRequestWithoutConstructor(): Request
    {
        return (new ReflectionClass(Request::class))->newInstanceWithoutConstructor();
    }

    private function resetSingletonInstance(): void
    {
        $property = new ReflectionProperty(Request::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    private function sanitize(array $params): array
    {
        $request = $this->makeRequestWithoutConstructor();
        $method = new ReflectionMethod(Request::class, 'sanitizeParams');
        $method->setAccessible(true);

        return $method->invoke($request, $params);
    }

    private function setPrivateProperty(Request $request, string $property, $value): void
    {
        $reflectionProperty = new ReflectionProperty(Request::class, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($request, $value);
    }
}
