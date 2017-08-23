<?php

namespace mono\classes;


use mono\constants\RouterType;
use mono\models\Route;

class Router {

    /**
     * @var Route[]
     */
    private static $routes = [];

    /**
     * @var array
     */
    private static $routeAliases = [];

    /**
     * @param string $routeString
     * @param callable $function
     * @param boolean $strict
     */
    public static function any($routeString, $function, $strict = true)
    {
        self::addRoute(RouterType::ANY, $routeString, $function, $strict);
    }

    /**
     * @param string $routeString
     * @param callable $function
     * @param boolean $strict
     */
    public static function get($routeString, $function, $strict = true)
    {
        self::addRoute(RouterType::GET, $routeString, $function, $strict);
    }

    /**
     * @param string $routeString
     * @param callable $function
     * @param boolean $strict
     */
    public static function post($routeString, $function, $strict = true)
    {
        self::addRoute(RouterType::POST, $routeString, $function, $strict);
    }

    /**
     * @param string $routeString
     * @param callable $function
     * @param boolean $strict
     */
    public static function put($routeString, $function, $strict = true)
    {
        self::addRoute(RouterType::PUT, $routeString, $function, $strict);
    }

    /**
     * @param string $routeString
     * @param callable $function
     * @param boolean $strict
     */
    public static function delete($routeString, $function, $strict = true)
    {
        self::addRoute(RouterType::DELETE, $routeString, $function, $strict);
    }

    /**
     * @param string $routeString
     * @param callable $function
     * @param boolean $strict
     */
    public static function update($routeString, $function, $strict = true)
    {
        self::addRoute(RouterType::UPDATE, $routeString, $function, $strict);
    }

    /**
     * This function will remove a route by the route string and add the same route again with the new route string.
     * @param $oldRouteString
     * @param $aliasRouteString
     */
    public static function alias($oldRouteString, $aliasRouteString)
    {
        Log::info("Adding alias route string \"{$aliasRouteString}\" for route string \"{$oldRouteString}\"");
        array_push(self::$routeAliases, [
            "alias" => $aliasRouteString,
            "route_string" => $oldRouteString,
        ]);
    }

    /**
     * This function will remove a route by the route string and add the same route again with the new route string.
     * IMPORTANT: This function has to be ran after the old route string was added.
     * @param $oldRouteString
     * @param $newRouteString
     * @param RouterType|callable|null $obj
     * @param RouterType|null $routerType
     * @param boolean $strict
     */
    public static function override($oldRouteString, $newRouteString, $obj = null, $routerType = null, $strict = true)
    {
        if(isset(self::$routes[$oldRouteString]))
        {
            Log::info("Overriding route string \"{$oldRouteString}\" with \"{$newRouteString}\"");
            if(is_callable($obj)) {
                $route = new Route($routerType, $newRouteString, $obj, $strict);
            } else {
                $route = self::$routes[$oldRouteString];
            }
            $route->setRouteString($newRouteString);
            if($obj instanceof RouterType) {
                $route->setRouterType($obj);
            }
            unset(self::$routes[$oldRouteString]);
            self::$routes[$newRouteString] = $route;
        }
        else {
            Log::warning("Route string \"{$oldRouteString}\" was not found, cannot override with \"{$newRouteString}\"");
        }
    }

    /**
     * @param $type
     * @param string $routeString
     * @param callable $function
     * @param boolean $strict
     * @internal
     */
    public static function addRoute($type, $routeString, $function, $strict)
    {
        if(!is_callable($function))
        {
            Log::severe("Please provided a valid function for route \"{$routeString}\". "  . getErrorAtLine(2));
        }
        if(is_array($routeString)){
            foreach ($routeString as $string) {
                $string = rtrim($string, '/') . '/';
                Log::info("Adding route \"{$string}\" with method: {$type}");
                self::$routes[$string] = new Route($type, $string, $function, $strict);
            }
        } else {
            $routeString = rtrim($routeString, '/') . '/';
            Log::info("Adding route \"{$routeString}\" with method: {$type}");
            self::$routes[$routeString] = new Route($type, $routeString, $function, $strict);
        }
    }

