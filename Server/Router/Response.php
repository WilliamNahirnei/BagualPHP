<?php
    namespace Server\Router;

    use Server\Interfaces\InterfaceDefaultValuesResponse;
    use Server\Interfaces\InterfaceResponseContent;

    class Response implements InterfaceDefaultValuesResponse, InterfaceResponseContent{
        private static int $statusCode = self::DEFAULT_STATUS_CODE;

        private $responseContent = self::DEFAULT_CONTENT;
        private static string $responseMessage = self::DEFAULT_MESSAGE;


        public static function setStatusCode(int $statusCode): void {
            self::$statusCode = $statusCode;
        }
        public static function getStatusCode(): int {
            return self::$statusCode;
        }
        public function setResponseContent($responseContent): void {
            $this->responseContent = $responseContent;
        }
        public function getResponseContent() {
            return $this->responseContent;
        }

        public static function setResponseMessage(string $responseMessage): void {  
            self::$responseMessage = $responseMessage;
        }
        public static function getResponseMessage(): string {
            return self::$responseMessage;
        }

        public function mountCompleteResponse(): array {
            return [
                self::RESPONSE_MESSAGE => $this::getResponseMessage(),
                self::RESPONSE_DATA => $this->getResponseContent(),
            ];
        }

        public function generateServerResponse(): string {
            http_response_code(self::getStatusCode());
            return json_encode($this->mountCompleteResponse(),true);
        }

    }
?>