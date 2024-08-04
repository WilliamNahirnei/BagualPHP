<?php
namespace Server\Router;

use Server\Interfaces\InterfacePHPRequest;

/**
 * Class Request
 *
 * This class handles HTTP request data, including query parameters, body parameters, headers, method, URI, and files.
 * It follows the singleton pattern to ensure only one instance is created.
 *
 * @package Server\Router
 * @author William Nahirnei Lopes
 */
class Request implements InterfacePHPRequest {
    /**
     * @var Request|null The singleton instance of the Request class.
     */
    private static ?Request $instance = null;

    /**
     * @var array|null The query parameters of the request.
     */
    private ?array $queryParams;

    /**
     * @var array|null The body parameters of the request.
     */
    private ?array $bodyParams;

    /**
     * @var array|null The headers of the request.
     */
    private ?array $headers;

    /**
     * @var string The HTTP method of the request.
     */
    private string $method;

    /**
     * @var string The URI of the request.
     */
    private string $uri;

    /**
     * @var array|null The files uploaded in the request.
     */
    private ?array $files;

    /**
     * Constructor.
     * Initializes the request data from the global PHP variables.
     */
    private function __construct() {
        $this->queryParams = $_GET;
        $this->bodyParams = json_decode(file_get_contents('php://input'), true);
        $this->headers = getallheaders();
        $this->method = $_SERVER[self::REQUEST_METHOD];
        $this->uri = $_SERVER[self::REQUEST_URI];
        $this->files = $_FILES;
    }

    /**
     * Returns the singleton instance of the Request class.
     *
     * @return Request The singleton instance.
     */
    public static function getInstance(): Request {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Gets the query parameters of the request.
     *
     * @return array The query parameters.
     */
    public function getQueryParams(): array {
        return $this->queryParams;
    }

    /**
     * Gets the body parameters of the request.
     *
     * @return array The body parameters.
     */
    public function getBodyParams(): array {
        return $this->bodyParams;
    }

    /**
     * Gets the headers of the request.
     *
     * @return array The headers.
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * Gets the HTTP method of the request.
     *
     * @return string The HTTP method.
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * Gets the URI of the request.
     *
     * @return string The URI.
     */
    public function getUri(): string {
        return $this->uri;
    }

    /**
     * Gets the files uploaded in the request.
     *
     * @return array The files.
     */
    public function getFiles(): array {
        return $this->files;
    }

    /**
     * Merges and returns all parameters (query and body).
     *
     * @return array The merged parameters.
     */
    public function getAllMergedParams(): array {
        return array_merge($this->queryParams, $this->bodyParams);
    }
}
?>