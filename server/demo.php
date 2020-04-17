<?php

@session_name("aghontpi");
@session_start();


/** 
 * response hardcoded for working with client side
*/

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

header('Access-Control-Allow-Origin: *');
echo json_encode($arrFailResp);

?>

