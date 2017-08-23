<?php

namespace mono\models;

abstract class Controller
{

    /**
     *
     * @var string
     */
    private $controllerName;


    public function __construct()
    {
        $this->controllerName = strtolower(get_class($this));
    }

    /**
     * Default controller method
     */
    public abstract function index();

    /**
     * @return string $controllerName
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }
}