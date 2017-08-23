<?php

defined('ROOT_PATH') OR exit('No direct script access is allowed.');

return [

    /*
    |-----------------------------------------------
    | 'name'
    |-----------------------------------------------
    | This is the name of the application. Plugins
    | will be able to grab this information using a
    | function within Mono.
    |-----------------------------------------------
    */
    'name' => "Application",

    /*
    |-----------------------------------------------
    | 'version'
    |-----------------------------------------------
    | This is the version of the application.
    | Setting  a version is very important as some
    | plugins may require functions from an earlier
    | version of this application.
    |-----------------------------------------------
    */
    'version' => "1",

    /*
    |-----------------------------------------------
    | 'author'
    |-----------------------------------------------
    | This is who created the application.
    | Plugins will be able to grab this information
    | using a function within Mono.
    |-----------------------------------------------
    */
    'author' => "",

    /*
    |-----------------------------------------------
    | 'language'
    |-----------------------------------------------
    | this is the language that Mono use for errors
    | and logging. This option is set to English by
    | default.
    | Supported languages:
    | - English
    | - Spanish
    | - French - coming soon
    | - Dutch - coming soon
    |-----------------------------------------------
    */
    'language' => "english",

    /*
    |-----------------------------------------------
    | 'default_controller'
    |-----------------------------------------------
    | This is the controller Mono will load as the
    | default controller. If a controller was not
    | found it will always fall back to this
    | controller.
    |
    | name - The name of the controller file AND
    |        class
    | method - The function which will execute for
    |          the controller
    |-----------------------------------------------
    */
    'default_controller' => [
        "name" => "home",
        "method" => "index",
    ],

    /*
    |-----------------------------------------------
    | 'template'
    |-----------------------------------------------
    | This is the template which Mono will use to
    | display content to the client. This can also be
    | defined using the setTemplate function if it
    | needs to be changed on a user interface.
    |-----------------------------------------------
    */
    'template' => 'default',

    /*
    |-----------------------------------------------
    | 'template_extension'
    |-----------------------------------------------
    | This is the file extension for the application
    | template files. Set this to what is preffered.
    |-----------------------------------------------
    */
    'template_extension' => ".php",

    /*
    |-----------------------------------------------
    | 'base_url'
    |-----------------------------------------------
    | The base url is the main url of your project.
    | For example "http://example.com" WITHOUT a
    | trailing slash.
    |-----------------------------------------------
    */
    "base_url" => "http://monophp.dev",
	
	/*
    |-----------------------------------------------
    | 'clean_url'
    |-----------------------------------------------
    | This will let the application know if a clean
    | url should be used. This requires .htaccess 
	| to be working, or for NGINX, proper routing.
	| Please refer to the documentation on how to
	| setup routing for nginx.
    |-----------------------------------------------
    */
    "clean_url" => false,
	
    /*
    |-----------------------------------------------
    | 'date_format'
    |-----------------------------------------------
    | A global configured date format which will
    | be used for the getDate() functions provided by
    | VNOX Framework.
    |-----------------------------------------------
    */
    "date_format" => "Y-m-d H:i:sP",
];