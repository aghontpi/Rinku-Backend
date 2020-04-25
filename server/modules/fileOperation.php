<?php

include_once __DIR__."/../interfaces/module.php";

use \server\interfaces\module;

class fileOperation implements module{

    private $fileList;
    private $inputs;
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
                "errMsg"=>"operation not valid",
            ]
        ];

    public function __construct(){
        
    }

    public function setInputs($content){
        $this->inputs = $content;
        return $this;
    }

    public function process(){
        switch($this->inputs['operation']){
            case "list":
                $this->list();
                break;
            default:
                break;
        }
        return $this;
    }

    private function list(){  
        $listing = [];
        $item = [
            "key"=>'idm.zip',
            "size"=>1024,
            "modified"=>time()
        ];
        array_push($listing, $item);

        $this->respSuccessTemplate['content'] = json_encode($listing);

        $this->response =  $this->respSuccessTemplate;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>