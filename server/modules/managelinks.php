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
    }

    public function process(){
        $limit = $this->inputs['limit'];
        if (!empty($limit))
            return $this->queryFiles(($limit));
        return $this;

    }

    private function queryFiles($limit){
        // validate limit from client side
        if($limit != 10) {
            $this->repFailTemplate["errors"]['errMsg']
                = "sorry, you are only allowed to query 10";
            $this->response = $this->repFailTemplate;
            return $this;
        }

        // only allow 10 to be queried each time
        $limitCondition = " " . ($limit - 10) . ", 10";
        $prepedSql = $this->database->prepare(
            "SELECT 
                download_name,path_of_file,status
            FROM 
                download_details
            ORDER BY 
                download_id ASC LIMIT " 
            . $limitCondition
        );
        $prepedSql->execute();
        if ($prepedSql->rowCount() >  0){
            $linksSelect = $prepedSql->fetchAll(PDO::FETCH_ASSOC);
            $this->respSuccessTemplate["content"]["list"] = 
                json_encode($linksSelect);
            $this->response = $this->respSuccessTemplate;
        }
        return $this;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>