<?php
//echo phpinfo();
//die();
use RecursiveIteratorIterator;
function scanDirRecursively($dir){
    $items = [];
    $dirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach($dirIterator as $iteratorItem){
        $fileName = $iteratorItem->getFilename();
        if ($fileName == "." || $fileName == "..") { continue; }
        $item =[
            "key" =>  $iteratorItem->getPathname(),
            "size" => $iteratorItem->getSize(),
            "modified" => $iteratorItem->getMTime()
        ];
        array_push($items, $item);
    }
    return $items;
}
var_dump(scanDirRecursively((".")));

?>