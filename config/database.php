<?php

defined('ROOT_PATH') OR exit('No direct script access is allowed.');

return [
    'default' => [
        'type' => "mysql.pdo",
        'host' => "127.0.0.1",
        'port' => 3306,
        'database' => "monophp",
        'table_prefix' => 'mphp_',
        'username' => "root",
        'password' => ""
    ]
];