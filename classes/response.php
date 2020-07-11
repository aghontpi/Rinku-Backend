<?php
namespace server\classes;

require_once(__DIR__."/../abstract/utils.php");
require_once(__DIR__."/../interfaces/response.php");
use server\abstracts\utils;
use server\interfaces\response as Iresponse;

class response extends utils implements Iresponse{
 
    /* handle pre options erquest */
    public static function handleOptions() {
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Headers: Content-Type, origin");
        header("Access-Control-Allow-Credentials: true");
        header("HTTP/1.1 200 ");
        exit;
    }
}

?>