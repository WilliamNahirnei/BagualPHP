<?php
    namespace Server\Iterface;
interface InterfacePHPRequest
{
    public const PHP_SELF = 'PHP_SELF';
    public const ARGV ='argv';
    public const ARGC = 'argc';
    public const GATEWAY_INTERFACE = 'GATEWAY_INTERFACE';
    public const SERVER_ADDR = 'SERVER_ADDR';
    public const SERVER_NAME = 'SERVER_NAME';
    public const SERVER_SOFTWARE = 'SERVER_SOFTWARE';
    public const SERVER_PROTOCOL = 'SERVER_PROTOCOL';
    public const REQUEST_METHOD = 'REQUEST_METHOD';
    public const REQUEST_TIME = 'REQUEST_TIME';
    public const REQUEST_TIME_FLOAT = 'REQUEST_TIME_FLOAT';
    public const QUERY_STRING = 'QUERY_STRING';
    public const DOCUMENT_ROOT = 'DOCUMENT_ROOT';
    public const HTTPS = 'HTTPS';
    public const REMOTE_ADDR = 'REMOTE_ADDR';
    public const REMOTE_HOST = 'REMOTE_HOST';
    public const REMOTE_PORT = 'REMOTE_PORT';
    public const REMOTE_USER = 'REMOTE_USER';
    public const REDIRECT_REMOTE_USER = 'REDIRECT_REMOTE_USER';
    public const SCRIPT_FILENAME = 'SCRIPT_FILENAME';
    public const SERVER_ADMIN = 'SERVER_ADMIN';
    public const SERVER_PORT = 'SERVER_PORT';
    public const SERVER_SIGNATURE = 'SERVER_SIGNATURE';
    public const PATH_TRANSLATED = 'PATH_TRANSLATED';
    public const SCRIPT_NAME = 'SCRIPT_NAME';
    public const REQUEST_URI = 'REQUEST_URI';
    public const PHP_AUTH_DIGEST = 'PHP_AUTH_DIGEST';
    public const PHP_AUTH_USER = 'PHP_AUTH_USER';
    public const PHP_AUTH_PW = 'PHP_AUTH_PW';
    public const AUTH_TYPE = 'AUTH_TYPE';
    public const PATH_INFO = 'PATH_INFO';
    public const ORIG_PATH_INFO = 'ORIG_PATH_INFO';

}
?>