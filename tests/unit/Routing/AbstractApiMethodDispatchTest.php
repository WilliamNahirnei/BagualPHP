<?php
namespace Routing;

use Fixtures\Api\FakeApi;
use Fixtures\Controllers\FakeController;
use Server\Routing\Route;

class AbstractApiMethodDispatchTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _after()
    {
        $this->resetRoutes();
    }

    // comportamento esperado: addEndpoint('POST', ...) registra o endpoint via Route::post()
    public function testAddEndpointRegistersUnderPostWhenRequestTypeIsPost()
    {
        $api = new FakeApi(null, null, null, false);
        $api->callAddEndpoint('POST', FakeController::class, 'staticNoParams', 'fake-post');

        $this->assertArrayHasKey('/fake-post', Route::fecthRouteList()['POST']);
    }

    // comportamento esperado: addEndpoint('PUT', ...) registra o endpoint via Route::put()
    public function testAddEndpointRegistersUnderPutWhenRequestTypeIsPut()
    {
        $api = new FakeApi(null, null, null, false);
        $api->callAddEndpoint('PUT', FakeController::class, 'staticNoParams', 'fake-put');

        $this->assertArrayHasKey('/fake-put', Route::fecthRouteList()['PUT']);
    }

    // comportamento esperado: addEndpoint('DELETE', ...) registra o endpoint via Route::delete()
    public function testAddEndpointRegistersUnderDeleteWhenRequestTypeIsDelete()
    {
        $api = new FakeApi(null, null, null, false);
        $api->callAddEndpoint('DELETE', FakeController::class, 'staticNoParams', 'fake-delete');

        $this->assertArrayHasKey('/fake-delete', Route::fecthRouteList()['DELETE']);
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
