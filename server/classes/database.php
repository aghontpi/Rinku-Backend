<?php
/**
 * db stuff
 */
namespace server\classes;

include_once __DIR__."/../interfaces/config.php";

use PDO;
use PDOException;
use \server\interfaces\config as Iconfig;

class database implements Iconfig{
    static $connection = null;

    public static function getConnection() {
        try{
            if(is_null(self::$connection))
                self::$connection =new PDO(
                    "mysql:host=".database::host.";"
                        ."dbname=".database::database.";",
                    database::user,
                    database::password
                );
        }
        catch (PDOException $e){
            die($e->getMessage());
        }
        return self::$connection;
    }
}



?>