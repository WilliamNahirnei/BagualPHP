<?php

namespace Server\Constants;

/**
 * Class StatusCodes
 *
 * Defines a set of constants representing HTTP status codes. These codes are used to indicate the result
 * of an HTTP request and are categorized into informational, successful, redirection, client error, and
 * server error responses.
 *
 * @package Server\Constants
 */
class StatusCodes {

    // Informational responses
    /**
     * Indicates that the initial part of a request has been received and the client should continue with the request.
     */
    const HTTP_CONTINUE = 100;

    /**
     * Indicates that the server is switching protocols as requested by the client.
     */
    const HTTP_SWITCHING_PROTOCOLS = 101;

    /**
     * Indicates that the server has received and is processing the request, but no response is available yet.
     */
    const HTTP_PROCESSING = 102;

    /**
     * Indicates that the server is delivering an informational response before the final response is ready.
     */
    const HTTP_EARLY_HINTS = 103;

    // Successful responses
    /**
     * Indicates that the request has succeeded.
     */
    const HTTP_OK = 200;

    /**
     * Indicates that the request has been fulfilled and resulted in a new resource being created.
     */
    const HTTP_CREATED = 201;

    /**
     * Indicates that the request has been accepted for processing, but the processing has not been completed.
     */
    const HTTP_ACCEPTED = 202;

    /**
     * Indicates that the request was successful but returned information that is not the expected resource.
     */
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;

    /**
     * Indicates that the server has successfully fulfilled the request, but there is no content to send back.
     */
    const HTTP_NO_CONTENT = 204;

    /**
     * Indicates that the server has successfully fulfilled the request, and the client should reset the document view.
     */
    const HTTP_RESET_CONTENT = 205;

    /**
     * Indicates that the server has fulfilled a partial GET request for the resource.
     */
    const HTTP_PARTIAL_CONTENT = 206;

    /**
     * Indicates that the server has fulfilled the request and there are multiple representations of the resource.
     */
    const HTTP_MULTI_STATUS = 207;

    /**
     * Indicates that the server has already reported the status of the request.
     */
    const HTTP_ALREADY_REPORTED = 208;

    /**
     * Indicates that the server has fulfilled the request and the resource is not currently available.
     */
    const HTTP_IM_USED = 226;

    // Redirection messages
    /**
     * Indicates that the requested resource has multiple choices and the user or user agent should choose one of them.
     */
    const HTTP_MULTIPLE_CHOICES = 300;

    /**
     * Indicates that the requested resource has been permanently moved to a new URL.
     */
    const HTTP_MOVED_PERMANENTLY = 301;

    /**
     * Indicates that the requested resource has temporarily moved to a different URL.
     */
    const HTTP_FOUND = 302;

    /**
     * Indicates that the client should follow a different URL to access the requested resource.
     */
    const HTTP_SEE_OTHER = 303;

    /**
     * Indicates that the requested resource has not been modified since the last request.
     */
    const HTTP_NOT_MODIFIED = 304;

    /**
     * Indicates that the client should use a proxy to access the requested resource.
     */
    const HTTP_USE_PROXY = 305; // Deprecated

    /**
     * Indicates that the requested resource has temporarily moved to a different URL and the client should repeat the request.
     */
    const HTTP_TEMPORARY_REDIRECT = 307;

    /**
     * Indicates that the requested resource has permanently moved to a different URL and the client should use the new URL.
     */
    const HTTP_PERMANENT_REDIRECT = 308;

    // Client error responses
    /**
     * Indicates that the server cannot process the request due to client error.
     */
    const HTTP_BAD_REQUEST = 400;

    /**
     * Indicates that the request requires user authentication.
     */
    const HTTP_UNAUTHORIZED = 401;

    /**
     * Indicates that the server requires payment for the request.
     */
    const HTTP_PAYMENT_REQUIRED = 402;

    /**
     * Indicates that the server understands the request but refuses to authorize it.
     */
    const HTTP_FORBIDDEN = 403;

    /**
     * Indicates that the server cannot find the requested resource.
     */
    const HTTP_NOT_FOUND = 404;

    /**
     * Indicates that the method specified in the request is not allowed for the resource.
     */
    const HTTP_METHOD_NOT_ALLOWED = 405;

    /**
     * Indicates that the server cannot produce a response acceptable to the client.
     */
    const HTTP_NOT_ACCEPTABLE = 406;

