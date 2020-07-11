<?php

include_once __DIR__."/../abstract/module.php";
require_once(__DIR__.'/../lib/autoload.php');

use \server\abstracts\module;

class login extends module {
    private static $userDetails;

    public function __construct(){
        parent::__construct();
        // default to fail template
        $this->repFailTemplate["errors"] = [
            "errMsg"=>"Invalid credentials entered",
        ];
        $this->response = $this->repFailTemplate;
    }

    public function process(){
        $this->authenticate();
        return $this;
    }

    private function authenticate(){
        // even though captcha is disable in server, client will
        // still been shown recaptcha during login, but its not
        // validated in server, it is only validated if the config
        // is set to 'enable'
        if(login::captcha === "enable" && !$this->verifyCaptcha()){
            //captcha verify failed, stop proceeding give response
            $this->repFailTemplate["errors"]["errMsg"] = 
                "Capcha verify failed, kindly try again";
            $this->response = $this->repFailTemplate;
            return;
        }

        if(!$this->checkUserInDb() || !$this->checkUserPwdAndStatus()){
            $this->response = $this->repFailTemplate;
            return;
        }
        // set the session values, @todo: move this part to seperate function
        if($timeUpdatedForUser = $this->updateTimeInforUser()){
            $_SESSION["userId"] = self::$userDetails['user_id'];
            $this->respSuccessTemplate['content']["user"] = base64_encode(self::$userDetails['user_id']);
            $this->respSuccessTemplate['content']["nick"] =self::$userDetails['user_nick_name'];
            $this->respSuccessTemplate['content']["loginTime"] =$timeUpdatedForUser;
            $this->response = $this->respSuccessTemplate;
        }
        
    }

    // update last login time
    private function updateTimeInforUser(){
        $time = time();
        $timestamp = date("Y-m-d H:i:s", $time);
        $preparedSql = $this->database->prepare(
            "UPDATE user_details SET last_login_time = :utime WHERE user_id = :userid"
        );
        $preparedSql->execute([':userid'=>self::$userDetails['user_id'], ':utime'=>$timestamp]);
        return ($preparedSql->rowCount() > 0)? $time: FALSE;
    }

    // check pwd & user status
    private function checkUserPwdAndStatus(){
        $preparedSql = $this->database->prepare(
            "SELECT * FROM user_details WHERE user_name = :uname " . 
            "AND email_id_status = 'Y' AND status = 'Y'"
        );
        $preparedSql->execute([':uname'=>$this->inputs['uname'][0]]);
        self::$userDetails = $preparedSql->fetch(PDO::FETCH_ASSOC);
        return password_verify($this->inputs["pword"][0], self::$userDetails['user_password']);
    }

    // user not present
    private function checkUserInDb(){
        $preparedSql = $this->database->prepare("SELECT 1 FROM `user_details` WHERE `user_name` = :uname");
        $preparedSql->execute([':uname'=>$this->inputs['uname'][0]]);
        return ($preparedSql->rowCount() > 0);
    } 

    private function verifyCaptcha(){
         // validate captcha with google's api
         try{
            $reCaptchaInstance = new \ReCaptcha\ReCaptcha(login::secret);
        } catch(Error $e){ 
            throw new Exception($e->getMessage());
            error_log("permission not given for 'lib' folder");
            return $this; // return with recaptcha error
        }
        
        // set domain-name in config, (i.e) server where hosted
        // ignore the client ip, since we don't want to hassle with
        // determining their real ip
        $response = $reCaptchaInstance
                        ->setExpectedHostname(login::domain)
                        ->setChallengeTimeout(50)
                        ->verify($this->inputs['captcha']);
        // for debugging use $response->getErrorCodes();
        return $response->isSuccess();
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>