<?php

namespace mono\classes;

use mono\constants\EventPriority;

use mono\models\Event;
use mono\models\EventListener;

class Events
{

    /**
     * @var array
     */
    private static $listeners = [
        EventPriority::Highest => [],
        EventPriority::High => [],
        EventPriority::Medium => [],
        EventPriority::Low => [],
        EventPriority::Lowest => [],
    ];

    /**
     * @param Event $event
     */
    public static function call($event)
    {
        foreach (self::$listeners as $priority => $listeners) {
            foreach ($listeners as $listener) {
                if($listener["event"] == getShortName($event))
                {
                    if($event->isCancelled()){
                        unset($listener);
                        return;
                    }
                    call_user_func_array([$listener["listener"], $listener["method"]], [$event]);
                }
            }
        }
    }

    /**
     * Default priority is EventPriority::Medium
     *
     * @param EventListener $eventListener
     */
    public static function registerEventListener($eventListener)
    {
        if($eventListener instanceof EventListener) {
            //For each function in listener
            $rc = new \ReflectionClass($eventListener);
            foreach($rc->getMethods(\ReflectionProperty::IS_PUBLIC) as $method) {
                if($method->getName() === "__construct")continue;
                $priority = getPHPAttribute($eventListener, "priority", $method);
                $event = getPHPAttribute($eventListener, "param", $method);
                if ($event) {
                    if(!$priority) {
                        $priority = [];
                        $priority[0] = EventPriority::Medium;
                    }
                    $continue = false;
                    switch ($priority[0]) {
                        case EventPriority::Highest:
                        case EventPriority::High:
                        case EventPriority::Medium:
                        case EventPriority::Low:
                        case EventPriority::Lowest:
                            $continue = true;
                            break;
                        default:
                            Log::warning("Invalid @priority attribute on " . get_class($eventListener) . "::" . $method->getName() . ". Unable to add as listener. Pleas refer to the documentation", true);
                            break;
                    }
                    if ($continue) {
                        array_push(self::$listeners[$priority[0]], ["event" => $event[0], "listener" => $eventListener, "method" => $method->getName()]);
                        Log::info("Event listener " . get_class($eventListener) . "::" . $method->getName() . " was added for event \"{$event[0]}\".");
                    }
                }
            }
        } else {
            Log::warning(get_class($eventListener) . " must extend mono\\models\\EventListener");
        }
    }

}