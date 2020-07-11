<?php

namespace server\abstracts;
include_once __DIR__."/../interfaces/config.php";
include_once __DIR__."/../interfaces/module.php";
include_once __DIR__."/../classes/database.php";
use \server\interfaces\module as Imodule;
use \server\interfaces\config as Iconfig;

abstract class module implements Imodule,Iconfig{

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