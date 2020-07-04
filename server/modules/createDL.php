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
        // we combine the path with configured path.
        $file  = createDL::path . $this->inputs['file'];
        if(!file_exists($file))
            return $this;
        $preparedSql = $this->database->prepare(
            "INSERT INTO 
                    `download_details` 
                (`download_id`, `download_name`, `path_of_file`, `status`, `create_by`,
                `updated_by`, `create_timestamp`, `update_timestamp`)
            VALUES 
                ('0', :download_name, :file_path, 1, :create_user_id, 0, now(), NULL);"
        );
        $insertSql = [
            "download_name" => bin2hex(random_bytes(4)),
            "file_path" => $file,
            "create_user_id" => $_SESSION['userId']
        ];
        //@todo check if the download_name is already present in database, regenerate if 
        // already present.
        $status = $preparedSql->execute(
            $insertSql
        );
        if(!$status)
            return $this;
        $this->respSuccessTemplate["content"]["downloadId"] = $insertSql['download_name'];
        $this->respSuccessTemplate["content"]["Msg"] = "link was Created";
        $this->response = $this->respSuccessTemplate;
        return $this;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>