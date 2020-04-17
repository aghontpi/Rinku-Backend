<?php

/** 
 * response hardcoded for working with client side
*/
ini_set("display_errors",1);
$arrSuccessResp = [
    "response"=>"success",
    "content"=> [
        "user" => base64_encode("someVal"),
        "loginTime"=> time(),
        "otherParam"=> "..."
    ]
];


$arrFailResp = [
    "response"=>"error",
    "errors"=>[
        "errMsg"=>"Invalid credentials entered",
    ]
];



require_once "classes/request.php";
header("Access-Control-Allow-Origin: *");
header('Content-Type', 'application/json');
$req = new \server\classes\request();
//$req->handlePost();
echo(json_encode($arrFailResp));


?>

