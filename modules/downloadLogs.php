<?php

include_once __DIR__."/../abstract/module.php";

use \server\abstracts\module;

class downloadLogs extends module{

    public function __construct(){
        parent::__construct();
        $this->queryErrMsg = "Sorry, you have to be"
            ." logged in to perform this action";
        $this->repFailTemplate["errors"]['errMsg'] = "something went wrong,"
            ." please contact site admin";
    }

    public function process(){
        // though login check is made in request entry, 
        // - checking it here again, because other module 
        // - can extend this module and call proeess,
        // - download module which does not require auth
        // - can call this if it extends,causing unauthenticated
        // - to be able to query the download logs
        if(empty($_SESSION['userId'])){
            $this->response =   $this->queryErrMsg;
            return $this;
        }
        $limit = $this->inputs['limit'];
        if (!empty($limit))
            return $this->queryLogs(($limit));
        return $this;

    }

    private function queryLogs($limit){
        // validate limit from client side
        if($limit >= 10000) {
            $this->repFailTemplate["errors"]['errMsg']
                = "sorry, you are only allowed to query untill 10000";
            $this->response = $this->repFailTemplate;
            return $this;
        }

        // only allow 10 to be queried each time
        $limitCondition = " " . ($limit - 10) . ", 10";
        $prepedSql = $this->database->prepare(
            "SELECT 
                download_log_id,
                ip_addr as ip,
                user_agent,
                concat(ud.user_name,'  (',ud.user_nick_name, ')') as user,
                requested_time as time,
                path_of_file as id,
                dd.download_name as path
            FROM 
                download_log
            INNER JOIN 
                download_details dd
            ON 
                (download_details_id = dd.download_id)
            INNER JOIN
                user_details ud
            ON
                (downloaded_by = ud.user_id)
            ORDER BY 
                download_log_id DESC LIMIT " 
            . $limitCondition
        );
        $prepedSql->execute();
        if ($prepedSql->rowCount() >  0){
            $linksSelect = $prepedSql->fetchAll(PDO::FETCH_ASSOC);
            $this->respSuccessTemplate["logs"]["list"] = 
                json_encode($linksSelect);
            $this->response = $this->respSuccessTemplate;
        } else {
            $this->repFailTemplate["errors"]['errMsg']
            = "sorry, End of the list reached";
        $this->response = $this->repFailTemplate;
        }
        return $this;
    }



    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>