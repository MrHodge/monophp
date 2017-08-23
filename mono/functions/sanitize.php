<?php

/**
 *
 * @param string $string
 * @return string
 */
function sanitize($string = "")
{
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

/**
 * @param string $string
 * @return string
 */
function desanitize($string = "")
{
    return html_entity_decode($string);
}