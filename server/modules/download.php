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
        $prepedSql = $this->datebase->prepare(
            "SELECT downlad_id FROM download_details WHERE download_name := downloadName"
        );
        $prepedSql->execute([':downloadName'=>$fileId]);
        $fileDetailsDB = $prepedSql->fetch(PDO::FETCH_ASSOC)['download_id'];
        if ($prepedSql->rowCount() == 1){
            $_SESSION["downloadFile"] = $fileDetailsDB['download_id'];
            $this->respSuccessTemplate["content"]["filename"] = basename($fileDetailsDB['path_of_file']);
            $this->respSuccessTemplate["content"]["filesize"] = filesize($fileDetailsDB['path_of_file']);
            $this->response = $this->respSuccessTemplate;
        }
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>