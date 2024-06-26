<?php
namespace Server\Router;

require_once('./Server/Routes/api.php');

use Server\Routes\Route;
use Server\Routes\RouteParams;
use Config\Server;
use Server\Constants\ServerMessage;
use Server\Interfaces\InterfacePHPRequest;
use Server\Constants\StatusCodes;
use Server\Router\Response;

class Router implements InterfacePHPRequest
{
    private string $totalRoute = '';

    private string $route = '';

    private string $requestMethod;

    private Response $response;

    public function __construct() {
        header("Content-type: application/json; charset=utf-8");
        $this->setTotalRoute($_SERVER[self::REQUEST_URI]);
        $this->setResponse(new Response());
    }

    public function executeRequest() {
        try {
            if ($this->invalidPrefixRoute()) {
                $this->defineNotFoundResponse(Server::PREFIX_API);
            } else {
                $this->defineRequestData();
                $this->processRequest();
            }
        } catch (\Throwable $e) {
            $this->defineInternalErrorResponse($e);
        }

        $this->sendResponse();
    }

    private function invalidPrefixRoute() {
        $prefix = $this->getPrefixInRoute();
        return (!empty(Server::PREFIX_API) && $prefix != Server::PREFIX_API);
    }

    private function defineRequestData() {
        $this->defineTreatedRoute();
        $this->mouteParams();
        $this->defineRequestMethod();
    }

    private function defineTreatedRoute() {
        $route = $_SERVER[self::PATH_INFO];
        //Remove route prefix
        if (!empty(Server::PREFIX_API)) {
            $route = str_replace('/'.Server::PREFIX_API, '' ,$route);
        }
        $this->setRoute($route);
    }

    private function mouteParams() {
        //take request body
        $bodyJson = file_get_contents('php://input');

        RouteParams::mountQuery($_REQUEST);
        RouteParams::mountBody($bodyJson);
    }

    private function defineRequestMethod() {
        $this->setRequestMethod($_SERVER[self::REQUEST_METHOD]);
    }

    private function processRequest() {
        if ($this->apiRouteIsDefined()) {
            $this->getResponse()->setResponseContent($this->callControllerMethodRoute());
        } else {
            $this->defineNotFoundResponse(ServerMessage::ROUTE);
        }
    }

    private function callControllerMethodRoute() {
        return call_user_func(
            [
                $this->getControllerRoute(),
                $this->getMethodControllerRoute()
            ]
        );
    }

    private function getControllerRoute() {
        return Route::fecthRouteList()[$this->getRequestMethod()][$this->getRoute()][0];
    }

    private function getMethodControllerRoute() {
        return Route::fecthRouteList()[$this->getRequestMethod()][$this->getRoute()][1];
    }

    private function getPrefixInRoute() {
        return explode('/',$this->getTotalRoute())[1];
    }

    private function apiRouteIsDefined() {
        return array_key_exists($this->getRoute(), Route::fecthRouteList()[$this->getRequestMethod()]);
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

    private function sendResponse() {
        echo $this->getResponse()->generateServerResponse();
    }

    private function setTotalRoute(string $totalRoute) {
        $this->totalRoute = $totalRoute;
    }

    private function getTotalRoute() {
        return $this->totalRoute;
    }

    private function setRoute(string $route) {
        $this->route = $route;
    }

    private function getRoute() {
        return $this->route;
    }

    private function setRequestMethod(string $requestMethod) {
        $this->requestMethod = $requestMethod;
    }

    private function getRequestMethod() {
        return $this->requestMethod;
    }

    private function setResponse(Response $response) {
        $this->response = $response;
    }

    private function getResponse() : Response {
        return $this->response;
    }
}
?>
