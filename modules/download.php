<?php

include_once __DIR__."/../abstract/module.php";

use \server\abstracts\module;
require_once(__DIR__.'/../lib/autoload.php');
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
        $this->reCaptchaError = "Captcha error, kindly try again";
    }

    public function process(){
        $fileId  = $this->inputs['fileid'];
        $action = $this->inputs['action'];
        $filepath = $this->inputs['filepath'];
        $captchaStatus = download::captcha === "enable";
        $captcha = $this->inputs['captcha'];
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
        // if download request, change error message && check captcha
        if(!empty($action)) {
            $this->repFailTemplate["errors"]['errMsg'] = $this->downloadErrMsg;
            $origin = $_SERVER['HTTP_ORIGIN'];
            /* for local environment remove the port */
            $origin = str_replace(":3000",'',$origin);
            $downloadUrl = $origin ."/".$_SERVER['PHP_SELF'];
            // for download request, we validate captcha
            if($captchaStatus && empty($captcha)){
                $this->repFailTemplate = $this->reCaptchaError;
            } else {
                // validate captcha with google's api
                try{
                    $reCaptchaInstance = new \ReCaptcha\ReCaptcha(download::captcha);
                } catch(Error $e){ 
                    error_log("permission not given for 'lib' folder");
                    return $this; // return with recaptcha error
                }
                
                // set domain-name in config, (i.e) server where hosted
                // ignore the client ip, since we don't want to hassle with
                // determining their real ip
                $response = $reCaptchaInstance
                                ->setExpectedHostname(download::domain)
                                ->setChallengeTimeout(50)
                                ->verify($captcha);
                // for debugging use $response->getErrorCodes();
                $response->isSuccess() ? $captcha = "verified" : "errors";
            }
                
        }
        $this->response = $this->repFailTemplate;
        // if captcha enabled and there is no-captcha in the download request, stop the request
        if(!empty($action) && $captchaStatus && empty($captcha) && $captcha !== "verified"){ return $this; }
        $prepedSql = $this->database->prepare(
            "SELECT * FROM download_details WHERE download_name = :downloadName
            AND status = 'Y'"
        );
        $prepedSql->execute([':downloadName'=>$fileId]);
        if ($prepedSql->rowCount() == 1){
            $fileDetailsDB = $prepedSql->fetch(PDO::FETCH_ASSOC);
            $_SESSION["downloadFile"] = $fileDetailsDB['download_id'];
            $_SESSION["location"] = $fileDetailsDB['path_of_file'];
            //@todo if file not found in path to file, return error
            $fileInfo["filename"] = basename($fileDetailsDB['path_of_file']);
            $fileInfo["filesize"] = filesize($fileDetailsDB['path_of_file']);
            // send the captcha config state to client to enable/disable
            $fileInfo["captcha"] = download::captcha;
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
            $this->respSuccessTemplate["content"] = $fileInfo;
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