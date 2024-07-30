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
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $this->setTotalRoute($_SERVER[self::REQUEST_URI]);
        $this->setResponse(new Response());
    }

    public function executeRequest(): void {
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

    private function validadePrefixRoute(): void {
        if ($this->invalidPrefixRoute()) {
            throw new ApiException(true, ApiExceptionTypes::ERROR, [$this->generateNotFoundMessage(Server::PREFIX_API)], StatusCodes::HTTP_NOT_FOUND);
        }
    }

    private function invalidPrefixRoute(): bool {
        $prefix = $this->getPrefixInRoute();
        return (!empty(Server::PREFIX_API) && $prefix != Server::PREFIX_API);
    }

    private function defineRequestData(): void {
        $this->defineTreatedRoute();
        $this->mouteRequestInstance();
        $this->defineRequestMethod();
    }

    private function defineTreatedRoute(): void {
        $route = $_SERVER[self::PATH_INFO];
        //Remove route prefix
        if (!empty(Server::PREFIX_API)) {
            $route = str_replace('/'.Server::PREFIX_API, '' ,$route);
        }
        $this->setRoute($route);
    }

    private function mouteRequestInstance():void {
        $this->request = Request::getInstance();
    }

    private function defineRequestMethod():void {
        $this->setRequestMethod($_SERVER[self::REQUEST_METHOD]);
    }

    private function processRequest():void {
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
        return Route::fecthRouteList()[$this->getRequestMethod()][$this->getRoute()]->executeEndpoint();
    }

    private function getPrefixInRoute(): ?string {
        return explode('/',$this->getTotalRoute())[1];
    }

    private function apiRouteIsDefined(): bool {
        if ($_SERVER[self::REQUEST_METHOD] === self::METHOD_OPTIONS) {
            $route = $this->getRoute();
            $routeList = Route::fecthRouteList();
            
            return (isset($routeList[self::METHOD_POST]) && isset($routeList[self::METHOD_POST][$route])) ||
                   (isset($routeList[self::METHOD_PUT]) && isset($routeList[self::METHOD_PUT][$route])) ||
                   (isset($routeList[self::METHOD_DELETE]) && isset($routeList[self::METHOD_DELETE][$route]));
        }
    
        return array_key_exists($this->getRoute(), Route::fecthRouteList()[$this->getRequestMethod()]);
    }

    public function defineInternalErrorResponse($error): void {
        $this->defineResponse($this->generateInternalErrorMessage($error), StatusCodes::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function defineApiExceptionErrorResponse(ApiException $apiException): void {
        $this->defineResponse($apiException->getMessage(), $apiException->getCode());
    }

    public function defineResponse(string $responseMessage, int $statusCode): void {
        $this->getResponse()->setStatusCode($statusCode);
        $this->getResponse()->setResponseMessage($responseMessage);
    }

    private function generateNotFoundMessage(string $resourceNotFounded) : string {
        return $resourceNotFounded . " " . ServerMessage::NOT_FOUND;
    }

    private function generateInternalErrorMessage($error) : string {
        return ServerMessage::INTERNAL_SERVER_ERRO . $error;
    }

    private function sendResponse(): void {
        echo $this->getResponse()->generateServerResponse();
    }

    private function setTotalRoute(string $totalRoute): void {
        $this->totalRoute = $totalRoute;
    }

    private function getTotalRoute(): string {
        return $this->totalRoute;
    }

    private function setRoute(string $route): void {
        $this->route = $route;
    }

    private function getRoute(): string {
        return $this->route;
    }

    private function setRequestMethod(string $requestMethod): void {
        $this->requestMethod = $requestMethod;
    }

    private function getRequestMethod(): string {
        return $this->requestMethod;
    }

    private function setResponse(Response $response): void {
        $this->response = $response;
    }

    private function getResponse() : Response {
        return $this->response;
    }
}
?>
