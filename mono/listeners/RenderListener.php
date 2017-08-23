<?php

namespace mono\listeners;

use mono\classes\Log;
use mono\classes\Variables;
use mono\classes\VParser;
use mono\constants\RenderType;
use mono\models\EventListener;
use mono\events\RenderEvent;

class RenderListener extends EventListener
{
    /**
     * @priority EventPriority::Lowest
     * @param RenderEvent $event
     */
    public function onEvent($event)
    {
        $extension = ".php";

        Mono()->getConfig()->set("template_extension", $extension);

        $event->setExtension(Mono()->getConfig()->getString("template_extension"));

        if($event->getType() == RenderType::FILE)
        {
            if(!file_exists($event->getDirectory() . $event->getFile() . $event->getExtension())){
                Log::warning(sprintf(Mono()->getLang()['errors']['controller_error_5'], $event->getDirectory() . $event->getFile() . $event->getExtension(), Mono()->getControllerName()));
                return;
            }
        }

        if($event->getType() == RenderType::FILE)
        {
            require $event->getDirectory() . $event->getFile() . $event->getExtension();
        }
        elseif($event->getType() == RenderType::STRING)
        {
            echo VParser::init(false, Variables::get())->parse($event->getType());
        }
    }
}