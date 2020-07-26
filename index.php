<?php
// allowed cors
$cors = [
    "http://localhost:3000",
];

// check request-cors present in cors defined
if(in_array($_SERVER['HTTP_ORIGIN'], $cors)) {
    $key = array_search($_SERVER['HTTP_ORIGIN'],$cors);
    $cors['fixed'] = $cors[$key];
}
// set cors to server
$_SERVER['cors'] = [
    "Access-Control-Allow-Origin" =>  $cors['fixed']
];

unset($cors);

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    header("Access-Control-Allow-Origin: " . $_SERVER['cors']['Access-Control-Allow-Origin'] );
    header("Access-Control-Allow-Headers: Content-Type, origin");
    header("Access-Control-Allow-Credentials: true");
    header("HTTP/1.1 200 ");
    exit;
}

ini_set("display_errors",0);

require_once "classes/request.php";

// header('Content-Type', 'application/json');
$req = new \server\classes\request();
$req->handleGet()
    ->handlePost()
    ->handleArgs()
    ->processReq();


?>

