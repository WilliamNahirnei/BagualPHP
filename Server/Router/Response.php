<?php
    namespace Server\Router;

    use Server\Interfaces\InterfaceDefaultValuesResponse;
    use Server\Interfaces\InterfaceResponseContent;

    class Response implements InterfaceDefaultValuesResponse, InterfaceResponseContent{
        private static int $statusCode = self::DEFAULT_STATUS_CODE;

        private $responseContent = self::DEFAULT_CONTENT;
        private static string $responseMessage = self::DEFAULT_MESSAGE;


        public static function setStatusCode(int $statusCode) {
            self::$statusCode = $statusCode;
        }
        public static function getStatusCode() {
            return self::$statusCode;
        }
        public function setResponseContent($responseContent) {
            $this->responseContent = $responseContent;
        }
        public function getResponseContent() {
            return $this->responseContent;
        }

        public static function setResponseMessage(string $responseMessage) {  
            self::$responseMessage = $responseMessage;
        }
        public static function getResponseMessage() {
            return self::$responseMessage;
        }

        public function mountCompleteResponse() {
            return [
                self::RESPONSE_MESSAGE => $this::getResponseMessage(),
                self::RESPONSE_DATA => $this->getResponseContent(),
            ];
        }

        public function generateServerResponse() {
            http_response_code(self::getStatusCode());
            return json_encode($this->mountCompleteResponse(),true);
        }

    }
?>