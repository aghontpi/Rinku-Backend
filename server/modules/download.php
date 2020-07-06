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
        $this->queryFileErrMsg = "Sorry, you have to be"
            ." logged in to perform this action";
    }

    public function process(){
        $fileId  = $this->inputs['fileid'];
        $action = $this->inputs['action'];
        $filepath = $this->inputs['filepath'];
        $downloadUrl = NULL;
        // if filepath query request change error message.
        if(!empty($filepath)){
            $filepath = download::path . $filepath;
            $this->repFailTemplate["errors"]['errMsg'] = $this->queryFileErrMsg;
            $this->response = $this->repFailTemplate;
            // only logged in users must be able to do query,
            if(empty($_SESSION['userId']))
                return $this;
            return $this->queryFilePath($filepath);
        }
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
            !empty($action) 
                && $this->addDownloadLog($fileDetailsDB['download_id']) 
                && $fileInfo['downloadUrl'] = $downloadUrl;
            $this->respSuccessTemplate["content"]["file"] = $fileInfo;
            $this->response = $this->respSuccessTemplate;
        }
        return $this;
    }

    private function queryFilePath($filepath){
        $preparedSql = $this->database->prepare(
            "SELECT * FROM download_details WHERE path_of_file = :filepath 
            ORDER BY download_id DESC LIMIT 1"
        );
        $preparedSql->execute([":filepath"=>$filepath]);
        if ($preparedSql->rowCount() == 1){
            $fileDetailsDB = $preparedSql->fetch(PDO::FETCH_ASSOC);
            $fileInfo['downloadName'] = $fileDetailsDB['download_name'];
            $this->respSuccessTemplate["content"]["file"] = $fileInfo;
        } else {
            $this->respSuccessTemplate["content"]["file"] = 
               "id not yet generated";
        }
        $this->response = $this->respSuccessTemplate;
        return $this;
         
    }

    private function addDownloadLog($downloadDetailsId){
        $preparedSql = $this->database->prepare("
            INSERT INTO 
                download_log(download_details_id, ip_addr, user_agent, downloaded_by)
            VALUES
                (:dd_id,:ip,:ua,:user)
        ");
        $user = !empty($_SESSION['userId']) 
            ? $_SESSION['userId'] 
            : 0 ;
        $preparedSql->execute([
            "dd_id"=>$downloadDetailsId,
            "ip"=>$_SERVER['REMOTE_ADDR'],
            "ua"=>$_SERVER['HTTP_USER_AGENT'],
            "user"=>$user
        ]);
        return true;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>