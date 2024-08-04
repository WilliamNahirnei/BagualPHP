<?php
namespace Server\Interfaces;

/**
 * Interface InterfacePHPRequest
 *
 * This interface defines constants for various PHP and server-related superglobal keys.
 * It provides a standardized way to access common server and request variables.
 *
 * @package Server\Interfaces
 * @author William Nahirnei Lopes
 */
interface InterfacePHPRequest
{
    /**
     * The name of the PHP file currently being executed.
     */
    public const PHP_SELF = 'PHP_SELF';

    /**
     * Command-line arguments passed to the script.
     */
    public const ARGV = 'argv';

    /**
     * The number of command-line arguments passed to the script.
     */
    public const ARGC = 'argc';

    /**
     * The gateway interface used on the server.
     */
    public const GATEWAY_INTERFACE = 'GATEWAY_INTERFACE';

    /**
     * The IP address of the server.
     */
    public const SERVER_ADDR = 'SERVER_ADDR';

    /**
     * The name of the server.
     */
    public const SERVER_NAME = 'SERVER_NAME';

    /**
     * The server software being used.
     */
    public const SERVER_SOFTWARE = 'SERVER_SOFTWARE';

    /**
     * The server protocol being used.
     */
    public const SERVER_PROTOCOL = 'SERVER_PROTOCOL';

    /**
     * The request method used for the request.
     */
    public const REQUEST_METHOD = 'REQUEST_METHOD';

    /**
     * The time of the request as a Unix timestamp.
     */
    public const REQUEST_TIME = 'REQUEST_TIME';

    /**
     * The time of the request as a float value.
     */
    public const REQUEST_TIME_FLOAT = 'REQUEST_TIME_FLOAT';

    /**
     * The query string from the request URL.
     */
    public const QUERY_STRING = 'QUERY_STRING';

    /**
     * The document root directory of the server.
     */
    public const DOCUMENT_ROOT = 'DOCUMENT_ROOT';

    /**
     * Whether the request is made over HTTPS.
     */
    public const HTTPS = 'HTTPS';

    /**
     * The IP address of the client making the request.
     */
    public const REMOTE_ADDR = 'REMOTE_ADDR';

    /**
     * The host name of the client making the request.
     */
    public const REMOTE_HOST = 'REMOTE_HOST';

    /**
     * The port number used by the client making the request.
     */
    public const REMOTE_PORT = 'REMOTE_PORT';

    /**
     * The user name provided by the client, if any.
     */
    public const REMOTE_USER = 'REMOTE_USER';

    /**
     * The redirected user name, if applicable.
     */
    public const REDIRECT_REMOTE_USER = 'REDIRECT_REMOTE_USER';

    /**
     * The absolute path of the script being executed.
     */
    public const SCRIPT_FILENAME = 'SCRIPT_FILENAME';

    /**
     * The email address of the server administrator.
     */
    public const SERVER_ADMIN = 'SERVER_ADMIN';

    /**
     * The port number on the server.
     */
    public const SERVER_PORT = 'SERVER_PORT';

    /**
     * The server signature.
     */
    public const SERVER_SIGNATURE = 'SERVER_SIGNATURE';

    /**
     * The translated path of the script.
     */
    public const PATH_TRANSLATED = 'PATH_TRANSLATED';

    /**
     * The name of the script being executed.
     */
    public const SCRIPT_NAME = 'SCRIPT_NAME';

    /**
     * The URI used for the request.
     */
    public const REQUEST_URI = 'REQUEST_URI';

    /**
     * The HTTP digest authentication string.
     */
    public const PHP_AUTH_DIGEST = 'PHP_AUTH_DIGEST';

    /**
     * The user name for HTTP authentication.
     */
    public const PHP_AUTH_USER = 'PHP_AUTH_USER';

    /**
     * The password for HTTP authentication.
     */
    public const PHP_AUTH_PW = 'PHP_AUTH_PW';

    /**
     * The type of HTTP authentication.
     */
    public const AUTH_TYPE = 'AUTH_TYPE';

    /**
     * The path info for the request.
     */
    public const PATH_INFO = 'PATH_INFO';

    /**
     * The original path info for the request.
     */
    public const ORIG_PATH_INFO = 'ORIG_PATH_INFO';
}
?>