<?php

namespace mono\classes;

use mono\constants\LogLevel;
use mono\events\LogLineEvent;
use mono\models\LogLine;

class Log
{
    /**
     *
     * @var LogLine[]
     */
    private static $log = [];

    /**
     * This function allows you to add a message to the Log of Mono.
     *
     * @param string $level
     * @param mixed $obj
     * @param bool $ignoreEvent
     */
    public static function log($level, $obj, $ignoreEvent = false)
    {
        switch ($level){
            case LogLevel::INFO:
            case LogLevel::WARNING:
            case LogLevel::SEVERE:
                break;
            default:
                self::log(LogLevel::WARNING, "Invalid log level " . $level . " setting level to LogLevel::INFO.");
                $level = LogLevel::INFO;
                break;
        }
        $fileNameLong = "";
        $fileNameShort = "";
        $fileLine = null;
        if(isset(debug_backtrace()[1]["file"])) {
            $file = debug_backtrace()[1]["file"];
            $fileNameLong = $file;
            $file = explode(DIRECTORY_SEPARATOR, debug_backtrace()[1]["file"]);
            $fileNameShort = explode(".", end($file))[0];
            $fileLine = debug_backtrace()[1]["line"];
        }
        foreach (explode("\n", $obj) as $value) {
            if (is_string($value) && endsWith(".", $value)) {
                $value = substr($value, 0, strlen($value) - 1);
            }
            $line = new LogLine($level, ["name" => $fileNameLong, "short_name" => $fileNameShort, "line" => $fileLine], $value);
            if ($ignoreEvent) {
                array_push(self::$log, $line);
            } else {
                $event = new LogLineEvent();
                $event->setLine($line);
                $event->call();
                array_push(self::$log, $line); //Do not set line to the value of event because we want to have the log contain correct data at all times.
            }
        }
    }

    /**
     * This function writes a line to the log with a prefix [Info].
     * This should be used for showing general information.
     *
     * @param string $content
     */
    public static function info($content)
    {
        self::log(LogLevel::INFO, $content);
    }

    /**
     * This function writes a line to the log with a prefix [Warning].
     * This should be used for showing warnings.
     *
     * @param string $content
     */
    public static function warning($content)
    {
        self::log(LogLevel::WARNING, $content);
    }

    /**
     * This function writes a line to the log with a prefix [Severe].
     * This should be used for showing errors.
     *
     * @param string $content
     */
    public static function severe($content)
    {
        self::log(LogLevel::SEVERE, $content);
        if(DEBUG) {
            self::renderLogger();
        }
        exit();
    }

    /**

     * @return LogLine[]
     */
    public static function getLog()
    {
        return self::$log;
    }

    /**
     * @return string
     */
    public static function getJSONLog()
    {
        return json_encode(self::$log);
    }

    public static function renderLogger() {
        if (DEBUG) {
            if (!headers_sent()) {
                header("Content-Type: text/html; charset=utf-8");
            }
            $log = str_replace("\n", "<br/>", Log::getLog());
            $log = str_replace("Info", '<span style="color:#2980b9;">Info</span>', $log);
            $log = str_replace("Warning", '<span style="color:#d35400;">Warning</span>', $log);
            $log = str_replace("Severe", '<span style="color:#c0392b;">Severe</span>', $log);
            Variables::set("log", $log);
            include MONO_PATH . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "log.php";
        }
    }

}