<?php
namespace Server\Interfaces;

/**
 * Interface InterfaceHeaders
 *
 * This interface defines a set of common HTTP headers and their corresponding values.
 * These constants can be used throughout the application to ensure consistency and avoid typos.
 *
 * @package Server\Interfaces
 * @author William Nahirnei Lopes
 */
interface InterfaceHeaders {
    // Common HTTP Headers

    /** @var string The MIME type of the resource */
    public const HEADER_CONTENT_TYPE = 'Content-Type';

    /** @var string The size of the resource, in decimal number of octets */
    public const HEADER_CONTENT_LENGTH = 'Content-Length';

    /** @var string The type of encoding used on the data */
    public const HEADER_CONTENT_ENCODING = 'Content-Encoding';

    /** @var string The natural language or languages of the intended audience for the enclosed content */
    public const HEADER_CONTENT_LANGUAGE = 'Content-Language';

    /** @var string A URI reference to the source of the content */
    public const HEADER_CONTENT_LOCATION = 'Content-Location';

    /** @var string A base64-encoded binary MD5 sum of the content of the response */
    public const HEADER_CONTENT_MD5 = 'Content-MD5';

    /** @var string Where in a full body message this partial message belongs */
    public const HEADER_CONTENT_RANGE = 'Content-Range';

    /** @var string An indication of the disposition of the content */
    public const HEADER_CONTENT_DISPOSITION = 'Content-Disposition';

    /** @var string The Content Security Policy (CSP) that browsers should enforce */
    public const HEADER_CONTENT_SECURITY_POLICY = 'Content-Security-Policy';

    /** @var string Directives for caching mechanisms in both requests and responses */
    public const HEADER_CACHE_CONTROL = 'Cache-Control';

    /** @var string The date/time after which the response is considered stale */
    public const HEADER_EXPIRES = 'Expires';

    /** @var string A unique identifier for a specific version of a resource */
    public const HEADER_ETAG = 'ETag';

    /** @var string The last modified date of the resource */
    public const HEADER_LAST_MODIFIED = 'Last-Modified';

    /** @var string Used in redirection or when a new resource has been created */
    public const HEADER_LOCATION = 'Location';

    /** @var string An HTTP cookie */
    public const HEADER_SET_COOKIE = 'Set-Cookie';

    /** @var string Enforces strict HSTS policy */
    public const HEADER_STRICT_TRANSPORT_SECURITY = 'Strict-Transport-Security';

    /** @var string Used to specify the form of encoding used to safely transfer the payload body to the user */
    public const HEADER_TRANSFER_ENCODING = 'Transfer-Encoding';

    /** @var string Indicates the request headers that can vary between requests */
    public const HEADER_VARY = 'Vary';

    /** @var string Defines the authentication method that should be used to access a resource */
    public const HEADER_WWW_AUTHENTICATE = 'WWW-Authenticate';

    /** @var string Clickjacking protection */
    public const HEADER_X_FRAME_OPTIONS = 'X-Frame-Options';

    /** @var string Cross-site scripting (XSS) filter */
    public const HEADER_X_XSS_PROTECTION = 'X-XSS-Protection';

    /** @var string MIME types to prevent the browser from interpreting files as something else */
    public const HEADER_X_CONTENT_TYPE_OPTIONS = 'X-Content-Type-Options';

    /** @var string The technology used by the server */
    public const HEADER_X_POWERED_BY = 'X-Powered-By';

    // Common Header Values

    /** @var string JSON MIME type */
    public const CONTENT_TYPE_JSON = 'application/json';

    /** @var string HTML MIME type */
    public const CONTENT_TYPE_HTML = 'text/html';

    /** @var string XML MIME type */
    public const CONTENT_TYPE_XML = 'application/xml';

    /** @var string Form URL encoded MIME type */
    public const CONTENT_TYPE_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    /** @var string Multipart form data MIME type */
    public const CONTENT_TYPE_MULTIPART_FORM_DATA = 'multipart/form-data';

    /** @var string Cache control value to disable caching */
    public const CACHE_CONTROL_NO_CACHE = 'no-cache, no-store, must-revalidate';

    /** @var string Cache control value for public caching */
    public const CACHE_CONTROL_PUBLIC = 'public, max-age=31536000';

    /** @var string Cache control value for private caching */
    public const CACHE_CONTROL_PRIVATE = 'private, max-age=31536000';

    /** @var string Cache control value to disable storing */
    public const CACHE_CONTROL_NO_STORE = 'no-store';

    /** @var string Denies the use of iframes */
    public const X_FRAME_OPTIONS_DENY = 'DENY';

    /** @var string Allows the use of iframes on the same origin */
    public const X_FRAME_OPTIONS_SAMEORIGIN = 'SAMEORIGIN';

    /** @var string Enables XSS protection */
    public const X_XSS_PROTECTION_ENABLED = '1; mode=block';

    /** @var string Prevents MIME type sniffing */
    public const X_CONTENT_TYPE_OPTIONS_NOSNIFF = 'nosniff';
}
?>