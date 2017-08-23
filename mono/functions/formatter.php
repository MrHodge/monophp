<?php

/**
 * @param $bytes
 * @param string $unit
 * @param int $decimals
 * @return string
 */
function formatBytes($bytes, $unit = "", $decimals = 2) {
    $units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4,
        'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);

    $value = 0;
    if ($bytes > 0) {
        if (!array_key_exists($unit, $units)) {
            $pow = floor(log($bytes)/log(1024));
            $unit = array_search($pow, $units);
        }

        $value = ($bytes/pow(1024, floor($units[$unit])));
    }

    if (!is_numeric($decimals) || $decimals < 0) {
        $decimals = 2;
    }

    return sprintf('%.' . $decimals . 'f '. $unit, $value);
}

/**
 * @param string $string
 * @param bool $strict
 * @return string
 *
 */
function capitalize($string, $strict = false) {
    if(is_string($string)) {
        $words = explode(" ", str_replace("&nbsp;", " ", $string));
        $string = "";
        foreach ($words as $word) {
            if($strict) {
                $secondPart = strtolower(substr($word, 1, strlen($word)));
            } else {
                $secondPart = substr($word, 1, strlen($word));
            }
            $string .= strtoupper(substr($word, 0, 1)) . $secondPart . " ";
        }
        return trim($string);
    }
    //Return whatever back because you can only capitalize strings
    return $string;
}

/**
 * @param string $string
 * @param bool $strict
 * @return string
 */
function capitalizeFirst($string, $strict = false) {
    if(is_string($string)) {
        $words = explode(" ", str_replace("&nbsp;", " ", $string));
        $string = "";
        $count = 0;
        foreach ($words as $word) {
            $count++;
            if($count == 1) {
                if($strict) {
                    $secondPart = strtolower(substr($word, 1, strlen($word)));
                } else {
                    $secondPart = substr($word, 1, strlen($word));
                }
                $string .= strtoupper(substr($word, 0, 1)) . $secondPart . " ";
            } else {
                $string .= $word . " ";
            }
        }
        return trim($string);
    }
    //Return whatever back because you can only capitalize strings
    return $string;
}

/**
 * @param string $string
 * @param bool $strict
 * @return string
 */
function capitalizeLast($string, $strict = false) {
    if(is_string($string)) {
        $words = explode(" ", str_replace("&nbsp;", " ", $string));
        $string = "";
        $count = 0;
        $wordCount = count($words);
        foreach ($words as $word) {
            $count++;
            if($wordCount == $count) {
                if($strict) {
                    $secondPart = strtolower(substr($word, 1, strlen($word)));
                } else {
                    $secondPart = substr($word, 1, strlen($word));
                }
                $string .= strtoupper(substr($word, 0, 1)) . $secondPart . " ";
            } else {
                $string .= $word . " ";
            }
        }
        return trim($string);
    }
    //Return whatever back because you can only capitalize strings
    return $string;
}

/**
 * @param string $string
 * @param bool $strict
 * @return string
 */
function formatSentences($string, $strict = false) {
    if(is_string($string)) {
        $string = strip_tags($string);
        $sentences = explode(".", str_replace("&nbsp;", " ", $string));
        $string = "";
        foreach ($sentences as $sentence) {
            $string .= capitalizeFirst($sentence, $strict) . " ";
        }
        $string = trim($string);
        if(!endsWith([".", "!", "?"], $string)){
            $string .= ".";
        }
        return $string;
    }
    //Return whatever back because you can only capitalize strings
    return $string;
}

/**
 * @param $string
 * @return mixed
 */
function singular($string) {
    if(is_string($string)) {
        return mono\libraries\Inflect::singularize($string);
    }
    return $string;
}

/**
 * @param $string
 * @return mixed
 */
function plural($string) {
    if(is_string($string)) {
        return mono\libraries\Inflect::pluralize($string);
    }
    return $string;
}

/**
 * @param $string
 * @return string
 */
function capitalise($string) {
    return capitalize($string);
}