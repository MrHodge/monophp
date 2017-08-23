<?php

namespace mono\models;

class LogLine
{

    /**
     * @var string
     */
    public $level;

    /**
     * @var array
     */
    public $whoLogged;


    /**
     * @var mixed
     */
    public $content;

    /**
     * LogLine constructor.
     * @param string $level
     * @param array $whoLogged
     * @param mixed $content
     */
    public function __construct($level, $whoLogged, $content)
    {
        $this->level = $level;
        $this->whoLogged = $whoLogged;
        $this->content = $content;
    }

    function __toString()
    {
        return json_encode($this);
    }

}