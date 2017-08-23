<?php

use mono\classes\Router;
use mono\classes\Render;

Router::get("/", function() {
    Render::render("home");
});
