<?php
namespace Server\Routing;

use Server\Interfaces\InterfaceRequestMethods;

class Route implements InterfaceRequestMethods {

    private static $allRoutesRoutesFunctions = [
        self::METHOD_GET    => [],
        self::METHOD_POST   => [],
        self::METHOD_PUT    => [],
        self::METHOD_DELETE => []

    ];

    public static function get(Endpoint $endpoint) {
        self::$allRoutesRoutesFunctions[self::METHOD_GET][$endpoint->getEndpoint()] = $endpoint;
    }

    public static function post(Endpoint $endpoint) {
        self::$allRoutesRoutesFunctions[self::METHOD_POST][$endpoint->getEndpoint()] = $endpoint;
    }

    public static function put(Endpoint $endpoint) {
        self::$allRoutesRoutesFunctions[self::METHOD_PUT][$endpoint->getEndpoint()] = $endpoint;
    }

    public static function delete(Endpoint $endpoint) {
        self::$allRoutesRoutesFunctions[self::METHOD_DELETE][$endpoint->getEndpoint()] = $endpoint;
    }

    public static function fecthRouteList(){
        return self::$allRoutesRoutesFunctions;
    }
}
?>