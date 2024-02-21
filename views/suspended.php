<?php

$styles = plugins_url( 'assets/aic-theme-options.css', dirname( __FILE__ ) );

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Account Suspended</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                background-color: #000;
                font-size: 16px;
                padding-top: 5%;
            }
            .suspended-container {
                text-align: center;
            }
            img {
                max-width: 300px;
            }
            h1 {
                color: #C70000;
                font-size: 4em;
            }
            h2 {
                color: #fff;
                font-size: 2em;
            }
        </style>
    </head>
    <body>
        <div class="suspended-container">
            <img src="https://maintence.s3.us-east-2.amazonaws.com/stop-graphic.png" />
            <h1>ACCOUNT<br />SUSPENDED</h1>
            <h2>Please contact your hosting provider for assistance.</h2>
        </div>
    </body>
</html>