<?php

use mono\classes\Log;

defined('ROOT_PATH') OR exit('No direct script access is allowed.');

if (DEBUG) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

if(phpversion() < 5.3){
    die("The lowest version that you can use VNOX Framework with is 5.3.");
}
/**
 *  Simple loading function implemented by Mono.
 *  This auto-loader loads Classes as they are needed. The approach is similar to Java import keyword.
 *  To load a class in any class use the keyword "use" followed by the full path from the root directory to the file. Below in the comment is an example class.
 *
 *
 *   use app\models\User;
 *   use app\models\MyClass as myModel;
 *
 *   class MyClass {
 *
 *       public function __construct() {
 *          new myModel();  //Loads MyClass from directory ROOT_PATH/app/models/;
 *          new User();  //Loads User from directory ROOT_PATH/app/models/;
 *       }
 *
 *   }
 *
 *
 */

spl_autoload_register(function($class) {
    if (file_exists(ROOT_PATH . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php")) {
        require_once ROOT_PATH . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
    } else if (file_exists(DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php")) {
        require_once ROOT_PATH . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
    }
});


//Load function files first so that they can be used anywhere
$functionFiles = scandir(MONO_PATH . "functions");
foreach ($functionFiles as $file) {
    $arr = explode(".", $file);
    $extension = end($arr);
    if($extension === "php") {
        include_once MONO_PATH . "functions" . DIRECTORY_SEPARATOR . $file;
    }
}

//Composer auto loader
if(file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

function loadMono()
{
    $class = str_replace(ROOT_PATH, "", MONO_PATH . "Core");
    new $class();
}


set_error_handler(function($errno, $errstr, $errfile, $errline){
    Log::warning("Error thrown in " . $errfile . " at line " . $errline . ". Message: " . $errstr);
});

set_exception_handler(function($exception){
    http_response_code(500);
    Log::severe("Exception thrown in " . $exception->getFile() . " at line " . $exception->getLine() . ". Message: " . $exception->getMessage());
});

loadMono();