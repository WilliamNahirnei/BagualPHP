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

    // item 4 do DIVIDA_TECNICA.md: PATTERN_OR não tem \b no início, então "or" isolado em texto legítimo é apagado
    public function testIsolatedOrWordRemovedFromLegitimateText()
    {
        $result = $this->sanitize(['nome' => 'Rafael or Silva']);

        $this->assertSame('Rafael  Silva', $result['nome']);
    }

    // item 4: qualquer palavra terminada em "or" tem o sufixo apagado, não só a palavra isolada "or"
    public function testWordEndingInOrGetsTruncated()
    {
        $result = $this->sanitize(['profissao' => 'motor']);

        $this->assertSame('mot', $result['profissao']);
    }

    // item 4: substantivos comuns em português terminados em "-or"/"-dor" são truncados da mesma forma
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

    // item 4: "@" conta como fronteira de palavra, então e-mails começando com "user"/"admin" são corrompidos
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

    // comportamento esperado: sanitizeParams() só trata string/array; outros tipos passam intactos
    public function testNonStringNonArrayValuesPassThroughUnchanged()
    {
        $result = $this->sanitize([
            'idade' => 30,
            'ativo' => true,
            'nulo' => null,
            'preco' => 12.5,
        ]);

        $this->assertSame(30, $result['idade']);
        $this->assertTrue($result['ativo']);
        $this->assertNull($result['nulo']);
        $this->assertSame(12.5, $result['preco']);
    }

    // comportamento esperado: getAllMergedParams() combina query params e body params num único array
    public function testGetAllMergedParamsMergesQueryAndBody()
    {
        $request = $this->makeRequestWithoutConstructor();
        $this->setPrivateProperty($request, 'queryParams', ['a' => '1']);
        $this->setPrivateProperty($request, 'bodyParams', ['b' => '2']);

        $this->assertSame(['a' => '1', 'b' => '2'], $request->getAllMergedParams());
    }

    // item 4 (resolvido por remoção): a chamada a sanitizeParams() foi removida do construtor de Request,
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

    // comportamento esperado: o construtor real também popula method/uri a partir das superglobais
    // (getBodyParams/getHeaders/getFiles dependem de php://input/$_FILES, mais difíceis de popular em
    // CLI, então ficam cobertos só via Reflection nos testes isolados abaixo)
    public function testMethodAndUriArePopulatedFromServerSuperglobalsThroughRealConstructor()
    {
        $originalServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/outra-rota';

        $this->resetSingletonInstance();
        $request = Request::getInstance();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/api/outra-rota', $request->getUri());

        $_SERVER = $originalServer;
        $this->resetSingletonInstance();
    }

    // comportamento esperado
    public function testGetBodyParamsReturnsStoredBodyParams()
    {
        $request = $this->makeRequestWithoutConstructor();
        $this->setPrivateProperty($request, 'bodyParams', ['b' => '2']);

        $this->assertSame(['b' => '2'], $request->getBodyParams());
    }

    // comportamento esperado
    public function testGetHeadersReturnsStoredHeaders()
    {
        $request = $this->makeRequestWithoutConstructor();
        $this->setPrivateProperty($request, 'headers', ['Authorization' => 'Bearer token']);

        $this->assertSame(['Authorization' => 'Bearer token'], $request->getHeaders());
    }

    // comportamento esperado
    public function testGetMethodReturnsStoredMethod()
    {
        $request = $this->makeRequestWithoutConstructor();
        $this->setPrivateProperty($request, 'method', 'POST');

        $this->assertSame('POST', $request->getMethod());
    }

    // comportamento esperado
    public function testGetUriReturnsStoredUri()
    {
        $request = $this->makeRequestWithoutConstructor();
        $this->setPrivateProperty($request, 'uri', '/api/teste');

        $this->assertSame('/api/teste', $request->getUri());
    }

    // comportamento esperado
    public function testGetFilesReturnsStoredFiles()
    {
        $request = $this->makeRequestWithoutConstructor();
        $this->setPrivateProperty($request, 'files', ['arquivo' => ['name' => 'foto.png']]);

        $this->assertSame(['arquivo' => ['name' => 'foto.png']], $request->getFiles());
    }

    // Request lê superglobais no construtor (item 5 do DIVIDA_TECNICA.md), então instanciamos sem passar por ele
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
