<?php
    namespace Server\Routes;
    class Route {

        private static $allRoutesRoutesFunctions = [
            'GET'    => [],
            'POST'   => [],
            'PUT'    => [],
            'DELETE' => []
        ];

        public static function get($routeUri, $functionReference) {
            self::$allRoutesRoutesFunctions['GET'][$routeUri] = $functionReference;
        }

        public static function post($routeUri, $functionReference) {
            self::$allRoutesRoutesFunctions['POST'][$routeUri] = $functionReference;
        }

        public static function put($routeUri, $functionReference) {
            self::$allRoutesRoutesFunctions['PUT'][$routeUri] = $functionReference;
        }

        public static function delete($routeUri, $functionReference) {
            self::$allRoutesRoutesFunctions['DELETE'][$routeUri] = $functionReference;
        }

        public static function fecthRouteList(){
            return self::$allRoutesRoutesFunctions;
        }

    }