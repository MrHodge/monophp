<?php

namespace mono\database\classes;

use mono\database\drivers\PDO;
use mono\database\models\DriverModel;

class Database
{
    /**
     * @param string $id
     * @return DriverModel
     * @throws \Exception
     */
    public static function initialize($id = null) {
        if(empty($id)) $id = "default";

        $databaseConfig = Mono()->getConfig("database")->get($id);
        if($databaseConfig == null) {
            throw new \Exception("Database settings with the id \"{$id}\" was not found.");
        }
        if(!isset($databaseConfig["type"]))
            throw new \Exception("Database type from the id \"{$id}\" was not found.");

        switch ($databaseConfig["type"]) {
            case "mysql.pdo":
                if(!isset($databaseConfig["host"]) || empty($databaseConfig["host"]))
                    throw new \Exception("Database host from the id \"{$id}\" was not found.");

                if(!isset($databaseConfig["port"]) || empty($databaseConfig["port"]))
                    throw new \Exception("Database port from the id \"{$id}\" was not found.");

                if(!isset($databaseConfig["database"]) || empty($databaseConfig["database"]))
                    throw new \Exception("Database from the id \"{$id}\" was not found.");

                if(!isset($databaseConfig["username"]) || empty($databaseConfig["username"]))
                    throw new \Exception("Database username from the id \"{$id}\" was not found.");

                if(!isset($databaseConfig["password"]))
                    throw new \Exception("Database password from the id \"{$id}\" was not found.");

                if(!isset($databaseConfig["table_prefix"]))
                    throw new \Exception("Database table_prefix from the id \"{$id}\" was not found.");

                return new PDO($databaseConfig["host"], $databaseConfig["port"], $databaseConfig["database"], $databaseConfig["table_prefix"], $databaseConfig["username"], $databaseConfig["password"]);
                break;
        }
    }

}