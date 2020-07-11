<?php

include_once __DIR__."/../abstract/module.php";

use \server\abstracts\module;
class logout extends module {

    public function process(){
        $this->response = $this->respSuccessTemplate;
        setcookie('aghontpi', false);
        session_destroy();
        return $this;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>