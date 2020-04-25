<?php

include_once __DIR__."/../interfaces/module.php";

use \server\interfaces\module;

class logout implements module{

    private $response;
    private $respSuccessTemplate = [
        "response"=>"success",
        "content"=> [
            []
        ]
    ];
    private $repFailTemplate = [
            "response"=>"error",
            "errors"=>[
                "errMsg"=>"...",
            ]
        ];

    public function setInputs($content){
        $this->inputs = $content;
        return $this;
    }

    public function process(){
        $this->response = $this->respSuccessTemplate;
        setcookie('aghontpi', false);
        session_destroy();
        return $this;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>