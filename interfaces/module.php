<?php

namespace server\interfaces;

interface module{

    public function setInputs($content);

    public function process();

    public function getResponse();
}

?>