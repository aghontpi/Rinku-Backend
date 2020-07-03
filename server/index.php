<?php

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Headers: Content-Type, origin");
    header("Access-Control-Allow-Credentials: true");
    header("HTTP/1.1 200 ");
    exit;
}

/** 
 * response hardcoded for working with client side
*/
ini_set("display_errors",1);

require_once "classes/request.php";
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
// header('Content-Type', 'application/json');
$req = new \server\classes\request();
$req->handlePost()
    ->handleArgs()
    ->processReq();


?>

