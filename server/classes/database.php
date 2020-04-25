<?php
/**
 * db stuff
 */
namespace server\classes;

use PDO;
use PDOException;

class database{
    static $connection = null;

    public static function getConnection() {
        try{
            if(is_null(self::$connection))
                self::$connection =new PDO("mysql:host=database;dbname=backend_db;","user","user");
        }
        catch (PDOException $e){
            die($e->getMessage());
        }
        return self::$connection;
    }
}



?>