<?php
namespace Server\Interfaces;

use Server\Constants\StatusCodes;

/**
 * Interface InterfaceDefaultValuesResponse
 *
 * This interface defines default values for HTTP responses, including default status codes,
 * response content, and messages. It provides a standard way to ensure consistency in response defaults.
 *
 * @package Server\Interfaces
 */
interface InterfaceDefaultValuesResponse
{
    /**
     * The default status code for responses.
     * 
     * @var int
     */
    public const DEFAULT_STATUS_CODE = StatusCodes::HTTP_OK;

    /**
     * The default content for responses.
     * 
     * @var array
     */
    public const DEFAULT_CONTENT = [];

    /**
     * The default message for responses.
     * 
     * @var string
     */
    public const DEFAULT_MESSAGE = "";
}
?>