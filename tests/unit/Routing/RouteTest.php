<?php
namespace Routing;

use Fixtures\Controllers\FakeController;
use ReflectionClass;
use Server\Routing\Endpoint;
use Server\Routing\Route;

class RouteTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->resetRoutes();
    }

    protected function _after()
    {
        $this->resetRoutes();
    }

    // comportamento esperado
    public function testPostRegistersEndpointUnderPostMethod()
    {
        $endpoint = $this->makeEndpoint('POST', '/fake-post');

        Route::post($endpoint);

        $this->assertSame($endpoint, Route::fecthRouteList()['POST']['/fake-post']);
    }

    // comportamento esperado
    public function testPutRegistersEndpointUnderPutMethod()
    {
        $endpoint = $this->makeEndpoint('PUT', '/fake-put');

        Route::put($endpoint);

        $this->assertSame($endpoint, Route::fecthRouteList()['PUT']['/fake-put']);
    }

    // comportamento esperado
    public function testDeleteRegistersEndpointUnderDeleteMethod()
    {
        $endpoint = $this->makeEndpoint('DELETE', '/fake-delete');

        Route::delete($endpoint);

        $this->assertSame($endpoint, Route::fecthRouteList()['DELETE']['/fake-delete']);
    }

    private function makeEndpoint(string $requestType, string $uri): Endpoint
    {
        return new Endpoint($requestType, $uri, FakeController::class, 'staticNoParams', null, null, true);
    }

    private function resetRoutes(): void
    {
        $property = (new ReflectionClass(Route::class))->getProperty('allRoutesRoutesFunctions');
        $property->setAccessible(true);
        $property->setValue(null, [
            'GET'    => [],
            'POST'   => [],
            'PUT'    => [],
            'DELETE' => [],
        ]);
    }
}