    /**
     * Indicates that the client must authenticate to access the requested resource.
     */
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;

    /**
     * Indicates that the client did not produce a request within the time limit.
     */
    const HTTP_REQUEST_TIMEOUT = 408;

    /**
     * Indicates that the request could not be completed due to a conflict with the current state of the resource.
     */
    const HTTP_CONFLICT = 409;

    /**
     * Indicates that the requested resource is no longer available.
     */
    const HTTP_GONE = 410;

    /**
     * Indicates that the server requires the Content-Length header field to be specified.
     */
    const HTTP_LENGTH_REQUIRED = 411;

    /**
     * Indicates that the server cannot meet one of the preconditions specified in the request headers.
     */
    const HTTP_PRECONDITION_FAILED = 412;

    /**
     * Indicates that the request is too large for the server to process.
     */
    const HTTP_PAYLOAD_TOO_LARGE = 413;

    /**
     * Indicates that the URI of the request is too long for the server to process.
     */
    const HTTP_URI_TOO_LONG = 414;

    /**
     * Indicates that the server does not support the media type of the request.
     */
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * Indicates that the server cannot satisfy the range request.
     */
    const HTTP_RANGE_NOT_SATISFIABLE = 416;

    /**
     * Indicates that the server cannot meet the expectation specified in the Expect request header.
     */
    const HTTP_EXPECTATION_FAILED = 417;

    /**
     * Indicates that the server refuses to brew coffee because it is a teapot.
     */
    const HTTP_IM_A_TEAPOT = 418;

    /**
     * Indicates that the server cannot process the request due to a misdirected request.
     */
    const HTTP_MISDIRECTED_REQUEST = 421;

    /**
     * Indicates that the server understands the content type of the request entity, but was unable to process the contained instructions.
     */
    const HTTP_UNPROCESSABLE_CONTENT = 422;

    /**
     * Indicates that the resource being accessed is locked.
     */
    const HTTP_LOCKED = 423;

    /**
     * Indicates that the request failed due to a failed dependency.
     */
    const HTTP_FAILED_DEPENDENCY = 424;

    /**
     * Indicates that the server is unwilling to process the request because it is too early.
     */
    const HTTP_TOO_EARLY = 425;

    /**
     * Indicates that the server requires the client to upgrade to a different protocol.
     */
    const HTTP_UPGRADE_REQUIRED = 426;

    /**
     * Indicates that the server requires the client to meet certain preconditions.
     */
    const HTTP_PRECONDITION_REQUIRED = 428;

    /**
     * Indicates that the user has sent too many requests in a given amount of time.
     */
    const HTTP_TOO_MANY_REQUESTS = 429;

    /**
     * Indicates that the server is unwilling to process the request because its headers are too large.
     */
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    /**
     * Indicates that the resource is unavailable due to legal reasons.
     */
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    // Server error responses
    /**
     * Indicates that the server encountered an internal error and was unable to complete the request.
     */
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * Indicates that the server does not support the functionality required to fulfill the request.
     */
    const HTTP_NOT_IMPLEMENTED = 501;

    /**
     * Indicates that the server, while acting as a gateway or proxy, received an invalid response from the upstream server.
     */
    const HTTP_BAD_GATEWAY = 502;

    /**
     * Indicates that the server is currently unable to handle the request due to a temporary overload or maintenance of the server.
     */
    const HTTP_SERVICE_UNAVAILABLE = 503;

    /**
     * Indicates that the server did not receive a timely response from the upstream server or some other auxiliary server it needed to access.
     */
    const HTTP_GATEWAY_TIMEOUT = 504;

    /**
     * Indicates that the server does not support the HTTP protocol version that was used in the request.
     */
    const HTTP_HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * Indicates that the server is unable to store the representation needed to complete the request.
     */
    const HTTP_VARIANT_ALSO_NEGOTIATES = 506;

    /**
     * Indicates that the server is unable to store the representation needed to complete the request.
     */
    const HTTP_INSUFFICIENT_STORAGE = 507;

    /**
     * Indicates that the server detected an infinite loop while processing the request.
     */
    const HTTP_LOOP_DETECTED = 508;

    /**
     * Indicates that the server does not support the extension requested by the client.
     */
    const HTTP_NOT_EXTENDED = 510;

    /**
     * Indicates that the server requires network authentication to fulfill the request.
     */
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;
}
?>