<?php
    namespace Server\Routes;

    class RouteParams {
        public static $body = [];
        public static $query = [];

        public static function mountBody($body){
            $data = json_decode($body);
            self::$body = $data;        
        }

        public static function mountQuery($requestData){
            // Remove Url
            array_shift($requestData);
            self::$query = $requestData;
        }
    }

?>