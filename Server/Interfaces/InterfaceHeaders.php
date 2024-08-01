<?php
namespace Server\Interfaces;

interface InterfaceHeaders {
    // Common HTTP Headers
    public const HEADER_CONTENT_TYPE = 'Content-Type';
    public const HEADER_CONTENT_LENGTH = 'Content-Length';
    public const HEADER_CONTENT_ENCODING = 'Content-Encoding';
    public const HEADER_CONTENT_LANGUAGE = 'Content-Language';
    public const HEADER_CONTENT_LOCATION = 'Content-Location';
    public const HEADER_CONTENT_MD5 = 'Content-MD5';
    public const HEADER_CONTENT_RANGE = 'Content-Range';
    public const HEADER_CONTENT_DISPOSITION = 'Content-Disposition';
    public const HEADER_CONTENT_SECURITY_POLICY = 'Content-Security-Policy';
    public const HEADER_CACHE_CONTROL = 'Cache-Control';
    public const HEADER_EXPIRES = 'Expires';
    public const HEADER_ETAG = 'ETag';
    public const HEADER_LAST_MODIFIED = 'Last-Modified';
    public const HEADER_LOCATION = 'Location';
    public const HEADER_SET_COOKIE = 'Set-Cookie';
    public const HEADER_STRICT_TRANSPORT_SECURITY = 'Strict-Transport-Security';
    public const HEADER_TRANSFER_ENCODING = 'Transfer-Encoding';
    public const HEADER_VARY = 'Vary';
    public const HEADER_WWW_AUTHENTICATE = 'WWW-Authenticate';
    public const HEADER_X_FRAME_OPTIONS = 'X-Frame-Options';
    public const HEADER_X_XSS_PROTECTION = 'X-XSS-Protection';
    public const HEADER_X_CONTENT_TYPE_OPTIONS = 'X-Content-Type-Options';
    public const HEADER_X_POWERED_BY = 'X-Powered-By';

    // Common Header Values
    public const CONTENT_TYPE_JSON = 'application/json';
    public const CONTENT_TYPE_HTML = 'text/html';
    public const CONTENT_TYPE_XML = 'application/xml';
    public const CONTENT_TYPE_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    public const CONTENT_TYPE_MULTIPART_FORM_DATA = 'multipart/form-data';
    public const CACHE_CONTROL_NO_CACHE = 'no-cache, no-store, must-revalidate';
    public const CACHE_CONTROL_PUBLIC = 'public, max-age=31536000';
    public const CACHE_CONTROL_PRIVATE = 'private, max-age=31536000';
    public const CACHE_CONTROL_NO_STORE = 'no-store';
    public const X_FRAME_OPTIONS_DENY = 'DENY';
    public const X_FRAME_OPTIONS_SAMEORIGIN = 'SAMEORIGIN';
    public const X_XSS_PROTECTION_ENABLED = '1; mode=block';
    public const X_CONTENT_TYPE_OPTIONS_NOSNIFF = 'nosniff';
}
?>