<?php

namespace mono\models;

use mono\classes\Log;

class Config
{

    /**
     *
     * @var array
     */
    private $cnfData = null;

    /**
     * @param string $fileLocation
     * @param string $type
     */
    public function __construct($fileLocation, $type = "php")
    {
        if (file_exists($fileLocation)) {
            if (file_get_contents($fileLocation) == "") {
                return;
            }
            switch ($type) {
                case "json" :
                    $this->cnfData = json_decode(file_get_contents($fileLocation), true);
                    break;
                default :
                    $this->cnfData = include $fileLocation;
                    break;
            }
        } else {
            Log::severe(sprintf(Mono()->getLang()['errors']['config_error_0'], $fileLocation));
        }
    }

    /**
     *
     * @param string $path
     * @param string $type [string,
     *            integer, boolean, array]
     * @return mixed
     */
    public function get($path = null, $type = null)
    {
        if($path == null) {
            return (object) $this->getConfigData();
        }
        $array = explode(".", $path);
        $data = $this->getConfigData();
        foreach ($array as $key) {
            if (isset($data[$key])) {
                $data = $data[$key];
            } else {
                $data = null;
            }
        }
        switch ($type) {
            case "string" :
                if (isset($data) && !is_string($data)) {
                    Log::severe(sprintf($this->vfwCore->getLang()['errors']['config_error_1'], $path));
                } elseif (!isset($data)) {
                    $data = null;
                }
                break;
            case "integer" :
                if (isset($data) && !is_int($data)) {
                    Log::severe(sprintf($this->vfwCore->getLang()['errors']['config_error_2'], $path));
                } elseif (!isset($data)) {
                    $data = 0;
                }
                break;
            case "boolean" :
                if (isset($data) && !is_bool($data)) {
                    Log::severe(sprintf($this->vfwCore->getLang()['errors']['config_error_3'], $path));
                } elseif (!isset($data)) {
                    $data = false;
                }
                break;
            case "array" :
                if (isset($data) && !is_array($data)) {
                    Log::severe(sprintf($this->vfwCore->getLang()['errors']['config_error_4'], $path));
                } elseif (!isset($data)) {
                    $data = null;
                }
                break;
            case "callable" :
                if (isset($data) && !is_callable($data)) {
                    Log::severe(sprintf($this->vfwCore->getLang()['errors']['config_error_4'], $path));
                } elseif (!isset($data)) {
                    $data = null;
                }
                break;
            default :
                break;
        }
        return $data;
    }

    /**
     *
     * @return string
     * @param string $path
     */
    public function getString($path)
    {
        return $data = $this->get($path, "string");
    }

    /**
     *
     * @return integer
     * @param string $path
     */
    public function getInt($path)
    {
        return $this->get($path, "integer");
    }

    /**
     *
     * @return boolean
     * @param string $path
     */
    public function getBoolean($path)
    {
        if ($this->get($path, "boolean")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @return array
     * @param string $path
     */
    public function getArray($path)
    {
        return $this->get($path, "array");
    }

    /**
     * @param string $path
     * @return callable mixed
     */
    public function getCallable($path) {
       return $this->get($path, "callable");
    }

    /**
     * @param string $path
     * @return callable mixed
     */
    public function getFunction($path) {
        return $this->getCallable($path);
    }

    /**
     * For security reasons this will only work when a configuration file is stored to as an object and the object is modified.
     * @param $path
     * @param $value
     */
    public function set($path, $value)
    {
        $array = explode(".", $path);
        $current = &$this->cnfData;
        foreach ($array as $key) {
            $current = &$current[$key];
        }
        $current = $value;
    }

    /**
     * @return array|mixed
     */
    public function getConfigData()
    {
        return $this->cnfData;
    }
}