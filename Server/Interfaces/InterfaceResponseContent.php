<?php
namespace Server\Interfaces;

/**
 * Interface InterfaceResponseContent
 *
 * This interface defines constants for response content keys.
 * It is used to standardize the keys for response messages and data.
 *
 * @package Server\Interfaces
 * @author William Nahirnei Lopes
 */
interface InterfaceResponseContent
{
    /**
     * The key for the response message.
     */
    public const RESPONSE_MESSAGE = 'message';

    /**
     * The key for the response data.
     */
    public const RESPONSE_DATA = 'data';
}
?>