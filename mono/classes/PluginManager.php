<?php

namespace mono\classes;

use mono\models\Plugin;


class PluginManager
{

    /**
     * This is an array of enabled plugins.
     *
     * @var Plugin[]
     */
    private static $enabledPlugins = [];

    /**
     * This is an array of disabled plugins.
     *
     * @var Plugin[]
     */
    private static $disabledPlugins = [];

    /**
     *
     * @var Plugin[]
     */
    private static $pluginDependencies = [];

    /**
     *
     * @var Plugin[]
     */
    private static $loadedfirst = [];

    /**
     * @var bool
     */
    private static $enabled;


    /**
     * This function will search the plugins directory for folders.
     * It then decides that a folder is a plugin.
     * The folder will be searched for a file named "plugin.json" which contains vital information in order to
     * load the plugin.
     * Once the file "plugin.json" is found and is in valid JSON format, the plugin will be loaded into the disabled plugins array.
     */
    public static function loadPlugins()
    {
        if (!file_exists(PLUGINS_PATH)) {
            Log::warning(Mono()->getLang()['errors']['plugins_dir_notfound']);
            return;
        }

        $pluginDirs = scandir(PLUGINS_PATH);

        array_shift($pluginDirs);
        array_shift($pluginDirs);

        self::scanForPlugins($pluginDirs);

    }

    private static function scanForPlugins($directories, $parent = "") {
        foreach ($directories as $pluginDir) {
			if(!is_dir(PLUGINS_PATH . $parent . DIRECTORY_SEPARATOR . $pluginDir))continue;
            if (file_exists(PLUGINS_PATH . $parent . DIRECTORY_SEPARATOR . $pluginDir . DIRECTORY_SEPARATOR . "plugin.json")) {
                $pluginData = json_decode(file_get_contents(PLUGINS_PATH . $parent . DIRECTORY_SEPARATOR . $pluginDir . DIRECTORY_SEPARATOR . "plugin.json"));
                if ($pluginData && isset($pluginData->name) && isset($pluginData->version) && isset($pluginData->main)) {
                    $loadedPlugin = self::getPlugin($pluginData->name);
                    if (!$loadedPlugin) {
                        $mainFile = str_replace("\\", DIRECTORY_SEPARATOR, $pluginData->main) . ".php";
                        if (file_exists($mainFile)) {
                            require_once $mainFile;
                            $class = $pluginData->main;
                            if (class_exists($class)) {
                                $plugin = new $class($parent . DIRECTORY_SEPARATOR . $pluginDir, $pluginData);
                                if ($plugin instanceof Plugin) {
                                    self::$disabledPlugins[$plugin->getName()] = $plugin;
                                } else {
                                    Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_4'], $pluginData->main, PLUGINS_PATH . "/" . $pluginDir . ""));
                                }
                            } else {
                                Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_3'], $pluginData->main, PLUGINS_PATH . "/" . $pluginDir . ""));
                            }
                        } else {
                            Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_3'], $pluginData->main, PLUGINS_PATH . "/" . $pluginDir . ""));
                        }
                    } else {
                        Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_2'], $loadedPlugin->getName(), PLUGINS_PATH . "/" . $pluginDir . "/plugin.json"));
                    }
                } else {
                    Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_1'], $pluginDir));
                }
            } else {
                $directories = scandir(PLUGINS_PATH . $parent . DIRECTORY_SEPARATOR . $pluginDir . DIRECTORY_SEPARATOR);
                array_shift($directories);
                array_shift($directories);
                self::scanForPlugins($directories, $parent . DIRECTORY_SEPARATOR . $pluginDir);
            }
        }
    }

    /**
     * This function will enable all plugins with the order:
     * - dependency
     * - loadfirst
     * - normal
     */
    public static function enablePlugins()
    {
        if (self::$enabled) {
            $errorSrc = debug_backtrace()[1];
            Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_15'], $errorSrc['file'], $errorSrc['line']));
            return;
        }

        self::$enabled = true;

        foreach (self::$disabledPlugins as $plugin) {
            if (!isset(self::$enabledPlugins[$plugin->getName()]) && !isset(self::$pluginDependencies[$plugin->getName()]) && !isset(self::$loadedfirst[$plugin->getName()])) {
                self::enablePlugin($plugin);
            }
        }
    }

    /**
     *
     * @return Plugin[]
     */
    public static function getEnabledPlugins()
    {
        return self::$enabledPlugins;
    }

    /**
     *
     * @return Plugin[]
     */
    public static function getDisabledPlugins()
    {
        return self::$disabledPlugins;
    }

    /**
     *
     * @param string $name
     * @return Plugin
     */
    public static function getPlugin($name)
    {
        foreach (self::getEnabledPlugins() as $plugin) {
            if ($plugin->getName() === $name) {
                return $plugin;
            }
        }
        foreach (self::getDisabledPlugins() as $plugin) {
            if ($plugin->getName() === $name) {
                return $plugin;
            }
        }
        return null;
    }

