<?php

namespace app;

use mono\classes\Variables;
use mono\models\Application;

class App extends Application
{

    /**
     * Here is the best place to register all your events.
     */
    public function init()
    {
        Variables::set("version", Mono()->version());
    }

    public function beforePlugins()
    {
        // TODO: Implement beforePlugins() method.
    }

    public function beforeRoutes()
    {
        // TODO: Implement beforeRoutes() method.
    }

    public function beforeController()
    {
        // TODO: Implement beforeController() method.
    }

}
