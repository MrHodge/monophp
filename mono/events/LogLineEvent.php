<?php

namespace mono\events;

use mono\classes\Log;
use mono\models\Event;
use mono\models\LogLine;

class LogLineEvent extends Event
{

    /**
     * @var LogLine
     */
    private $line;

    /**
     * @return LogLine
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param LogLine $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

}
