<?php

namespace mono;

use app\App;

use mono\classes\Input;
use mono\classes\Log;
use mono\classes\Render;
use mono\classes\Events;
use mono\classes\PluginManager;
use mono\classes\Router;

use mono\constants\RenderType;

use mono\listeners\RenderListener;

use mono\models\Config;
use mono\models\Controller;

/**
 * Class Core
 * @package vfw
 */
class Core
{
    /**
     *
     * @var App
     */
    private $app = null;

    /**
     *
     * @var Config
     */
    private $config = null;

    /**
     *
     * @var int
     */
    private $startTime = 0;

    /**
     *
     * @var string
     */
    private $version = "Aplha-0.0.3";

    /**
     *
     * @var mixed
     */
    private $controller = null;

    /**
     *
     * @var string
     */
    private $method = null;

    /**
     *
     * @var array
     */
    private $loadedControllers = [];

    /**
     *
     * @var array
     */
    private $controllerArgs = [];

    /**
     *
     * @var array
     */
    private $variables = [];

    /**
     *
     * @var string
     */
    private $template = "default";

    /**
     * @var string
     */
    private $error404Controller = null;

    /**
     * @var Core
     */
    private static $instance;

    public function __construct()
    {

        session_start();
        $this->startTime = microtime(true);

        //Set instance so that it can be grabbed later using Mono();
        self::$instance = &$this;

        Log::info("Initializing <span data-toggle='tooltip' title='Version ". Mono()->version() . "'>Mono</span>");

        //Set main config to app.php
        $this->config = $this->getConfig("app");


        //Set Mono Language file
        $language = $this->getConfig()->getString("language");

        //Initialize app
        $this->setApp(
            new App(
                $this->getConfig()->getString("name"),
                $this->getConfig()->getString("author"),
                $this->getConfig()->getString("description"),
                $this->getConfig()->getString("version")
            )
        );


        //Set default render function to PHP file rendering
        Events::registerEventListener(new RenderListener());

        $this->setTemplate($this->getConfig()->getString("template"));

        //Disable sessions since it's disabled in the config
        if (!$this->getConfig("sessions")->getBoolean("enabled"))
        {
            session_abort();
        }
        if(file_exists(MONO_PATH . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $language . '.php'))
        {
            $this->applicationLanguage = include_once MONO_PATH . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $language . '.php';
        }
        else {
            Log::warning("Language \"{$language}\" was not found, reverting to English.");
            $this->applicationLanguage = include_once MONO_PATH . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR .'english.php';
        }

        Log::info("HTTP method: " . Input::method());

        $this->putAppControllers();
        $this->getApp()->beforePlugins();

        if (USE_PLUGINS)
        {
            PluginManager::loadPlugins();
            PluginManager::enablePlugins();
        }

        $this->getApp()->beforeRoutes();

        //Load routers from app routes
        $routeFiles = scandir(APP_PATH . "routes");
        foreach ($routeFiles as $file) {
            $arr = explode(".", $file);
            $extension = end($arr);
            if($extension === "php") {
                include_once APP_PATH . "routes" . DIRECTORY_SEPARATOR . $file;
            }
        }

        $routerInitialised = Router::initRouters();

        if(!$routerInitialised)
        {
            $this->loadController();
        }
        $this->getApp()->beforeController();
        if(!$routerInitialised) {
            $this->runController();
        }
        Log::info(sprintf($this->getLang()['log']['loaded'], $this->getExecutionTime(4)));
        Log::renderLogger();
    }

    /**
     *
     * @return string mono version
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * @param string $config, If not provided it will return the app.php config
     * @return Config
     */
    public function getConfig($config = null)
    {
        if($config) {
            return new Config(ROOT_PATH . "config" . DIRECTORY_SEPARATOR . $config . ".php");
        }
        return $this->config;
    }

    /**
     *
     * @return array language
     */
    public function getLang()
    {
        return $this->applicationLanguage;
    }

    /**
     *
     * @param int $round
     * @return float|mixed
     */
    private function getExecutionTime($round = 0)
    {
        if ($round) {
            return round((microtime(true) - $this->startTime), $round);
        } else {
            return (microtime(true) - $this->startTime);
        }
    }

