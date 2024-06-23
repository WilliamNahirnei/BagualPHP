<?php
    namespace Server\Routes;
    include_once('Server/Interface/InterfaceRequestMethods.php');
    use Server\Iterface\InterfaceRequestMethods;
    class Route implements InterfaceRequestMethods {

        private static $allRoutesRoutesFunctions = [
            self::METHOD_GET    => [],
            self::METHOD_POST   => [],
            self::METHOD_PUT    => [],
            self::METHOD_DELETE => []
        ];

        public static function get($routeUri, $functionReference) {
            self::$allRoutesRoutesFunctions[self::METHOD_GET][$routeUri] = $functionReference;
        }

        public static function post($routeUri, $functionReference) {
            self::$allRoutesRoutesFunctions[self::METHOD_POST][$routeUri] = $functionReference;
        }

        public static function put($routeUri, $functionReference) {
            self::$allRoutesRoutesFunctions[self::METHOD_PUT][$routeUri] = $functionReference;
        }

        public static function delete($routeUri, $functionReference) {
            self::$allRoutesRoutesFunctions[self::METHOD_DELETE][$routeUri] = $functionReference;
        }

        public static function fecthRouteList(){
            return self::$allRoutesRoutesFunctions;
        }

    }