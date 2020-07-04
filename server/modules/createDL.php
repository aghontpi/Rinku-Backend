<?php

include_once __DIR__."/../abstract/module.php";

use \server\abstracts\module;

class createDL extends module{

    public function __construct(){
        parent::__construct();
        // by default set error message to response
        $this->repFailTemplate["errors"] = [
            "errMsg"=>"Can not create Download Link",
        ];
        $this->response = $this->repFailTemplate;
    }

    public function process(){
        $file  = createDL::path . $this->inputs['file'];
        if(!file_exists($file))
            return $this;
        $sql = "INSERT INTO `dowload_details` (`download_id`, `download_name`, `path_of_file`, `status`, `create_by`, `updated_by`, `create_timestamp`, `update_timestamp`)
            VALUES ('0', '4c7c3f0893b92962', '/var/www/html/index.php', 1, '1', '0', now(), NULL);";
        $this->respSuccessTemplate["content"]["downloadId"] = "345345345";
        $this->respSuccessTemplate["content"]["Msg"] = "link was Created";
        $this->response = $this->respSuccessTemplate;
        return $this;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>