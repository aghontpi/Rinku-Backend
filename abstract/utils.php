<?php
namespace server\abstracts;

require_once(__DIR__."/../interfaces/utils.php");
use server\interfaces\utils as Iutils;

abstract class utils implements Iutils{
    public function getRequestType() : string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getContentType() : string {
        return $_SERVER["CONTENT_TYPE"];
    }

    public function getRawData() : array {
        $clientSideData = file_get_contents("php://input");
        $clientSideData = json_decode($clientSideData,1);
        return ($clientSideData) ?  $clientSideData : [];
    }

    public function throwError() {
        session_destroy();
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? 
            $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        http_response_code(405);
        $this->setCorsHeaders();
        header($protocol .' 405 Method Not Allowed');
        exit;
    }

    public function throwBadRequest() {
        session_destroy();
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? 
            $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        http_response_code(400);
        $this->setCorsHeaders();
        header($protocol .' 400 Bad Request');        
        exit;
    }

    public function setCorsHeaders(){
        header("Access-Control-Allow-Origin: " . $_SERVER['cors']['Access-Control-Allow-Origin'] );
        header("Access-Control-Allow-Credentials: true");
        return $this;
    }

    public function sanitize($content){
        return filter_var($content, FILTER_SANITIZE_STRING);
    }
}


?>