<?php

function format_GMT_offset($offset) {
    $hours = intval($offset / 3600);
    $minutes = abs(intval($offset % 3600 / 60));
    return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
}

function format_timezone_name($name) {
    $name = str_replace('/', ', ', $name);
    $name = str_replace('_', ' ', $name);
    $name = str_replace('St ', 'St. ', $name);
    return $name;
}

function timezoneList() {
    static $timezones = null;

    if ($timezones === null) {
        $timezones = [];
        $offsets = [];
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
            $offsets[] = $offset = $now->getOffset();
            $timezones[$timezone] = '(' . format_GMT_offset($offset) . ') ' . format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
    }

    return $timezones;
}

/**
 * @param string $date
 * @param string $timezone
 * @return DateTime
 */
function getDateTime($date = null, $timezone = null) {
    if($date == null) {
        $date = new \DateTime();
    } else {
        $date = new \DateTime($date);
    }
    if($timezone) {
        $date->setTimezone(new DateTimeZone($timezone));
    }
    return $date;
}

/**
 * @param string $time
 * @return DateInterval
 */
function getDateInterval($time) {
    return DateInterval::createFromDateString($time);
}

/**
 * @return string
 */
function getUTCDateString() {
    $date = getDateTime(null,"UTC");
    return $date->format(Mono()->getConfig()->getString("date_format"));
}

/**
 * @param string $date
 * @return DateTime
 */
function getUTCDate($date = null) {
    return getDateTime($date, "UTC");
}

/**
 * @param DateTime $start
 * @param DateTime $end
 * @return string
 */
function getDateDifferenceInSeconds($start, $end){
    $diff = $end->diff($start);
    $diff_sec = $diff->format('%r').(($diff->s) + (60 * $diff->i) + (3600 * $diff->h) + (86400 * $diff->days) + (2592000 * $diff->m) + (31536000 * $diff->y));
    return intval($diff_sec);
}