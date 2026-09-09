<?php
namespace Routing;

use Fixtures\Api\FakeApi;
use Fixtures\Auth\FakeAuth;
use Fixtures\Controllers\FakeController;
use Server\Errors\AuthenticationException;
use Server\Routing\Route;

class AbstractApiAuthTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        FakeAuth::$shouldAuthenticate = true;
    }

    protected function _after()
    {
        $this->resetRoutes();
    }

    // comportamento esperado: authClass/authMethod do endpoint prevalecem sobre os defaults do módulo
    public function testEndpointAuthClassOverridesModuleDefaultAuthClass()
    {
        $api = new FakeApi('module-1', 'Fixtures\\Auth\\ModuleDefaultAuth', 'moduleMethod', false);
        $api->callAddEndpoint('GET', FakeController::class, 'staticNoParams', 'override', FakeAuth::class, 'authenticate');

        $endpoint = Route::fecthRouteList()['GET']['/module-1/override'];

        $this->assertSame(FakeAuth::class, $endpoint->getAuthClass());
        $this->assertSame('authenticate', $endpoint->getAuthMethod());
    }

    // comportamento esperado: endpoint sem authClass/authMethod usa o default do módulo
    public function testModuleDefaultAuthClassUsedWhenEndpointDoesNotSpecifyAuthClass()
    {
        $api = new FakeApi('module-2', FakeAuth::class, 'authenticate', false);
        $api->callAddEndpoint('GET', FakeController::class, 'staticNoParams', 'no-override');

        $endpoint = Route::fecthRouteList()['GET']['/module-2/no-override'];

        $this->assertSame(FakeAuth::class, $endpoint->getAuthClass());
        $this->assertSame('authenticate', $endpoint->getAuthMethod());
    }

    // comportamento esperado: ignoreAuth: false explícito no endpoint prevalece sobre ignoreAuth: true do módulo
    // (checagem em AbstractApi::addEndpoint() é !== null, não ??, exatamente para permitir esse caso)
    public function testEndpointIgnoreAuthFalseOverridesModuleIgnoreAuthTrue()
    {
        FakeAuth::$shouldAuthenticate = false;

        $api = new FakeApi('module-3', FakeAuth::class, 'authenticate', true);
        $api->callAddEndpoint('GET', FakeController::class, 'staticNoParams', 'ignore-override', null, null, false);

        $endpoint = Route::fecthRouteList()['GET']['/module-3/ignore-override'];

        $this->expectException(AuthenticationException::class);

        $endpoint->executeEndpoint();
    }

    // comportamento esperado: endpoint sem ignoreAuth (null) usa o default do módulo
    public function testModuleIgnoreAuthUsedWhenEndpointDoesNotSpecifyIgnoreAuth()
    {
        FakeAuth::$shouldAuthenticate = false;

        $api = new FakeApi('module-4', FakeAuth::class, 'authenticate', true);
        $api->callAddEndpoint('GET', FakeController::class, 'staticNoParams', 'ignore-default');

        $endpoint = Route::fecthRouteList()['GET']['/module-4/ignore-default'];
        $result = $endpoint->executeEndpoint();

        $this->assertSame('executed', $result);
    }

    private function resetRoutes(): void
    {
        $reflection = new \ReflectionClass(Route::class);
        $property = $reflection->getProperty('allRoutesRoutesFunctions');
        $property->setAccessible(true);
        $property->setValue(null, [
            'GET'    => [],
            'POST'   => [],
            'PUT'    => [],
            'DELETE' => [],
        ]);
    }
}
