<?php

include_once __DIR__."/../abstract/module.php";

use \server\abstracts\module;

class managelinks extends module{

    public function __construct(){
        parent::__construct();
        $this->queryErrMsg = "Sorry, you have to be"
            ." logged in to perform this action";
        $this->repFailTemplate["errors"]['errMsg'] = "something went wrong,"
            ." please contact site admin";
        // set default error message
        $this->response = $this->repFailTemplate;
    }

    public function process(){
        $date = $this->inputs['date'];
        if(!empty($date)){
            return $this->stats($date);
        }
        return $this;

    }

    private function stats(){
        $preparedSql = $this->database->prepare(
            "SELECT 
                COUNT(dl.download_details_id) AS downloads, dd.path_of_file AS fname
            FROM 
                download_log dl            
            INNER JOIN 
                download_details dd
            ON 
                (dl.download_details_id = dd.download_id)
            GROUP BY 
                dl.download_details_id 
            ORDER BY 
                download_count 
            DESC"
        );
        if($preparedSql->execute() && $preparedSql->rowCount()>0){
            $statusResult = $preparedSql->fetchAll(PDO::FETCH_ASSOC);
            $this->respSuccessTemplate["content"]["stats"] = 
                json_encode($statusResult);
            $this->response = $this->respSuccessTemplate;
        }
        return $this;
    }


    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>