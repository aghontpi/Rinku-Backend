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

    public function handleGet(){
        /* download the file by reading the file */
        if( $this->getRequestType() == "GET"
            && isset($_SESSION['location'])
            && ($filename =  $_SESSION['location']) != ""
            && file_exists($filename)
            ){
                unset($_SESSION['filename']);
                $mimeType = mime_content_type($filename);
                header('Content-Type: '.$mimeType);
                header("Content-disposition: attachment; filename=\"" . basename($filename) . "\""); 
                readfile($filename);
                exit();
            }
        
        return $this;
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
        $this->module = $this->sanitize($this->processBuffer["endPoint"]);
        $this->moduleData = $this->processBuffer["data"];
        $this->processBuffer = null;
        return $this;
    }

    public function processReq(){
        if(!$this->checkModule($this->module))
            $this->throwBadRequest();
        try {
            $module = new $this->module();
            $module->setInputs($this->moduleData)
                ->process()
                ->getResponse();
        } catch(\Exception $e) {
            die($e->getMessage());
            //@todo handle exception and send error message
        }
        
        
    }

    protected function checkModule($moduleName) : bool{
        $moduleName .= ".php";
        $moduleLocation = __DIR__."/../modules/".$moduleName;
        if(!file_exists($moduleLocation))
            return FALSE;
        require_once($moduleLocation);
        return TRUE;
    }
}

?>