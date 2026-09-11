<?php
namespace Config;

use Config\ConfigLoader;
use Fixtures\Config\FakeConfigLoader;
use Fixtures\Config\FakeConfigLoaderMissingFile;
use Fixtures\Config\FakeConfigLoaderSecondary;
use ReflectionProperty;

class ConfigLoaderTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _after()
    {
        $this->resetInstance(FakeConfigLoader::class);
        $this->resetInstance(FakeConfigLoaderSecondary::class);
        $this->resetInstance(FakeConfigLoaderMissingFile::class);
    }

    // comportamento esperado
    public function testGetConfigReturnsValueLoadedFromEnvFile()
    {
        $config = FakeConfigLoader::getInstance();

        $this->assertSame('valor-permitido', $config->getConfig('ALLOWED_KEY'));
    }

    // comportamento esperado: CONFIG_KEYS funciona como allowlist
    public function testGetConfigReturnsNullForKeyNotInAllowlist()
    {
        $config = FakeConfigLoader::getInstance();

        $this->assertNull($config->getConfig('NOT_IN_ALLOWLIST'));
    }

    // comportamento esperado: linhas comentadas são ignoradas mesmo quando a chave está no allowlist
    public function testCommentedLineIsNotLoadedEvenIfKeyIsInAllowlist()
    {
        $config = FakeConfigLoader::getInstance();

        $this->assertNull($config->getConfig('COMMENTED_KEY'));
    }

    // comportamento esperado
    public function testGetConfigReturnsNullForKeyAbsentFromFile()
    {
        $config = FakeConfigLoader::getInstance();

        $this->assertNull($config->getConfig('CHAVE_INEXISTENTE'));
    }

    // comportamento esperado: singleton
    public function testGetInstanceReturnsSameInstanceOnRepeatedCalls()
    {
        $this->assertSame(FakeConfigLoader::getInstance(), FakeConfigLoader::getInstance());
    }

    // comportamento esperado: o cache do singleton é por classe concreta, não compartilhado entre subclasses
    public function testGetInstanceReturnsDifferentInstancesForDifferentSubclasses()
    {
        $this->assertNotSame(FakeConfigLoader::getInstance(), FakeConfigLoaderSecondary::getInstance());
    }

    // comportamento esperado
    public function testMissingConfigFileThrowsException()
    {
        $this->expectException(\Exception::class);

        FakeConfigLoaderMissingFile::getInstance();
    }

    private function resetInstance(string $class): void
    {
        $property = new ReflectionProperty(ConfigLoader::class, 'instances');
        $property->setAccessible(true);
        $instances = $property->getValue();
        unset($instances[$class]);
        $property->setValue(null, $instances);
    }
}
