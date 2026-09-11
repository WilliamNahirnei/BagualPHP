<?php
namespace Errors;

use Server\Constants\ApiExceptionTypes;
use Server\Constants\StatusCodes;
use Server\Errors\ApiException;

class ApiExceptionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // comportamento esperado
    public function testGetAcceptReturnsTrueWhenConstructedWithTrue()
    {
        $exception = new ApiException(true, ApiExceptionTypes::ERROR, [], StatusCodes::HTTP_BAD_REQUEST);

        $this->assertTrue($exception->getAccept());
    }

    // comportamento esperado
    public function testGetAcceptReturnsFalseWhenConstructedWithFalse()
    {
        $exception = new ApiException(false, ApiExceptionTypes::ERROR, [], StatusCodes::HTTP_BAD_REQUEST);

        $this->assertFalse($exception->getAccept());
    }

    // comportamento esperado
    public function testGetAcceptDefaultsToTrueWhenNotProvided()
    {
        $exception = new ApiException();

        $this->assertTrue($exception->getAccept());
    }

    // comportamento esperado
    public function testGetTypeReturnsConstructorValue()
    {
        $exception = new ApiException(true, 'warning', [], StatusCodes::HTTP_BAD_REQUEST);

        $this->assertSame('warning', $exception->getType());
    }

    // comportamento esperado
    public function testGetTypeDefaultsToErrorTypeWhenNotProvided()
    {
        $exception = new ApiException();

        $this->assertSame(ApiExceptionTypes::ERROR, $exception->getType());
    }

    // comportamento esperado
    public function testGetErrorListMessageReturnsImplodedMessages()
    {
        $exception = new ApiException(true, ApiExceptionTypes::ERROR, ['a', 'b'], StatusCodes::HTTP_BAD_REQUEST);

        $this->assertSame('a|b', $exception->getErrorListMessage());
    }

    // comportamento esperado
    public function testGetRestCodeReturnsConstructorValue()
    {
        $exception = new ApiException(true, ApiExceptionTypes::ERROR, [], StatusCodes::HTTP_BAD_REQUEST);

        $this->assertSame(StatusCodes::HTTP_BAD_REQUEST, $exception->getRestCode());
    }

    // comportamento esperado
    public function testGetRestCodeDefaultsToInternalServerErrorWhenNotProvided()
    {
        $exception = new ApiException();

        $this->assertSame(StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $exception->getRestCode());
    }
}
