<?php
namespace Server\Interfaces;

/**
 * Interface InterfaceRequestMethods
 *
 * This interface defines constants for various HTTP request methods.
 * It is used to standardize and reference HTTP methods throughout the application.
 *
 * @package Server\Interfaces
 */
interface InterfaceRequestMethods
{
    /**
     * HTTP GET request method.
     */
    public const METHOD_GET = 'GET';

    /**
     * HTTP POST request method.
     */
    public const METHOD_POST = 'POST';

    /**
     * HTTP PUT request method.
     */
    public const METHOD_PUT = 'PUT';

    /**
     * HTTP DELETE request method.
     */
    public const METHOD_DELETE = 'DELETE';

    /**
     * HTTP OPTIONS request method.
     */
    public const METHOD_OPTIONS = 'OPTIONS';
}
?>