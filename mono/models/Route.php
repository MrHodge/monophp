<?php

namespace mono\models;

use mono\classes\Input;
use mono\classes\Log;
use mono\classes\Router;
use mono\constants\RouterType;

class Route {

    /**
     * @var RouterType
     */
    private $routerType;

    /**
     * @var string
     */
    private $routeString;

    /**
     * @var array
     */
    private $routeArray = [];

    /**
     * @var callable
     */
    private $function;

    /**
     * @var bool
     */
    private $strict;

    /**
     * @var Parameter[]
     */
    private $parameters = [];


    /**
     * Route constructor.
     * @param RouterType $routerType
     * @param string $routeString
     * @param callable $function
     * @param boolean $strict
     */
    public function __construct($routerType, $routeString, $function, $strict = false)
    {
        $this->setRouterType($routerType);
        $this->setRouteString($routeString);
        $this->setFunction($function);
        $this->setStrict($strict);
    }

    /**
     * This function will automatically run by Mono. Usually it should not be ran more than once.
     * @return bool
     * @internal
     */
    public function init()
    {
        //Run checks if current route has this route string then run vibes
        $routeArray = $this->getRouteArray();
        $routerURLArray = Router::URLArray();
        if(isset($routeArray[0]) && $routeArray[0] === "") $routeArray[0] = "/";
        if(empty($routerURLArray) || $routerURLArray[0] === "") $routerURLArray = ["/"];
        if(!count($routeArray))
        {
            Log::warning("Route has an empty route string. Provide one if it should be initialised");
            return false;
        }
        if($this->routerType != RouterType::ANY && strtolower($this->routerType) != strtolower(Input::method())){
            Log::info("Route \"{$this->routeString}\" has a method of \"{$this->getRouterType()}\", skipping");
            return false;
        }
        if(count($routeArray) != count($routerURLArray))
        {
            Log::info("Route \"{$this->routeString}\" doesn't match current route URL length, skipping");
            return false;
        }
        $position = 0;
        foreach ($routerURLArray as $key)
        {
            if(!isset($routeArray[$position]))continue;
            $currentRouterKey = $routerURLArray[$position];
            $currentRouteKey = $routeArray[$position];

            if(!$this->isStrict())
            {
                $currentRouteKey = strtolower($routeArray[$position]);
                $currentRouterKey = strtolower($routerURLArray[$position]);
            }
            if(strlen($currentRouteKey) > 1 && startsWith(":", $currentRouteKey))
            {
                $id = strtolower(substr($currentRouteKey, 1));
                $value = $routerURLArray[$position];
                //Add parameter
                $this->addParameter(new Parameter($id, $value));
                //Add parameter to input
                Input::addInput($id, $value);
                $position++;
                continue;
            }
            if(!$this->isStrict()){
                $key = strtolower($key);
            }
            if($currentRouteKey !== $key)
            {
                Log::info("Route \"{$this->routeString}\" doesn't match current route URL, skipping");
                return false;
            }
            $position++;
        }
        Log::info("Running route  \"{$this->routeString}\"");
        call_user_func($this->function);
        Log::info("Route \"" . $this->routeString . "\" loaded successfully");
        return true;
    }

    /**
     * @return RouterType
     */
    public function getRouterType()
    {
        return $this->routerType;
    }

    /**
     * @param RouterType $routerType
     * @return $this
     */
    public function setRouterType($routerType)
    {
        $this->routerType = $routerType;
        return $this;
    }

    /**
     * @return string
     */
    public function getRouteString()
    {
        return $this->routeString;
    }

    /**
     * @param string $routeString
     * @return $this
     */
    public function setRouteString($routeString)
    {
        $this->routeString = (strlen($routeString)) ?
            startsWith("/", $routeString) ?
                $routeString
                : "/" . $routeString
            : $routeString;
        $this->setRouteArray(strlen($this->getRouteString()) < 1 ? [] : Router::URLToArray($this->getRouteString()));
        return $this;
    }

    /**
     * @return callable
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param callable $function
     * @return $this
     */
    public function setFunction($function)
    {
        $this->function = $function;
        return $this;
    }

    /**
     * @return array
     */
    public function getRouteArray()
    {
        return $this->routeArray;
    }

    /**
     * @param array $routeArray
     * @return $this
     */
    public function setRouteArray($routeArray)
    {
        $this->routeArray = $routeArray;
        return $this;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param Parameter[]
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * @param bool $strict
     * @return $this
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;
        return $this;
    }


    /**
     * @param Parameter $parameter
     * @return $this
     */
    public function addParameter($parameter)
    {
        array_push($this->parameters, $parameter);
        return $this;
    }

    /**
     * @param Parameter $parameter
     * @return $this
     */
    public function removeParameter($parameter)
    {
        foreach ($this->parameters as $obj) {
            if($obj->getId() == $parameter->getId()){
                unset($this->parameters[$obj]);
                return $this;
            }
        }
        return null;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function removeParameterById($id)
    {
        foreach ($this->parameters as $obj) {
            if($obj->getId() == $id){
                unset($this->parameters[$obj]);
                return $this;
            }
        }
        return null;
    }

    /**
     * @param string $id
     * @return string | null
     */
    public function getParameter($id)
    {
        foreach ($this->parameters as $obj) {
            if($obj->getId() == $id){
                return $obj->getValue();
            }
        }
        return null;
    }

    /**
     * @param string $id
     * @return Parameter | null
     */
    public function getParameterObject($id)
    {
        foreach ($this->parameters as $obj) {
            if($obj->getId() == $id){
                return $obj;
            }
        }
        return null;
    }

}
