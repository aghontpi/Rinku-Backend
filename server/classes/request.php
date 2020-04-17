<?
namespace server\classes;
require_once(__DIR__."/../abstract/utils.php");
require_once(__DIR__."/../interfaces/request.php");
require_once(__DIR__."/../classes/response.php");
use server\abstracts\utils;
use server\interfaces\request as Irequest;
use server\classes\response;

class request extends utils implements Irequest{
    private $module;
    private $moduleData;

    public function __construct(){
        /* dont process options request */
        if($this->getRequestType() == "OPTIONS")
            response::handleOptions();
        @session_name("aghontpi");
        @session_start();
    }

    public function handlePost(){
        if($this->getRequestType() != "POST" || $this->getContentType() != "application/json")
            return $this->throwError();
        $parsedData =  $this->getRawData();
        print_r($parsedData);
    }
}

?>