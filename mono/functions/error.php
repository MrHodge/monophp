<?php

function getErrorAtLine($depth = 1) {
    $problemFile = null;
    if(isset(debug_backtrace()[$depth])){
        $problemFile = debug_backtrace()[$depth];
    }
    $line = null;
    if($problemFile) {
        $line = "At line " . $problemFile["line"] . " in " . $problemFile["file"];
    }
    return $line;
}