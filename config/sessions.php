<?php


return [
    /*
    |-----------------------------------------------
    | 'sessions'
    |-----------------------------------------------
    | set this option to 'true' to enable PHP
    | sessions. This is required for very useful
    | features of Mono, such as the  Notifier and the
    | storage of CSRF Tokens. This may also be needed
    | for applications where you want to store user
    | data temporarily.
    |-----------------------------------------------
    */
    'enabled' => true,

    /*
    |-----------------------------------------------
    | 'session-names'
    |-----------------------------------------------
    | Please enter random characters for each session
    | name below.
    |-----------------------------------------------
    */
    'session-names' => [
        'csrfToken' => "insert-random-characters-here",
    ]
];