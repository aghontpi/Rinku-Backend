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
    private $processBuffer;

    public function __construct(){
        /* dont process options request */
        if($this->getRequestType() == "OPTIONS")
            response::handleOptions();
        @session_name("aghontpi");
        @session_start();
    }

    public function handlePost(){
        if($this->getRequestType() != "POST" || 
                $this->getContentType() != "application/json")
            $this->throwError();
        $this->processBuffer =  $this->getRawData();
        return $this;
    }

    public function handleArgs(){
        if (empty($this->processBuffer) || empty($this->processBuffer['endPoint']))
            $this->throwBadRequest();
        $this->module = $this->processBuffer["endPoint"];
        $this->moduleData = $this->processBuffer["data"];
        $this->processBuffer = null;
    }
}

?>