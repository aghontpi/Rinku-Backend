<?php

include_once __DIR__."/../abstract/module.php";

use \server\abstracts\module;

class download extends module{

    public function __construct(){
        parent::__construct();
        $this->repFailTemplate["errors"] = [
            "errMsg"=>"Invalid Download Link",
        ];
        $this->downloadErrMsg = "something went wrong,"
            ." please contact site admin";
    }

    public function process(){
        $fileId  = $this->inputs['fileid'];
        $action = $this->inputs['action'];
        $downloadUrl = NULL;
        // if download request, change error message.
        if(!empty($action)) {
            $this->repFailTemplate["errors"]['errMsg'] = $this->downloadErrMsg;
            $origin = $_SERVER['HTTP_ORIGIN'];
            /* for local environment remove the port */
            $origin = str_replace(":3000",'',$origin);
            $downloadUrl = $origin ."/".$_SERVER['PHP_SELF'];
        }
        $this->response = $this->repFailTemplate;
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
            /* if the post req is download action, set the download url */
            !empty($action) && $fileInfo['downloadUrl'] = $downloadUrl;
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