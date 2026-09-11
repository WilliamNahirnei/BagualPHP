<?php
namespace Errors;

use Server\Constants\ServerMessage;
use Server\Constants\StatusCodes;
use Server\Errors\AuthenticationException;

class AuthenticationExceptionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // comportamento esperado
    public function testDefaultMessageIsUnauthorizedAuthenticationError()
    {
        $exception = new AuthenticationException();

        $this->assertSame(ServerMessage::DEFAULT_AUTH_ERROR, $exception->getMessage());
    }

    // comportamento esperado
    public function testDefaultRestCodeIsHttpUnauthorized()
    {
        $exception = new AuthenticationException();

        $this->assertSame(StatusCodes::HTTP_UNAUTHORIZED, $exception->getCode());
    }

    // comportamento esperado
    public function testCustomMessageAndCodeOverrideDefaults()
    {
        $exception = new AuthenticationException(['erro customizado'], StatusCodes::HTTP_FORBIDDEN);

        $this->assertSame('erro customizado', $exception->getMessage());
        $this->assertSame(StatusCodes::HTTP_FORBIDDEN, $exception->getCode());
    }
}
