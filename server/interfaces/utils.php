<?php
namespace server\interfaces;

interface utils{
    public function getRequestType() : string;

    public function getContentType() : string;

    public function getRawData() : array;
}

?>