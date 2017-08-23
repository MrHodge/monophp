<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>404 Page Not Found</title>
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
        </style>
    </head>
    <body>
        <div class="center">
            <h1>Error 404</h1>
            <p>Page Not Found - VNOX Framework</p>
            <?php
                if(DEBUG) {
                    ?>
                    <p>
                        <small>Version <?php echo Mono()->version(); ?></small>
                    </p>
                    <?php
                }
            ?>
        </div>
    </body>
<html>