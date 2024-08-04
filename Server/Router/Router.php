<?php
namespace Server\Router;

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

/**
 * Class Router
 * 
 * This class handles routing and request execution.
 *
 * @package Server\Router
 * @author William Nahirnei Lopes
 */
class Router implements InterfacePHPRequest, InterfaceRequestMethods
{
    /**
     * @var string The complete route.
     */
    private string $totalRoute = '';

    /**
     * @var string The specific route.
     */
    private string $route = '';

    /**
     * @var string The request method (GET, POST, etc.).
     */
    private string $requestMethod;

    /**
     * @var Request The request instance.
     */
    private Request $request;

    /**
     * @var Response The response instance.
     */
    private Response $response;

    /**
     * Router constructor.
     */
    public function __construct() {
        header("Content-type: application/json; charset=utf-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $this->setTotalRoute($_SERVER[self::REQUEST_URI]);
        $this->setResponse(new Response());
    }

    /**
     * Executes the incoming request.
     *
     * @return void
     */
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

    /**
     * Validates the prefix of the route.
     *
     * @return void
     * @throws ApiException
     */
    private function validadePrefixRoute(): void {
        if ($this->invalidPrefixRoute()) {
            throw new ApiException(true, ApiExceptionTypes::ERROR, [$this->generateNotFoundMessage(Server::PREFIX_API)], StatusCodes::HTTP_NOT_FOUND);
        }
    }

    /**
     * Checks if the prefix of the route is invalid.
     *
     * @return bool
     */
    private function invalidPrefixRoute(): bool {
        $prefix = $this->getPrefixInRoute();
        return (!empty(Server::PREFIX_API) && $prefix != Server::PREFIX_API);
    }

    /**
     * Defines the data for the request.
     *
     * @return void
     */
    private function defineRequestData(): void {
        $this->defineTreatedRoute();
        $this->mouteRequestInstance();
        $this->defineRequestMethod();
    }

    /**
     * Defines and treats the route.
     *
     * @return void
     */
    private function defineTreatedRoute(): void {
        $route = $_SERVER[self::PATH_INFO];
        // Remove route prefix
        if (!empty(Server::PREFIX_API)) {
            $route = str_replace('/' . Server::PREFIX_API, '', $route);
        }
        $this->setRoute($route);
    }

    /**
     * Mounts the request instance.
     *
     * @return void
     */
    private function mouteRequestInstance(): void {
        $this->request = Request::getInstance();
    }

    /**
     * Defines the request method.
     *
     * @return void
     */
    private function defineRequestMethod(): void {
        $this->setRequestMethod($_SERVER[self::REQUEST_METHOD]);
    }

    /**
     * Processes the request.
     *
     * @return void
     * @throws ApiException
     */
    private function processRequest(): void {
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

    /**
     * Calls the controller method for the current route.
     *
     * @return mixed
     */
    private function callControllerMethodRoute() {
        return Route::fecthRouteList()[$this->getRequestMethod()][$this->getRoute()]->executeEndpoint();
    }

    /**
     * Gets the prefix from the route.
     *
     * @return string|null
     */
    private function getPrefixInRoute(): ?string {
        return explode('/', $this->getTotalRoute())[1];
    }

    /**
     * Checks if the API route is defined.
     *
     * @return bool
     */
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

    /**
     * Defines the response for internal errors.
     *
     * @param \Throwable $error The error.
     * @return void
     */
    public function defineInternalErrorResponse($error): void {
        $this->defineResponse($this->generateInternalErrorMessage($error), StatusCodes::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Defines the response for API exceptions.
     *
     * @param ApiException $apiException The API exception.
     * @return void
     */
    public function defineApiExceptionErrorResponse(ApiException $apiException): void {
        $this->defineResponse($apiException->getMessage(), $apiException->getCode());
    }

    /**
     * Defines the response.
     *
     * @param string $responseMessage The response message.
     * @param int $statusCode The status code.
     * @return void
     */
    public function defineResponse(string $responseMessage, int $statusCode): void {
        $this->getResponse()->setStatusCode($statusCode);
        $this->getResponse()->setResponseMessage($responseMessage);
    }

    /**
     * Generates a not found message.
     *
     * @param string $resourceNotFounded The resource not found.
     * @return string The not found message.
     */
    private function generateNotFoundMessage(string $resourceNotFounded): string {
        return $resourceNotFounded . " " . ServerMessage::NOT_FOUND;
    }

    /**
     * Generates an internal error message.
     *
     * @param \Throwable $error The error.
     * @return string The internal error message.
     */
    private function generateInternalErrorMessage($error): string {
        return ServerMessage::INTERNAL_SERVER_ERRO . $error;
    }

    /**
     * Sends the response.
     *
     * @return void
     */
    private function sendResponse(): void {
        echo $this->getResponse()->generateServerResponse();
    }

    /**
     * Sets the total route.
     *
     * @param string $totalRoute The total route.
     * @return void
     */
    private function setTotalRoute(string $totalRoute): void {
        $this->totalRoute = $totalRoute;
    }

    /**
     * Gets the total route.
     *
     * @return string The total route.
     */
    private function getTotalRoute(): string {
        return $this->totalRoute;
    }

    /**
     * Sets the route.
     *
     * @param string $route The route.
     * @return void
     */
    private function setRoute(string $route): void {
        $this->route = $route;
    }

    /**
     * Gets the route.
     *
     * @return string The route.
     */
    private function getRoute(): string {
        return $this->route;
    }

    /**
     * Sets the request method.
     *
     * @param string $requestMethod The request method.
     * @return void
     */
    private function setRequestMethod(string $requestMethod): void {
        $this->requestMethod = $requestMethod;
    }

    /**
     * Gets the request method.
     *
     * @return string The request method.
     */
    private function getRequestMethod(): string {
        return $this->requestMethod;
    }

    /**
     * Sets the response.
     *
     * @param Response $response The response.
     * @return void
     */
    private function setResponse(Response $response): void {
        $this->response = $response;
    }

    /**
     * Gets the response.
     *
     * @return Response The response.
     */
    private function getResponse(): Response {
        return $this->response;
    }
}
?>