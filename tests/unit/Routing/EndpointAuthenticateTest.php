<?php
namespace Routing;

use Fixtures\Auth\FakeAuth;
use Fixtures\Auth\FakeAuthNotExtendingAbstract;
use Fixtures\Controllers\FakeController;
use Server\Auth\AbstractAuthenticable;
use Server\Auth\AuthConfig;
use Server\Errors\AuthenticationException;
use Server\Routing\Endpoint;

class EndpointAuthenticateTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private array $originalAuthConfig;

    protected function _before()
    {
        FakeAuth::$shouldAuthenticate = true;
        FakeAuth::$calledMethod = null;
        $this->originalAuthConfig = $this->getAuthConfigValue();
    }

    protected function _after()
    {
        $this->setAuthConfigValue($this->originalAuthConfig);
    }

    // comportamento esperado
    public function testIgnoreAuthTrueSkipsAuthentication()
    {
        $result = $this->makeEndpoint(null, null, true)->executeEndpoint();

        $this->assertSame('executed', $result);
    }

    // comportamento esperado (FakeAuth::authenticate() retornando true)
    public function testValidAuthClassGrantsAccess()
    {
        FakeAuth::$shouldAuthenticate = true;

        $result = $this->makeEndpoint(FakeAuth::class, 'authenticate', false)->executeEndpoint();

        $this->assertSame('executed', $result);
    }

    // comportamento esperado
    public function testInvalidCredentialsThrowAuthenticationException()
    {
        FakeAuth::$shouldAuthenticate = false;

        $this->expectException(AuthenticationException::class);

        $this->makeEndpoint(FakeAuth::class, 'authenticate', false)->executeEndpoint();
    }

    // comportamento esperado (validação de classExtends)
    public function testAuthClassNotExtendingAbstractAuthenticableThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Class '" . FakeAuthNotExtendingAbstract::class . "' must extend '" . AbstractAuthenticable::class . "'");

        $this->makeEndpoint(FakeAuthNotExtendingAbstract::class, 'authenticate', false)->executeEndpoint();
    }

    // comportamento esperado (validação de methodIsStatic em authClassIAutenticatle())
    public function testAuthMethodNonStaticThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Method 'nonStaticMethod' in class '" . FakeAuth::class . "' must be static");

        $this->makeEndpoint(FakeAuth::class, 'nonStaticMethod', false)->executeEndpoint();
    }

    // comportamento esperado (validação de methodHasNoParameters em authClassIAutenticatle())
    public function testAuthMethodWithParametersThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Method 'methodWithParam' in class '" . FakeAuth::class . "' must not have parameters");

        $this->makeEndpoint(FakeAuth::class, 'methodWithParam', false)->executeEndpoint();
    }

    // comportamento esperado (validação de classExists em validateExistenceAuthenticationDefined())
    public function testMissingAuthClassThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Class 'Fixtures\\Auth\\DoesNotExist' not found");

        $this->makeEndpoint('Fixtures\\Auth\\DoesNotExist', 'authenticate', false)->executeEndpoint();
    }

    // comportamento esperado (validação de methodExists em validateExistenceAuthenticationDefined())
    public function testMissingAuthMethodThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Method 'doesNotExist' not found in class '" . FakeAuth::class . "'");

        $this->makeEndpoint(FakeAuth::class, 'doesNotExist', false)->executeEndpoint();
    }

    // [_bug] item 3 da DIVIDA_TECNICA.md (Aberto): especifica o comportamento CORRETO (fail-closed).
    // Falha hoje contra Endpoint::authenticate(), que retorna true (fail-open) neste caso.
    public function testEndpointWithoutAnyAuthClassConfiguredThrowsConfigurationException()
    {
        $this->setAuthConfigValue([AuthConfig::DEFAULT_CLASS_NAMESPACE => '']);

        $this->expectException(\Exception::class);

        $this->makeEndpoint(null, null, false)->executeEndpoint();
    }

    // [_bug] item 3 da DIVIDA_TECNICA.md (Aberto): mesmo espírito, authClass presente mas authMethod ausente.
    // Falha hoje: authenticate() libera acesso porque a condição é um OU (empty($authClass) || empty($authMethod)).
    public function testAuthClassDefinedWithoutAuthMethodThrowsConfigurationException()
    {
        $this->expectException(\Exception::class);

        $this->makeEndpoint(FakeAuth::class, null, false)->executeEndpoint();
    }

    // [_bug] item 3 da DIVIDA_TECNICA.md (Aberto): caso simétrico, authMethod presente mas authClass ausente.
    // Falha hoje pelo mesmo motivo do teste anterior.
    public function testAuthMethodDefinedWithoutAuthClassThrowsConfigurationException()
    {
        $this->setAuthConfigValue([AuthConfig::DEFAULT_CLASS_NAMESPACE => '']);

        $this->expectException(\Exception::class);

        $this->makeEndpoint(null, 'authenticate', false)->executeEndpoint();
    }

    // [_bug] item 3 da DIVIDA_TECNICA.md (Aberto): loadDefaultAuthApp() não deveria descartar um authMethod
    // customizado já definido quando só authClass está vazio. Falha hoje porque loadDefaultAuthApp()
    // sobrescreve authMethod incondicionalmente para o default ("authenticate").
    public function testLoadDefaultAuthAppPreservesCustomAuthMethodWhenAuthClassIsEmpty()
    {
        $this->setAuthConfigValue([AuthConfig::DEFAULT_CLASS_NAMESPACE => FakeAuth::class]);
        FakeAuth::$shouldAuthenticate = true;

        $this->makeEndpoint(null, 'alternateAuthenticate', false)->executeEndpoint();

        $this->assertSame('alternateAuthenticate', FakeAuth::$calledMethod);
    }

    // comportamento esperado: fallback global usado com sucesso quando o endpoint não define authClass
    public function testGlobalDefaultAuthClassIsUsedWhenEndpointHasNoAuthClass()
    {
        $this->setAuthConfigValue([AuthConfig::DEFAULT_CLASS_NAMESPACE => FakeAuth::class]);
        FakeAuth::$shouldAuthenticate = true;

        $result = $this->makeEndpoint(null, null, false)->executeEndpoint();

        $this->assertSame('executed', $result);
    }

    // comportamento esperado: fallback global usado, mas a autenticação falha
    public function testGlobalDefaultAuthClassIsUsedWhenEndpointHasNoAuthClassAndCredentialsAreInvalid()
    {
        $this->setAuthConfigValue([AuthConfig::DEFAULT_CLASS_NAMESPACE => FakeAuth::class]);
        FakeAuth::$shouldAuthenticate = false;

        $this->expectException(AuthenticationException::class);

        $this->makeEndpoint(null, null, false)->executeEndpoint();
    }

    private function makeEndpoint(?string $authClass, ?string $authMethod, bool $ignoreAuth): Endpoint
    {
        return new Endpoint('GET', '/fake', FakeController::class, 'staticNoParams', $authClass, $authMethod, $ignoreAuth);
    }

    private function getAuthConfigValue(): array
    {
        return $this->authConfigProperty()->getValue(AuthConfig::getInstance());
    }

    private function setAuthConfigValue(array $value): void
    {
        $this->authConfigProperty()->setValue(AuthConfig::getInstance(), $value);
    }

    private function authConfigProperty(): \ReflectionProperty
    {
        $reflection = new \ReflectionClass(AuthConfig::class);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        return $property;
    }
}
