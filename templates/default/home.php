<?php

//Import classes
use mono\classes\Variables;

?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>MonoPHP - Welcome</title>
        <style>
            html {
                font-family: sans-serif, Arial;
            }
            body {
                position: relative;
                background: #f4f4f4;
                color: #1e1e1e;
            }
            .center {
                text-align: center;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            a {
                padding: 2px;
                text-decoration: none;
                color: #1e1e1e;
            }
            a:hover, a:focus {
                text-decoration: none;
            }
            a:hover {
                padding: 1px; /* set padding to 3 px to compensate for border and remove graphical glitch */
                border: 1px solid #2b2b2b;
                color: #2b2b2b;
            }
        </style>
    </head>
    <body>
        <div class="center">
            <h1>MonoPHP</h1>
            <p><a href="https://monophp.com/docs/">Documentation</a> &bull; <a href="https://monophp.com/">Official Website</a> &bull; <a href="https://monophp.com/community/">Community</a> &bull; <a href="https://monophp.com/license/">License</a></p>
            <?php
            if(DEBUG) {
                ?>
                    <small>Version <?php echo Variables::get("version"); ?></small>
                <?php
            }
            ?>
        </div>
    </body>
</html>