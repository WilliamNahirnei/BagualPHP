<?php
namespace Routing;

use Server\Routing\ApiManager;

class ApiManagerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private const FAKE_MODULE = 'FakeTestModule';
    private const EMPTY_MODULE = 'EmptyModule';
    private const WRONG_CLASS_NAME_MODULE = 'WrongClassNameModule';
    private const NO_METHOD_MODULE = 'NoMethodModule';

    protected function _after()
    {
        $this->removeModuleDirectory(self::FAKE_MODULE);
        $this->removeModuleDirectory(self::EMPTY_MODULE);
        $this->removeModuleDirectory(self::WRONG_CLASS_NAME_MODULE);
        $this->removeModuleDirectory(self::NO_METHOD_MODULE);

        if (class_exists('Src\\Modules\\FakeTestModule\\Api', false)) {
            \Src\Modules\FakeTestModule\Api::$wasCalled = false;
        }
        if (class_exists('Src\\Modules\\WrongClassNameModule\\NotApi', false)) {
            \Src\Modules\WrongClassNameModule\NotApi::$wasCalled = false;
        }
    }

    // comportamento esperado: loadApiEndpoints() resolve o namespace do módulo e chama defineEndpointList()
    public function testLoadApiEndpointsCallsDefineEndpointListOfEachModuleFound()
    {
        $this->createModuleApiFile(self::FAKE_MODULE, <<<'PHP'
<?php
namespace Src\Modules\FakeTestModule;

class Api {
    public static bool $wasCalled = false;

    public function defineEndpointList(): void {
        self::$wasCalled = true;
    }
}
PHP);

        (new ApiManager())->loadApiEndpoints();

        $this->assertTrue(\Src\Modules\FakeTestModule\Api::$wasCalled);
    }

    // comportamento esperado: diretório de módulo sem Api.php é ignorado, sem erro
    public function testLoadApiEndpointsSkipsModuleDirectoryWithoutApiFile()
    {
        $this->createModuleDirectory(self::EMPTY_MODULE);

        (new ApiManager())->loadApiEndpoints();

        $this->assertTrue(true);
    }

    // comportamento esperado: Api.php existe mas não declara a classe esperada (Src\Modules\<Modulo>\Api) — class_exists() falso, nada é chamado
    public function testLoadApiEndpointsSkipsModuleWhenApiFileDoesNotDeclareExpectedClass()
    {
        $this->createModuleApiFile(self::WRONG_CLASS_NAME_MODULE, <<<'PHP'
<?php
namespace Src\Modules\WrongClassNameModule;

class NotApi {
    public static bool $wasCalled = false;

    public function defineEndpointList(): void {
        self::$wasCalled = true;
    }
}
PHP);

        (new ApiManager())->loadApiEndpoints();

        $this->assertFalse(\Src\Modules\WrongClassNameModule\NotApi::$wasCalled);
    }

    // comportamento esperado: classe Api existe mas não tem defineEndpointList() — method_exists() falso, nada é chamado
    public function testLoadApiEndpointsSkipsModuleWhenApiClassHasNoDefineEndpointListMethod()
    {
        $this->createModuleApiFile(self::NO_METHOD_MODULE, <<<'PHP'
<?php
namespace Src\Modules\NoMethodModule;

class Api {
}
PHP);

        (new ApiManager())->loadApiEndpoints();

        $this->assertTrue(true);
    }

    private function createModuleDirectory(string $moduleName): void
    {
        mkdir($this->modulesPath() . '/' . $moduleName, 0777, true);
    }

    private function createModuleApiFile(string $moduleName, string $contents): void
    {
        $this->createModuleDirectory($moduleName);
        file_put_contents($this->modulesPath() . '/' . $moduleName . '/Api.php', $contents);
    }

    private function removeModuleDirectory(string $moduleName): void
    {
        $dir = $this->modulesPath() . '/' . $moduleName;
        $apiFile = $dir . '/Api.php';

        if (file_exists($apiFile)) {
            unlink($apiFile);
        }
        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    private function modulesPath(): string
    {
        return dirname(__DIR__, 3) . '/Src/Modules';
    }
}
