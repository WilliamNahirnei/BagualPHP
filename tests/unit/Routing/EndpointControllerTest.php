<?php
namespace Routing;

use Fixtures\Controllers\FakeController;
use Server\Routing\Endpoint;

class EndpointControllerTest extends \Codeception\Test\Unit
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

    // comportamento esperado: método static sem parâmetros executa normalmente
    public function testStaticMethodWithoutParamsExecutesNormally()
    {
        $result = $this->makeEndpoint('staticNoParams')->executeEndpoint();

        $this->assertSame('executed', $result);
    }

    // comportamento esperado: classe de controller inexistente lança \Exception (via classExists da trait)
    public function testMissingControllerClassThrowsException()
    {
        $endpoint = new Endpoint('GET', '/fake', 'Fixtures\\Controllers\\DoesNotExist', 'staticNoParams', null, null, true);

        $this->expectException(\Exception::class);

        $endpoint->executeEndpoint();
    }

    // comportamento esperado: método de controller inexistente lança \Exception (via methodExists da trait)
    public function testMissingControllerMethodThrowsException()
    {
        $endpoint = $this->makeEndpoint('doesNotExist');

        $this->expectException(\Exception::class);

        $endpoint->executeEndpoint();
    }

    // item 4 do DIVIDA_TECNICA.md (corrigido): método não-estático é barrado por validateExistenceEndpointExecutable() antes da chamada, com mensagem clara
    public function testNonStaticControllerMethodIsRejectedBeforeExecution()
    {
        $endpoint = $this->makeEndpoint('nonStaticUsingThis');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Method 'nonStaticUsingThis' in class '" . FakeController::class . "' must be static");

        $endpoint->executeEndpoint();
    }

    // item 4 (corrigido): mesmo o caso mais perigoso do bug original — método não-estático que não usa $this e antes executava silenciosamente — agora é barrado antes da execução
    public function testNonStaticControllerMethodIsRejectedEvenWhenItDoesNotUseThis()
    {
        $endpoint = $this->makeEndpoint('nonStaticNotUsingThis');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Method 'nonStaticNotUsingThis' in class '" . FakeController::class . "' must be static");

        $endpoint->executeEndpoint();
    }

    // item 4 (corrigido): método com parâmetro obrigatório é barrado antes da chamada, em vez de falhar em runtime com ArgumentCountError
    public function testControllerMethodWithRequiredParamIsRejectedBeforeExecution()
    {
        $endpoint = $this->makeEndpoint('staticWithRequiredParam');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Method 'staticWithRequiredParam' in class '" . FakeController::class . "' must not have parameters");

        $endpoint->executeEndpoint();
    }

    // ignoreAuth = true isola o teste da resolução de autenticação (item 3 do DIVIDA_TECNICA.md), fora do escopo deste módulo
    private function makeEndpoint(string $controllerMethod): Endpoint
    {
        return new Endpoint('GET', '/fake', FakeController::class, $controllerMethod, null, null, true);
    }
}