    /**
     * This function redirects a user to a provided route.
     *
     * @param string $route
     * @param boolean $ignoreBaseUrl
     */
    public static function to($route, $ignoreBaseUrl = false)
    {
        if($ignoreBaseUrl)
        {
            header("location: " . $route);
        }
        else {
            if(Mono()->getConfig()->getBoolean("clean_url"))
            {
                header("location: " . Mono()->getConfig()->getString("base_url") . $route);
            }
            else {
                header("location: " . Mono()->getConfig()->getString("base_url") . "/index.php?route=" . $route);
            }
        }
        exit();
    }

    /**
     * @param string $url
     * @return string
     */
    public static function completeRouteURL($url)
    {
        if(Mono()->getConfig()->getBoolean("clean_url"))
        {
            return Mono()->getConfig()->getString("base_url") . $url;
        }
        else {
            return Mono()->getConfig()->getString("base_url") . "/index.php?route=" . $url;
        }
    }

    /**
     * @param string $url
     * @return string
     */
    public static function completeURL($url)
    {
        return Mono()->getConfig()->getString("base_url") . $url;
    }

    /**
     * @return Route[]
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * @param Route[] $routes
     */
    public static function setRoutes($routes)
    {
        self::$routes = $routes;
    }

    /**
     * @return array
     */
    public static function getRouteAliases()
    {
        return self::$routeAliases;
    }

    /**
     * @param array $routeAliases
     */
    public static function setRouteAliases($routeAliases)
    {
        self::$routeAliases = $routeAliases;
    }

    /**
     * This function will be handled by Mono, if ran more than once, route initialisation will be duplicated
     * If the route string matched the route string within a route return true else return false
     * @return boolean
     * @internal
     */
    public static function initRouters()
    {
        Log::info("Initializing routes");
        foreach (self::getRoutes() as $route)
        {
            if($route->init())
            {
                return true;
            }
        }

        Log::info("Initializing routes by alias");
        foreach (self::getRouteAliases() as $routeAlias)
        {
            if(isset(self::getRoutes()[$routeAlias["route_string"]]))
            {
                $route = self::getRoutes()[$routeAlias["route_string"]];
                $route->setRouteString($routeAlias["alias"]);
                if($route->init())
                {
                    return true;
                }
            }
        }
    }

    /**
     * This function returns the current route that the user is on.
     *
     * @param boolean $sanitized
     * @return string[]
     */
    public static function URLArray($sanitized = true)
    {
        if(isset($_GET['route']))
        {
            return self::URLToArray($_GET['route'], $sanitized);
        }
        elseif (isset($_SERVER['PATH_INFO']))
        {
            return self::URLToArray($_SERVER['PATH_INFO'], $sanitized);
        }
        elseif(isset($_SERVER['ORIG_PATH_INFO']))
        {
            return self::URLToArray($_SERVER['ORIG_PATH_INFO'], $sanitized);
        }
        else {
            return [];
        }
    }

    /**
     * This function returns the current route that the user is on.
     *
     * @return string[]
     */
    public static function rawURLArray()
    {
        return self::URLArray(false);
    }

    /**
     * @param string $url
     * @param boolean $sanitized
     * @return array
     */
    public static function URLToArray($url, $sanitized = true) {
        if($sanitized)
        {
            return explode("/", filter_var(sanitize(rtrim(preg_replace('/\//', '', $url, 1), "/")),FILTER_SANITIZE_URL));
        }
        else {
            return explode("/", rtrim(preg_replace('/\//', '', $url, 1), "/"));
        }
    }

    /**
     * @return Config
     */
    public static function config() {
        return Mono()->getConfig("route");
    }

}