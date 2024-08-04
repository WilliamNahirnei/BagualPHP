<?php
namespace Server\Router;

use Server\Interfaces\InterfaceDefaultValuesResponse;
use Server\Interfaces\InterfaceHeaders;
use Server\Interfaces\InterfaceResponseContent;

/**
 * Class Response
 * 
 * This class handles the server response including status code, response content, response message, and headers.
 *
 * @package Server\Router
 * @author William Nahirnei Lopes
 */
class Response implements InterfaceDefaultValuesResponse, InterfaceResponseContent, InterfaceHeaders {
    /**
     * @var int The HTTP status code for the response.
     */
    private static int $statusCode = self::DEFAULT_STATUS_CODE;

    /**
     * @var mixed The content of the response.
     */
    private $responseContent = self::DEFAULT_CONTENT;

    /**
     * @var string The message associated with the response.
     */
    private static string $responseMessage = self::DEFAULT_MESSAGE;

    /**
     * @var array The headers to be sent with the response.
     */
    private static array $headers = [];

    /**
     * Sets the HTTP status code for the response.
     *
     * @param int $statusCode The HTTP status code.
     * @return void
     */
    public static function setStatusCode(int $statusCode): void {
        self::$statusCode = $statusCode;
    }

    /**
     * Gets the HTTP status code for the response.
     *
     * @return int The HTTP status code.
     */
    public static function getStatusCode(): int {
        return self::$statusCode;
    }

    /**
     * Sets the content of the response.
     *
     * @param mixed $responseContent The content of the response.
     * @return void
     */
    public function setResponseContent($responseContent): void {
        $this->responseContent = $responseContent;
    }

    /**
     * Gets the content of the response.
     *
     * @return mixed The content of the response.
     */
    public function getResponseContent() {
        return $this->responseContent;
    }

    /**
     * Sets the message associated with the response.
     *
     * @param string $responseMessage The response message.
     * @return void
     */
    public static function setResponseMessage(string $responseMessage): void {
        self::$responseMessage = $responseMessage;
    }

    /**
     * Gets the message associated with the response.
     *
     * @return string The response message.
     */
    public static function getResponseMessage(): string {
        return self::$responseMessage;
    }

    /**
     * Adds a header to the response. If the header already exists, it appends the value.
     *
     * @param string $header The name of the header.
     * @param string $value The value of the header.
     * @return void
     */
    public static function addHeader(string $header, string $value): void {
        if (isset(self::$headers[$header])) {
            self::$headers[$header][] = $value;
        } else {
            self::$headers[$header] = [$value];
        }
    }

    /**
     * Sends all headers set for the response.
     *
     * @return void
     */
    private static function defineHeaders(): void {
        foreach (self::$headers as $header => $values) {
            foreach ($values as $value) {
                header("$header: $value", false); // false ensures headers are not replaced
            }
        }
    }

    /**
     * Mounts the complete response including message and data.
     *
     * @return array The complete response as an array.
     */
    public function mountCompleteResponse(): array {
        return [
            self::RESPONSE_MESSAGE => $this::getResponseMessage(),
            self::RESPONSE_DATA => $this->getResponseContent(),
        ];
    }

    /**
     * Generates the server response in JSON format.
     *
     * @return string The JSON encoded server response.
     */
    public function generateServerResponse(): string {
        http_response_code(self::getStatusCode());
        self::defineHeaders();
        return json_encode($this->mountCompleteResponse(), true);
    }
}
?>