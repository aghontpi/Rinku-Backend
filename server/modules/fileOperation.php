<?php

include_once __DIR__."/../abstract/module.php";

use \server\abstracts\module;

class fileOperation extends module{

    private $fileList;

    public function process(){
        switch($this->inputs['operation']){
            case "list":
                $this->list();
                break;
            default:
                break;
        }
        return $this;
    }

    private function list(){  
            $list = $this->scanDirectory();

            $this->respSuccessTemplate['content'] = json_encode($list);

            $this->response =  $this->respSuccessTemplate;
    }

    private function scanDirectory(){
        $listing = $this->scanDirRecursively(fileOperation::path);
        // template for showing in client side
        // $item = [
        //     "key"=>'idm.zip',
        //     "size"=>1024,
        //     "modified"=>time()
        // ];
        return $listing;
    }

    private function scanDirRecursively($dir){
        $items = [];
        $dirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach($dirIterator as $iteratorItem){
            $fileName = $iteratorItem->getFilename();
            if ($fileName == "." || $fileName == "..") { continue; }
            $relativePath = $iteratorItem->getPathname();
            if($relativePath[0] == "."){ $relativePath = ltrim($relativePath,".");}
            $modifiedTime =$iteratorItem->getMTime();
            $modifiedTime =  $modifiedTime * 1000;
            $item =[
                "key" =>  $relativePath,
                "size" => $iteratorItem->getSize(),
                "modified" => $modifiedTime
            ];
            array_push($items, $item);
        }
        return $items;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}

?>