    /**
     * This function will load all the default controllers to the loadedControllers Array.
     */
    private function putAppControllers()
    {
        $files = scandir(APP_PATH . "controllers");
        foreach ($files as $file) {
            $arr = explode(".", $file);
            $extension = end($arr);
            if($extension === "php") {
                $this->putController(substr($file, 0, strlen($file) - 4));
            }
        }
    }

    /**
     * This function will load the controller that the client is requesting.
     */
    private function loadController()
    {
        if (!count($this->getControllers())) {
            return;
        }
        $url = Router::URLArray();
        if (isset($url[0]) && !empty($url[0])) {
            $url[0] = str_replace("-", "_", $url[0]);
            if (isset($this->getControllers()[strtolower($url[0])])) {
                $controller = $this->getControllers()[strtolower($url[0])];
                if (file_exists($controller[1] . $url[0] . '.php')) {
                    include_once $controller[1] . $url[0] . '.php';
                    if (class_exists($url[0])) {
                        $this->controller = new $url[0]($this);
                        $this->method = $this->getControllers()[strtolower($url[0])];
                        if (!($this->controller instanceof Controller)) {
                            Log::severe(sprintf($this->getLang()['errors']['controller_error_3'], $url[0]));
                        }
                        unset($url[0]);
                    } else {
                        Log::severe(sprintf($this->getLang()['errors']['controller_error_1'], $url[0], $controller[1] . $controller[0] . '.php'));
                    }
                } else {
                    Log::warning(sprintf($this->getLang()['errors']['controller_error_0'], $url[0], $controller[1]));
                    if (404) {
                        $this->error404();
                    }
                }
            } else {
                Log::warning(sprintf($this->getLang()['errors']['controller_error_2'], $url[0]));
                if (404) {
                    $this->error404();
                }
            }
        }

        if ($this->controller == null) {
            $controller = $this->getConfig()->getString("default_controller.name");
            if (file_exists(APP_PATH . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php')) {
                include_once APP_PATH . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';
                if (class_exists($controller)) {
                    $this->controller = new $controller($this);
                    if (!($this->controller instanceof Controller)) {
                        Log::severe(sprintf($this->getLang()['errors']['controller_error_3'], $controller));
                    }
                    $url[0] = $this->getConfig()->getString("default_controller.method");
                } else {
                    Log::severe(sprintf($this->getLang()['errors']['controller_error_1'], $controller, APP_PATH . 'controllers' . DIRECTORY_SEPARATOR . $this->getConfig()->getString("default_controller") . '.php'));
                }
            } else {
                Log::warning(sprintf($this->getLang()['errors']['controller_error_0'], $controller, APP_PATH . 'controllers' . DIRECTORY_SEPARATOR));
                if (404) {
                    $this->error404();
                } else {
                    exit();
                }
            }
        }

        if (isset($url[1])) {
            if (method_exists($this->controller, str_replace("-", "_", $url[1]))) {
                $this->method = str_replace("-", "_",  $url[1]);
                unset($url[1]);
            } else {
                Log::warning(sprintf($this->getLang()['errors']['controller_error_4'], $url[1], $this->getController()->getControllerName()));
                $this->method = $this->getControllers()[$this->controller->getControllerName()][0];
            }
        } else {
            $defaultMethod = $this->getControllers()[$this->controller->getControllerName()][0];
            $this->method = isset($url[0]) ? $url[0] : $defaultMethod;
            if (!method_exists($this->controller, $this->method)) {
                Log::warning(sprintf($this->getLang()['errors']['controller_error_4'], $this->method, $this->getController()->getControllerName()));
                $this->method = "index";
                if (!method_exists($this->controller, $this->method)) {
                    if (404) {
                        $this->error404();
                    } else {
                        exit();
                    }
                }
            } else {
                unset($url[0]);
            }
        }

        if (isset($url)) {
            $this->controllerArgs = array_values($url);
        }
        Log::info(sprintf($this->getLang()['log']['loaded_controller'], $this->getController()->getControllerName(), $this->method, json_encode($this->controllerArgs)));
    }

    /**
     * This function will call the method of the controller that the client has requested.
     */
    private function runController()
    {
        if (!count($this->getControllers())) {
            return;
        }
        $reflection = new \ReflectionMethod($this->controller, $this->method);
        if ($reflection->isPublic()) {
            $result = call_user_func_array([
                $this->controller,
                $this->method
            ], $this->controllerArgs);
            if(is_bool($result) && $result) {
                Render::render($this->getControllerName(), RenderType::FILE);
            }
        } else {
            Router::to($this->getConfig()->getString("default_controller.name"));
        }
    }

    /**
     * This function allows you to dynamically add a controller to the current controllers without modifying the config.php
     *
     * @param string $controller
     * @param string $defaultMethod
     * @param string $controllerLocation
     */
    public function putController($controller, $defaultMethod = "index", $controllerLocation = null)
    {
        $controller = strtolower(str_replace("-", "_", $controller));
        if (!$controllerLocation) {
            $controllerLocation = APP_PATH . "/controllers/";
        }
        if (isset($this->loadedControllers[$controller])) {
            Log::warning(sprintf($this->getLang()['errors']['controller_exists'], $controller));
            return;
        }
        $currentControllers = $this->getConfig()->getArray("application.controllers");
        if (!isset($currentControllers[$controller])) {
            $currentControllers[] = $controller;
            $this->getConfig()->set("application.controllers", $currentControllers);
        }
        $this->loadedControllers[$controller] = [
            $defaultMethod,
            $controllerLocation
        ];
    }

    /**
     * This function returns the selected controller by the client
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    public function getControllerName()
    {
        return strtolower(get_class($this->getController()));
    }

    /**
     * This function returns the pre loaded controllers by VenomFramework.
     * @return array
     */
    public function getControllers()
    {
        return $this->loadedControllers;
    }

    /**
     * This function returns any controller arguments provided by the client.
     * @return array
     */
    public function getControllerArguments()
    {
        return $this->controllerArgs;
    }

    /**
     * This function returns all of the defined variables.
     * @return array variables
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * This function returns a variable if it exits. If the variable does not exist it will return null.
     * @param string $key
     * @return mixed variable
     */
    public function getVariable($key)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : null;
    }

