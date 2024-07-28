<?php
    namespace Server\Router;

use Server\Interfaces\InterfacePHPRequest;

    class Request implements InterfacePHPRequest{
        private static ?Request $instance = null;
        private ?array $queryParams;
        private ?array $bodyParams;
        private ?array $headers;
        private string $method;
        private string $uri;
        private ?array $files;

        private function __construct() {
            $this->queryParams = $_GET;
            $this->bodyParams = json_decode(file_get_contents('php://input'), true);
            $this->headers = getallheaders();
            $this->method = $_SERVER[self::REQUEST_METHOD];
            $this->uri = $_SERVER[self::REQUEST_URI];
            $this->files = $_FILES;
        }

        public static function getInstance(): Request {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function getQueryParams(): array {
            return $this->queryParams;
        }

        public function getBodyParams(): array {
            return $this->bodyParams;
        }

        public function getHeaders(): array {
            return $this->headers;
        }

        public function getMethod(): string {
            return $this->method;
        }

        public function getUri(): string {
            return $this->uri;
        }

        public function getFiles(): array {
            return $this->files;
        }

        public function getAllMergedParams(): array {
            return array_merge($this->queryParams, $this->bodyParams);
        }
    }