    /**
     *
     * @param Plugin $plugin
     * @return boolean
     */
    public static function enablePlugin($plugin)
    {
        if (isset($plugin->getData()->depend)) {
            if (is_object($plugin->getData()->depend)) {
                foreach ($plugin->getData()->depend as $dependency => $version) {
                    $higherThan = false;
                    $lowerThan = false;
                    $lowerThanOrEqualTo = false;
                    $higherThanOrEqualTo = false;
                    $sign = substr($version, 0, 2);
                    $continue = true;
                    switch ($sign) {
                        case "<=" :
                            $version = substr($version, 2);
                            $lowerThanOrEqualTo = true;
                            $continue = false;
                            break;
                        case ">=" :
                            $version = substr($version, 2);
                            $higherThanOrEqualTo = true;
                            $continue = false;
                            break;
                        default :
                            break;
                    }
                    if ($continue) {
                        if (substr($version, 0, 1) === ">") {
                            $version = substr($version, 1);
                            $higherThan = true;
                        } elseif (substr($version, 0, 1) === "<") {
                            $version = substr($version, 1);
                            $lowerThan = true;
                        }
                    }

                    if ($dependency === "php") {
                        if ($higherThan) {
                            if (!version_compare(PHP_VERSION, $version, '>')) {
                                Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_7'], $plugin->getName(), $version));
                                return false;
                            }
                        } elseif ($lowerThan) {
                            if (!version_compare(PHP_VERSION, $version, '<')) {
                                Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_8'], $plugin->getName(), $version));
                                return false;
                            }
                        } elseif ($lowerThanOrEqualTo) {
                            if (!version_compare(PHP_VERSION, $version, '<=')) {
                                Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_19'], $plugin->getName(), $version));
                                return false;
                            }
                        } elseif ($higherThanOrEqualTo) {
                            if (!version_compare(PHP_VERSION, $version, '>=')) {
                                Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_18'], $plugin->getName(), $version));
                                return false;
                            }
                        } else {
                            if (!version_compare(PHP_VERSION, $version, '=')) {
                                Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_9'], $plugin->getName(), $version));
                                return false;
                            }
                        }
                    } else {
                        if ($dependency === $plugin->getName()) {
                            continue;
                        }
                        $dependencyAsPlugin = self::getPlugin($dependency);
                        if ($dependencyAsPlugin) {
                            if ($higherThan) {
                                if (!version_compare($dependencyAsPlugin->getVersion(), $version, '>')) {
                                    Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_10'], $plugin->getName(), $dependencyAsPlugin->getName(), $version));
                                    return false;
                                }
                            } elseif ($lowerThan) {
                                if (!version_compare($dependencyAsPlugin->getVersion(), $version, '<')) {
                                    Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_11'], $plugin->getName(), $dependencyAsPlugin->getName(), $version));
                                    return false;
                                }
                            } elseif ($lowerThanOrEqualTo) {
                                if (!version_compare($dependencyAsPlugin->getVersion(), $version, '<=')) {
                                    Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_17'], $plugin->getName(), $dependencyAsPlugin->getName(), $version));
                                    return false;
                                }
                            } elseif ($higherThanOrEqualTo) {
                                if (!version_compare($dependencyAsPlugin->getVersion(), $version, '>=')) {
                                    Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_16'], $plugin->getName(), $dependencyAsPlugin->getName(), $version));
                                    return false;
                                }
                            } else {
                                if (!version_compare($dependencyAsPlugin->getVersion(), $version, '=')) {
                                    Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_12'], $plugin->getName(), $dependencyAsPlugin->getName(), $version));
                                    return false;
                                }
                            }
                            if (!isset(self::$pluginDependencies[$dependencyAsPlugin->getName()]) && !isset(self::$enabledPlugins[$dependencyAsPlugin->getName()])) {
                                self::enablePlugin($dependencyAsPlugin);
                                if (isset(self::$disabledPlugins[$dependencyAsPlugin->getName()])) {
                                    Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_20'], $plugin->getName(), $dependencyAsPlugin->getName()));
                                    self::$pluginDependencies[$dependency] = true;
                                    return false;
                                }
                            }
                        } else {
                            Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_13'], $plugin->getName(), $dependency));
                            return false;
                        }
                    }
                    self::$pluginDependencies[$dependency] = true;
                }
            }
        }
        if (isset($plugin->getData()->loadfirst)) {
            if (is_array($plugin->getData()->loadfirst)) {
                foreach ($plugin->getData()->loadfirst as $toLoadFirst) {
                    if ($toLoadFirst === $plugin->getName()) {
                        continue;
                    }
                    self::$loadedfirst[$toLoadFirst] = true;
                    $toLoadFirstAsPlugin = self::getPlugin($toLoadFirst);
                    if ($toLoadFirstAsPlugin) {
                        if (!isset(self::$pluginDependencies[$toLoadFirstAsPlugin->getName()]) && !in_array($toLoadFirstAsPlugin, self::getEnabledPlugins())) {
                            self::enablePlugin($toLoadFirstAsPlugin);
                        }
                    } else {
                        Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_14'], $plugin->getName(), $toLoadFirst));
                    }
                }
            }
        }
        if (isset(self::$enabledPlugins[$plugin->getName()])) {
            Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_5'], $plugin->getName()));
            return false;
        }
        Log::info(sprintf(Mono()->getLang()['log']['plugin_enable'], $plugin->getName(), $plugin->getVersion(), $plugin->getDescription()));
        unset(self::$disabledPlugins[$plugin->getName()]);
        self::$enabledPlugins[$plugin->getName()] = $plugin;
        $plugin->onEnable();
        if (count(Mono()->getControllerArguments())) {
            $plugin->onArgument(Input::method(), Mono()->getControllerArguments());
        }
        return true;
    }

    /**
     *
     * @return boolean
     * @param Plugin $plugin
     */
    public static function disablePlugin($plugin)
    {
        if (!isset(self::$enabledPlugins[$plugin->getName()])) {
            Log::warning(sprintf(Mono()->getLang()['errors']['plugin_error_6'], $plugin->getName()));
            return false;
        }
        Log::info(sprintf(Mono()->getLang()['log']['plugin_disable'], $plugin->getName(), $plugin->getVersion()));
        $plugin->onDisable();
        unset(self::$enabledPlugins[$plugin->getName()]);
        self::$disabledPlugins[$plugin->getName()] = $plugin;
        return true;
    }
}