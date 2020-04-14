<?php
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

echo json_encode($arrFailResp);

?>