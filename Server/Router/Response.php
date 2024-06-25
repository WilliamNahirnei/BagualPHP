<?php
    namespace Server\Router;

    require_once('./Server/Interface/InterfaceDefaultValuesResponse.php');
    require_once('./Server/Interface/InterfaceResponseContent.php');


    use Server\Iterface\InterfaceDefaultValuesResponse;
    use Server\Iterface\InterfaceResponseContent;

    class Response implements InterfaceDefaultValuesResponse, InterfaceResponseContent{
        private static int $statusCode = self::DEFAULT_STATUS_CODE;

        private $responseContent = self::DEFAULT_CONTENT;
        private string $responseMessage = self::DEFAULT_MESSAGE;


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

        public function setResponseMessage(string $responseMessage) {  
            $this->responseMessage = $responseMessage;
        }
        public function getResponseMessage() {
            return $this->responseMessage;
        }

        public function mountCompleteResponse() {
            return [
                self::RESPONSE_MESSAGE => $this->getResponseMessage(),
                self::RESPONSE_DATA => $this->getResponseContent(),
            ];
        }

        public function generateServerResponse() {
            http_response_code(self::getStatusCode());
            return json_encode($this->mountCompleteResponse());
        }

    }
?>