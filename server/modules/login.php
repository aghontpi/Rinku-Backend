<?php


class login {
    private $inputs = null;
    private $response = null;
    private $respSuccessTemplate = [
        "response"=>"success",
        "content"=> [
            "user" => "",
            "loginTime"=> "",
            "otherParam"=> "..."
        ]
        ];
    // private $repFailTemplate = [
    //         "response"=>"error",
    //         "errors"=>[
    //             "errMsg"=>"Invalid credentials entered",
    //         ]
    //     ];


    public function setInputData($content){
        $this->inputs = $content;
        return $this;
    }


    public function process(){
        $this->authenticate();
    }

    private function authenticate(){
        $_SESSION["userId"] = "123";
        $this->respSuccessTemplate['content']["user"] = base64_encode("asd");
        $this->respSuccessTemplate['content']["loginTime"] = time();
        $this->response = $this->respSuccessTemplate;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>