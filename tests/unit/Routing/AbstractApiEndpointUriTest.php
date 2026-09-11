<?php
namespace Routing;

use Fixtures\Api\FakeApi;
use Fixtures\Controllers\FakeController;
use Server\Routing\Route;

class AbstractApiEndpointUriTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _after()
    {
        $this->resetRoutes();
    }

    // comportamento esperado: com moduleName e endpoint definidos, a URI concatena os dois segmentos
    public function testEndpointUriWithModuleNameAndEndpointConcatenatesBothSegments()
    {
        $api = new FakeApi('module-y', null, null, false);
        $api->callAddEndpoint('GET', FakeController::class, 'staticNoParams', 'both-defined');

        $this->assertArrayHasKey('/module-y/both-defined', Route::fecthRouteList()['GET']);
    }

    // comportamento esperado: sem moduleName, a URI não ganha o segmento de módulo
    public function testEndpointUriWithoutModuleNameOmitsModuleSegment()
    {
        $api = new FakeApi(null, null, null, false);
        $api->callAddEndpoint('GET', FakeController::class, 'staticNoParams', 'only-endpoint');

        $this->assertArrayHasKey('/only-endpoint', Route::fecthRouteList()['GET']);
    }

    // comportamento esperado: sem endpoint, a URI não ganha o segundo segmento
    public function testEndpointUriWithoutEndpointPathOmitsEndpointSegment()
    {
        $api = new FakeApi('module-x', null, null, false);
        $api->callAddEndpoint('GET', FakeController::class, 'staticNoParams');

        $this->assertArrayHasKey('/module-x', Route::fecthRouteList()['GET']);
    }

    // comportamento esperado: sem moduleName nem endpoint, a URI registrada é string vazia
    public function testEndpointUriWithoutModuleNameOrEndpointIsEmptyString()
    {
        $api = new FakeApi(null, null, null, false);
        $api->callAddEndpoint('GET', FakeController::class, 'staticNoParams');

        $this->assertArrayHasKey('', Route::fecthRouteList()['GET']);
    }

    private function resetRoutes(): void
    {
        $property = (new \ReflectionClass(Route::class))->getProperty('allRoutesRoutesFunctions');
        $property->setAccessible(true);
        $property->setValue(null, [
            'GET'    => [],
            'POST'   => [],
            'PUT'    => [],
            'DELETE' => [],
        ]);
    }
}
