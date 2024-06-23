<?php
    namespace Server\Router;
    require_once('./Config/Server.php');
    require_once('./Server/Routes/api.php');
    require_once('./Server/Routes/RouteParams.php');
    require_once('./Server/Constants/ServerMessage.php');
    require_once('./Server/Interface/InterfacePHPRequest.php');
    
    use Server\Routes\Route;
    use Server\Routes\RouteParams;
    use Config\Server;
    use Server\Constants\ServerMessage;
    use Server\Iterface\InterfacePHPRequest;

    class Router implements InterfacePHPRequest{

        /**
         * string TotalRoute
         */
        private $totalRoute = '';
        public function __construct() {
            header("Content-type: application/json; charset=utf-8");
            $this->setTotalRoute($_SERVER[self::REQUEST_URI]);

        }

        public function executeRequest() {
            try{
                //take route prefix
                $prefix = explode('/',$this->getTotalRoute())[1];
                if(!empty(Server::PREFIX_API) && $prefix != Server::PREFIX_API){
                    http_response_code(404);
                    $response = [
                        ServerMessage::MESSAGE => Server::PREFIX_API . " " . ServerMessage::NOT_FOUND,
                    ];
            
                    $response = json_encode($response);
                    echo $response;
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
                       echo call_user_func(array($class, $method));
                   } else {
                       http_response_code(404);
                       $response = [
                        ServerMessage::MESSAGE => ServerMessage::ROUTE . " " . ServerMessage::NOT_FOUND,
                       ];
        
                       $response = json_encode($response);
                       echo $response;
                   }
                }
            } catch (\Throwable $e){
                http_response_code(500);
                echo json_encode([ServerMessage::MESSAGE => ServerMessage::INTERNAL_SERVER_ERRO . $e]);
            }
        }

        private function setTotalRoute(string $totalRoute) {
            $this->totalRoute = $totalRoute;
        }

        private function getTotalRoute() {
            return $this->totalRoute;
        }
    }

?>