<?php
    namespace Server\Router;
    require_once('./Config/Server.php');
    require_once('./Server/Routes/api.php');
    require_once('./Server/Routes/RouteParams.php');
    require_once('./Server/Constants/ServerMessage.php');
    require_once('./Server/Interface/InterfacePHPRequest.php');
    require_once('./Server/Constants/StatusCodes.php');
    require_once('./Server/Router/Response.php');

    use Server\Routes\Route;
    use Server\Routes\RouteParams;
    use Config\Server;
    use Server\Constants\ServerMessage;
    use Server\Iterface\InterfacePHPRequest;
    use Server\Constants\StatusCodes;
    use Server\Router\Response;

    class Router implements InterfacePHPRequest{

        /**
         * string TotalRoute
         */
        private $totalRoute = '';

        private Response $response;
        public function __construct() {
            header("Content-type: application/json; charset=utf-8");
            $this->setTotalRoute($_SERVER[self::REQUEST_URI]);
            $this->setResponse(new Response());

        }

        public function executeRequest() {
            try{
                //take route prefix
                $prefix = explode('/',$this->getTotalRoute())[1];

                if(!empty(Server::PREFIX_API) && $prefix != Server::PREFIX_API){
                    $this->defineNotFoundResponse(Server::PREFIX_API);
                } else{
                    $route = $_SERVER[self::PATH_INFO];
                   //Remove route prefix
                   if(!empty(Server::PREFIX_API)) {
                        $route = str_replace('/'.Server::PREFIX_API, '' ,$route);
                   }
        
                   //take request body
                   $bodyJson = file_get_contents('php://input');
        
                   $method = $_SERVER[self::REQUEST_METHOD];
        
                   RouteParams::mountQuery($_REQUEST);
                   RouteParams::mountBody($bodyJson);
        
                   if (array_key_exists($route,Route::fecthRouteList()[$method])){
                       $class = Route::fecthRouteList()[$method][$route][0];
                       $method = Route::fecthRouteList()[$method][$route][1];
                       $this->getResponse()->setResponseContent(call_user_func(array($class, $method)));
                   } else {
                    $this->defineNotFoundResponse(ServerMessage::ROUTE);
                   }
                }

            } catch (\Throwable $e){
                $this->defineInternalErrorResponse($e);
            }

            $this->sendResponse();
        }

        public function defineNotFoundResponse(string $resourceNotFounded) {
            $this->defineResponse($this->generateNotFoundMessage($resourceNotFounded), StatusCodes::HTTP_NOT_FOUND);
        }

        public function defineInternalErrorResponse($error) {
            $this->defineResponse($this->generateInternalErrorMessage($error), StatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }

        public function defineResponse(string $responseMessage, int $statusCode) {
            $this->getResponse()->setStatusCode($statusCode);
            $this->getResponse()->setResponseMessage($responseMessage);
        }

        private function generateNotFoundMessage(string $resourceNotFounded) : string {
            return $resourceNotFounded . " " . ServerMessage::NOT_FOUND;
        }

        private function generateInternalErrorMessage($error) : string {
            return ServerMessage::INTERNAL_SERVER_ERRO . $error;
        }

        private function sendResponse(){
            echo $this->getResponse()->generateServerResponse();
        }

        private function setTotalRoute(string $totalRoute) {
            $this->totalRoute = $totalRoute;
        }

        private function getTotalRoute() {
            return $this->totalRoute;
        }

        private function setResponse(Response $response) {
            $this->response = $response;
        }

        private function getResponse() : Response {
            return $this->response;
        }
    }

?>