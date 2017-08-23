<?php

namespace mono\models;


use mono\classes\Events;
use mono\classes\Log;
use mono\constants\LogLevel;

abstract class Event
{

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var bool
     */
    private $cancelled;

    /**
     * @var array
     */
    private $whoCancelled;

    function __construct()
    {
        $this->setEventName(getShortName($this));
        $this->setCancelled(false);
    }

    public function call()
    {
        Events::call($this);
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * @param bool $cancelled
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;
        if($this->cancelled == true){
            $this->whoCancelled = debug_backtrace()[0];
            Log::log(LogLevel::INFO, "Event \"{$this->eventName}\" was cancelled." , true, true);
        }
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @return object
     */
    public function getWhoCancelled()
    {
        return $this->whoCancelled;
    }
}