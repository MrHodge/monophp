<?php

namespace mono\models;

use mono\classes\PluginManager;

class Plugin
{

    /**
     *
     * @var Config
     */
    private $config = null;

    /**
     *
     * @var string
     */
    private $directory = null;

    /**
     *
     * @var array
     */
    private $data = null;

    /**
     *
     * @var string
     */
    private $name = null;

    /**
     *
     * @var double
     */
    private $version = null;

    /**
     *
     * @var string
     */
    private $mainClass = null;

    /**
     *
     * @var string
     */
    private $description = null;

    /**
     *
     * @var string
     */
    private $author = null;

    /**
     *
     * @var string
     */
    private $website = null;

    /**
     * Constructor.
     *
     * @param string $pluginDir
     * @param mixed $pluginData
     */
    public function __construct($pluginDir, $pluginData)
    {

        $this->mainClass = $pluginData->main;

        $this->directory = $pluginDir;

        $this->data = $pluginData;

        $this->name = $pluginData->name;

        $this->version = $pluginData->version;

        if (isset($pluginData->description)) {
            $this->description = $pluginData->description;
        }
        if (isset($pluginData->author)) {
            $this->author = $pluginData->author;
        }
        if (isset($pluginData->website)) {
            $this->website = $pluginData->website;
        }

        define(str_replace("/", "_", $this->getName()) . "_PATH", $this->getDirectory());

        if (file_exists(PLUGINS_PATH . "/" . $pluginDir . "/config.php")) {
            $this->config = new Config(PLUGINS_PATH . "/" . $pluginDir . "/config.php");
        }
    }

    /**
     * This function returns the PluginManager
     *
     * @return PluginManager
     */
    public function getPluginManager()
    {
        return new PluginManager();
    }

    /**
     * This function returns the exact directory of the plugin.
     *
     * @return string
     */
    public function getDirectory()
    {
        return PLUGINS_PATH. $this->directory . DIRECTORY_SEPARATOR;
    }

    /**
     * This function returns the name of the plugin which is set in the plugin.json
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * This function returns the version of the plugin which is set in the plugin.json
     *
     * @return double
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * This function returns the description of the plugin which is set in the plugin.json
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * This function returns the author of the plugin which is set in the plugin.json
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * This function returns the website of the plugin which is set in the plugin.json
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * This function returns the name of the main class which is set in the plugin.json
     *
     * @return string
     */
    public function getMainClass()
    {
        return $this->mainClass;
    }

    /**
     * This function returns the plugin.json in an object.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * This function returns the config that is in the root directory of the plugin.
     * If the file doesn't exist, the function will return null.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * This function returns a custom config with the name provided.
     * If the file was not found in the plugin's directory this function will return null.
     *
     * @param string $fileName
     * @return Config
     */
    public function getCustomConfig($fileName)
    {
        if (file_exists(PLUGINS_PATH . "/" . $this->getDirectory() . "/" . $fileName)) {
            return new Config(PLUGINS_PATH . "/" . $this->getDirectory() . "/" . $fileName, "json");
        } else {
            return null;
        }
    }

    /**
     * This function is called when the PluginManager enables the plugin.
     */
    public function onEnable()
    {
    }

    /*
     * This function is called when the PluginManager disables the plugin.
     */
    public function onDisable()
    {
    }

    /**
     * This function is called when there are controller arguments from the client.
     *
     * @param Input $inputMethod
     * @param array $controllerArguments
     */
    public function onArgument($inputMethod, $controllerArguments)
    {
    }

    /**
     * This function allows you to add a controller dynamically to the application without modifying the application's configuration.
     *
     * @param string $controller
     * @param string $defaultMethod
     */
    public function putController($controller, $defaultMethod = "index")
    {
        Mono()->putController($controller, $defaultMethod, $this->getDirectory() . "/controllers/");
    }
}