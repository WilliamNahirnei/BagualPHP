<?php
namespace Server\Router;

// require_once('./Server/Routing/api.php');

use Server\Routing\Route;
use Config\Server;
use Server\Constants\ApiExceptionTypes;
use Server\Constants\ServerMessage;
use Server\Interfaces\InterfacePHPRequest;
use Server\Constants\StatusCodes;
use Server\Interfaces\InterfaceRequestMethods;
use Server\Router\Response;
use Server\Errors\ApiException;
use Server\Routing\ApiManager;

class Router implements InterfacePHPRequest, InterfaceRequestMethods
{
    private string $totalRoute = '';

    private string $route = '';

    private string $requestMethod;

    private Request $request;

    private Response $response;

    public function __construct() {
        header("Content-type: application/json; charset=utf-8");
        $this->setTotalRoute($_SERVER[self::REQUEST_URI]);
        $this->setResponse(new Response());
    }

    public function executeRequest() {
        try {
            $this->validadePrefixRoute();
            $this->defineRequestData();
            $this->processRequest();
        } catch (\Throwable $e) {
            if ($e instanceof ApiException) {
                $this->defineApiExceptionErrorResponse($e);
            } else {
                $this->defineInternalErrorResponse($e);
            }
        }

        $this->sendResponse();
    }

    private function validadePrefixRoute() {
        if ($this->invalidPrefixRoute()) {
            throw new ApiException(true, ApiExceptionTypes::ERROR, [$this->generateNotFoundMessage(Server::PREFIX_API)], StatusCodes::HTTP_NOT_FOUND);
        }
    }

    private function invalidPrefixRoute() {
        $prefix = $this->getPrefixInRoute();
        return (!empty(Server::PREFIX_API) && $prefix != Server::PREFIX_API);
    }

    private function defineRequestData() {
        $this->defineTreatedRoute();
        $this->mouteRequestInstance();
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

    private function mouteRequestInstance() {
        $this->request = Request::getInstance();
    }

    private function defineRequestMethod() {
        $this->setRequestMethod($_SERVER[self::REQUEST_METHOD]);
    }

    private function processRequest() {
        $apiManager = new ApiManager();
        $apiManager->loadApiEndpoints();
        if ($this->apiRouteIsDefined()) {
            if ($_SERVER[self::REQUEST_METHOD] === self::METHOD_OPTIONS) {
                return;
            }
            $this->getResponse()->setResponseContent($this->callControllerMethodRoute());
        } else {
            throw new ApiException(true, ApiExceptionTypes::ERROR, [$this->generateNotFoundMessage(ServerMessage::ROUTE)], StatusCodes::HTTP_NOT_FOUND);
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
        return Route::fecthRouteList()[$this->getRequestMethod()][$this->getRoute()]->getControllerClass();
    }

    private function getMethodControllerRoute() {
        return Route::fecthRouteList()[$this->getRequestMethod()][$this->getRoute()]->getControllerMethod();
    }

    private function getPrefixInRoute() {
        return explode('/',$this->getTotalRoute())[1];
    }

    private function apiRouteIsDefined() {
        if ($_SERVER[self::REQUEST_METHOD] === self::METHOD_OPTIONS) {
            $route = $this->getRoute();
            $routeList = Route::fecthRouteList();
            var_export($routeList);
            
            return (isset($routeList[self::METHOD_POST]) && isset($routeList[self::METHOD_POST][$route])) ||
                   (isset($routeList[self::METHOD_PUT]) && isset($routeList[self::METHOD_PUT][$route])) ||
                   (isset($routeList[self::METHOD_DELETE]) && isset($routeList[self::METHOD_DELETE][$route]));
        }
    
        // Caso contrário, verifica se a rota está definida normalmente
        return array_key_exists($this->getRoute(), Route::fecthRouteList()[$this->getRequestMethod()]);
    }

    public function defineInternalErrorResponse($error) {
        $this->defineResponse($this->generateInternalErrorMessage($error), StatusCodes::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function defineApiExceptionErrorResponse(ApiException $apiException) {
        $this->defineResponse($apiException->getMessage(), $apiException->getCode());
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
