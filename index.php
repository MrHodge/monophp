<?php

/*
|-----------------------------------------------
| DEBUG MODE
|-----------------------------------------------
| Set this option to 'true' to enable errors and
| the Mono log box. It is highly recommended to
| set to false for production environments.
|-----------------------------------------------
*/
define ("DEBUG", true);

/*
|-----------------------------------------------
| DEBUG TYPE
|-----------------------------------------------
| You can set this value to HTML or javascript
| HTML will display the logger in a box and
| javascript will log the console to the browser
| developer console.
|-----------------------------------------------
*/
define ("DEBUG_TYPE", "HTML");

/*
|-----------------------------------------------
| DEBUG AUTO OPEN
|-----------------------------------------------
| Set this option to true to automatically open
| the Mono log box no matter what LogTypes are in
| the log. By default the log box will open for
| warning and severe LogTypes.
|-----------------------------------------------
*/
define ("DEBUG_AUTO_OPEN", false);

/*
|-----------------------------------------------
| DEBUG OPAQUE
|-----------------------------------------------
| This value will handle the opacity of the
| log box. The value can be from 0 to 1.
| Decimal places are acceptable
|-----------------------------------------------
*/
define ("DEBUG_OPAQUE", 0.1);

/*
|-----------------------------------------------
| 404 ERROR
|-----------------------------------------------
| Set this option to true to enable
| Mono 404 page error when a controller or a view
| file was not found.
|-----------------------------------------------
*/
define ("404", true);

/*
|-----------------------------------------------
| ROOT PATH
|-----------------------------------------------
| This is the folder that contains this file
| "index.php".
|-----------------------------------------------
*/
define ("ROOT_PATH", realpath(__DIR__) . DIRECTORY_SEPARATOR);

/*
|-----------------------------------------------
| APP PATH
|-----------------------------------------------
| This is the folder that contains all of the
| application files. You can rename/move it if
| you are aware how to do so.
|-----------------------------------------------
*/
define ("APP_PATH", realpath(ROOT_PATH . "app") . DIRECTORY_SEPARATOR);

/*
|-----------------------------------------------
| PLUGINS PATH
|-----------------------------------------------
| This is the folder that contains all of the
| application plugins. You can rename/move it if
| you are aware how to do so.
|-----------------------------------------------
*/
define ("PLUGINS_PATH", realpath(ROOT_PATH . "plugins") . DIRECTORY_SEPARATOR);

/*
|-----------------------------------------------
| USE PLUGINS
|-----------------------------------------------
| This option allows you to enable/disable
| the use of plugins with this application.
| Set to true to enable plugins and set to
| false to disable them.
|-----------------------------------------------
*/
define ("USE_PLUGINS", true);

/*
|-----------------------------------------------
| MONO PATH
|-----------------------------------------------
| This is the folder that contains all of the
| VenomFramework files. You can rename/move it
| if you are aware how to do so.
|-----------------------------------------------
*/
define ("MONO_PATH", realpath(ROOT_PATH . "mono") . DIRECTORY_SEPARATOR);

/*
 * No need to change this.
 */
require_once MONO_PATH . '/autoload.php';