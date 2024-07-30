<?php
namespace Server\Routing;

use Server\Interfaces\InterfaceRequestMethods;

/**
 * Class Route
 * 
 * Manages the registration and retrieval of routes for different HTTP methods.
 *
 * @package Server\Routing
 */
class Route implements InterfaceRequestMethods {

    /**
     * @var array An associative array storing routes categorized by HTTP methods.
     */
    private static $allRoutesRoutesFunctions = [
        self::METHOD_GET    => [],
        self::METHOD_POST   => [],
        self::METHOD_PUT    => [],
        self::METHOD_DELETE => []
    ];

    /**
     * Registers a GET route.
     *
     * @param Endpoint $endpoint The endpoint to register.
     * @return void
     */
    public static function get(Endpoint $endpoint): void {
        self::$allRoutesRoutesFunctions[self::METHOD_GET][$endpoint->getEndpoint()] = $endpoint;
    }

    /**
     * Registers a POST route.
     *
     * @param Endpoint $endpoint The endpoint to register.
     * @return void
     */
    public static function post(Endpoint $endpoint): void {
        self::$allRoutesRoutesFunctions[self::METHOD_POST][$endpoint->getEndpoint()] = $endpoint;
    }

    /**
     * Registers a PUT route.
     *
     * @param Endpoint $endpoint The endpoint to register.
     * @return void
     */
    public static function put(Endpoint $endpoint): void {
        self::$allRoutesRoutesFunctions[self::METHOD_PUT][$endpoint->getEndpoint()] = $endpoint;
    }

    /**
     * Registers a DELETE route.
     *
     * @param Endpoint $endpoint The endpoint to register.
     * @return void
     */
    public static function delete(Endpoint $endpoint): void {
        self::$allRoutesRoutesFunctions[self::METHOD_DELETE][$endpoint->getEndpoint()] = $endpoint;
    }

    /**
     * Fetches the list of all registered routes.
     *
     * @return array An associative array of routes categorized by HTTP methods.
     */
    public static function fecthRouteList(): array {
        return self::$allRoutesRoutesFunctions;
    }
}
?>