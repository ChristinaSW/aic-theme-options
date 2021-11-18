<?php

$logo = get_field( 'logo', 'option' );
$styles = plugins_url( 'assets/style.css', dirname( __FILE__ ) );

?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo $styles; ?>">
        <title>Under Maintenance</title>
    </head>
    <body>
        <div class="container">
            <div class="main-logo">
                <img src="<?php echo $logo ?>">
            </div>
        </div>
    </body>
</html>