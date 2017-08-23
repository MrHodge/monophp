<?php

namespace mono\classes;

use mono\constants\RenderType;
use mono\events\RenderEvent;

class Render
{

    /**
     * By default the render function will render files out of the template directory
     *
     * @param string $file
     * @param string $type
     * @param string $directory
     */
    public static function render($file, $type = RenderType::FILE, $directory = null) {

        if ($directory == null)
        {
            $directory = ROOT_PATH . 'templates'. DIRECTORY_SEPARATOR . Mono()->getTemplate() . DIRECTORY_SEPARATOR;
        }

        $renderEvent = new RenderEvent();
        $renderEvent->setFile($file);
        $renderEvent->setType($type);
        $renderEvent->setDirectory($directory);

        $renderEvent->call();

    }

    /**
     * @param string $content
     */
    public static function renderContent($content) {
        if(is_string($content)) {
            echo VParser::parse($content);
        } else {
            Log::warning("You can only render a string as content.");
        }
    }
}