    /**
     * This function allows you to add a variable to the controller.
     *
     * @param string $varaible
     * @param string $value
     */
    public function addVariable($varaible, $value)
    {
        $this->variables[$varaible] = $value;
    }

    /**
     * This function returns the template name of the application.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * This function sets the template name of the application.
     *
     * @param string $name
     */
    public function setTemplate($name)
    {
        $this->template = $name;
    }

    /**
     * This function sends a 404 error to the client and sends a HTML document.
     * You can disable the HTML document by adding the $showpage boolean parameter.
     *
     * @param boolean $showpage
     */
    public function error404($showpage = true)
    {
        http_response_code(404);
        if ($showpage && $this->error404Controller == null) {
            include_once MONO_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '404.php';
        } else {
            $this->controller = $this->error404Controller;
            if (file_exists( APP_PATH . '/controllers/' . $this->error404Controller . '.php')) {
                include_once APP_PATH . '/controllers/' . $this->error404Controller . '.php';
                if (class_exists($this->error404Controller)) {
                    $this->controller = new $this->error404Controller($this);
                    if (!($this->controller instanceof Controller)) {
                        Log::severe(sprintf($this->getLang()['errors']['controller_error_3'], $this->error404Controller));
                    }
                    $this->method = "index";
                    $this->runController();
                } else {
                    include_once MONO_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '404.php';
                }
            } else {
                include_once MONO_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '404.php';
            }
        }
        $errorSrc = debug_backtrace()[0];
        Log::info(sprintf($this->getLang()['log']['404error'], $errorSrc['file'], $errorSrc['line']));
        Log::renderLogger();
        exit();
    }

    /**
     * @return string
     */
    public function getError404Controller()
    {
        return $this->error404Controller;
    }

    /**
     * @param string $error404Controller
     */
    public function setError404Controller($error404Controller)
    {
        $this->error404Controller = $error404Controller;
    }

    /**
     * @param $app App
     */
    protected function setApp($app) {
        $this->app = $app;
    }

    /**
     * @return App
     */
    public function getApp() {
        return $this->app;
    }

    /**
     * @return Core
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param Core $instance
     */
    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }

}