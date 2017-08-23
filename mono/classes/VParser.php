<?php

namespace mono\classes;


class VParser
{
    /**
     * @var bool
     */
    private static $cache;

    /**
     * @var array
     */
    private static $variables;

    /**
     * VParser constructor.
     * @param bool $cache
     * @param array $variables
     * @return VParser
     */
    public static function init($cache = false, $variables = [])
    {
        self::setCache($cache);
        self::setVariables($variables);
        return new self();
    }

    /**
     * @param $content
     * @return string
     */
    public static function parse($content) {
        $content = self::replaceVariables($content);
        $content = self::handleFunctions($content);
        return $content;
    }

    public static function replaceVariables($content) {

        return $content;
    }

    public static function handleFunctions($content) {

        return $content;
    }

    /**
     * @return bool
     */
    public static function isCache()
    {
        return self::$cache;
    }

    /**
     * @param bool $cache
     */
    public static function setCache($cache)
    {
        self::$cache = $cache;
    }

    /**
     * @return array
     */
    public static function getVariables()
    {
        return self::$variables;
    }

    /**
     * @param array $variables
     */
    public static function setVariables($variables)
    {
        self::$variables = $variables;
    }
}