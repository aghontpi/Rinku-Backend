<?php

include_once __DIR__."/../abstract/module.php";

use \server\abstracts\module;

class download extends module{

    public function __construct(){
        parent::__construct();
        $this->repFailTemplate["errors"] = [
            "errMsg"=>"file is not valid",
        ];
    }

    public function process(){
        $fileId  = $this->inputs['fileid'];
        $prepedSql = $this->database->prepare(
            "SELECT * FROM download_details WHERE download_name = :downloadName"
        );
        $prepedSql->execute([':downloadName'=>$fileId]);
        if ($prepedSql->rowCount() == 1){
            $fileDetailsDB = $prepedSql->fetch(PDO::FETCH_ASSOC);
            $_SESSION["downloadFile"] = $fileDetailsDB['download_id'];
            $_SESSION["location"] = $fileDetailsDB['path_of_file'];
            //@todo if file not found in path to file, return error
            $fileInfo["filename"] = basename($fileDetailsDB['path_of_file']);
            $fileInfo["filesize"] = filesize($fileDetailsDB['path_of_file']);
            $this->respSuccessTemplate["content"]["file"] = $fileInfo;
            $this->response = $this->respSuccessTemplate;
        }
        return $this;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>