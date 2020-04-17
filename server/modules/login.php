<?php


class login {
    private $inputs = null;
    private $response = null;
    // private $respSuccessTemplate = [
    //     "response"=>"success",
    //         "content"=> [
    //             "user" => base64_encode("someVal"),
    //             "loginTime"=> time(),
    //             "otherParam"=> "..."
    //         ]
    //     ];
    private $repFailTemplate = [
            "response"=>"error",
            "errors"=>[
                "errMsg"=>"Invalid credentials entered",
            ]
        ];


    public function setInputData($content){
        $this->inputs = $content;
        return $this;
    }



    public function process(){
        $this->response = $this->repFailTemplate;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>