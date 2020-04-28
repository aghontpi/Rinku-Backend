<?php

namespace server\abstracts;
include_once __DIR__."/../interfaces/module.php";
include_once __DIR__."/../classes/database.php";
use \server\interfaces\module as Imodule;

abstract class module implements Imodule{

    protected $inputs;
    protected $response;
    protected $database;
    protected $respSuccessTemplate = [
        "response"=>"success",
        "content"=> [
            []
        ]
    ];
    protected $repFailTemplate = [
            "response"=>"error",
            "errors"=>[
                "errMsg"=>"...",
            ]
        ];

    public function __construct(){
        $this->database = \server\classes\database::getConnection();
    }

    public function setInputs($content){
        $this->inputs = $content;
        return $this;
    }

    abstract function process();

    abstract function getResponse();
}


?>