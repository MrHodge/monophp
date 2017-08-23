<?php

namespace mono\classes;

class Notifier
{


    /**
     * This function allows you to set a notification which can be then called in a controller view
     * using the notification array.
     *
     * @param string $type
     *            [success, error, critical or anything that you'd like to use in your application.]
     * @param string $message
     * @param boolean $dismissible
     *            [optional]
     * @param int $time
     *            [optional]
     * @param boolean $sound
     *            [optional]
     * @param boolean $full
     *            [optional]
     */
    public static function setNotification($type, $message, $dismissible = true, $time = 0, $sound = false, $full = true)
    {
        Session::put('vfw_notification', [
            'type' => $type,
            'message' => $message,
            'dismissible' => $dismissible,
            'sound' => $sound,
            'time' => $time,
            'full' => $full,
        ]);
    }

    /**
     * This function shows the notification that was set before. Once returned, the old notification will be removed.
     *
     * @return mixed
     */
    public static function getNotification()
    {
        if (Session::exists('vfw_notification')) {
            $notification = Session::get('vfw_notification');
            Session::delete('vfw_notification');
            return $notification;
        } else {
            return null;
        }
    }
}