<?php
    require_once('./Config/server.php');
    require_once('./Server/Routes/api.php');
    require_once('./Server/Routes/RouteParams.php');
    
    use Server\Routes\Route;
    use Server\Routes\RouteParams;

    header("Content-type: application/json; charset=utf-8");
    try{
        $totalRoute = $_SERVER["REQUEST_URI"];
        //take route prefix
        $prefix = explode('/',$totalRoute)[1];
        if($prefix != "api"){
            http_response_code(404);
            $Response = [
                'Message'=> "api NotFound"
            ];
    
            $Response = json_encode($Response);
            echo $Response;
        } else{
            $route = $_REQUEST["url"];
            //Remove toute prefix
            $route = str_replace('api/','' ,$route);
    
            //take request body
            $bodyJson = file_get_contents('php://input');
        
            $method = $_SERVER["REQUEST_METHOD"];
        
            RouteParams::mountQuery($_REQUEST);
            RouteParams::mountBody($bodyJson);
            
            if (array_key_exists($route,Route::fecthRouteList()[$method])){
                $class = Route::fecthRouteList()[$method][$route][0];
                $method = Route::fecthRouteList()[$method][$route][1];
                echo call_user_func(array($class, $method)); 
            } else {
                http_response_code(404);
                $Response = [
                    'Message'=> "NotFound"
                ];
        
                $Response = json_encode($Response);
                echo $Response;
            }
        }
    } catch (Exception $e){
        http_response_code(500);
        echo json_encode(["message"=>"erro no servidor:".$e]);
    }
?>