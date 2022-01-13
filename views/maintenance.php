<?php

$get_logo_img = get_field( 'logo', 'option' );
$get_logo_url = get_field( 'image_url', 'option' );

if( $get_logo_url != '' ){
    $logo = $get_logo_url;
}else{
    $logo = $get_logo_img;
}

$styles = plugins_url( 'assets/aic-theme-options.css', dirname( __FILE__ ) );

?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo $styles; ?>">
        <title>Under Maintenance</title>
    </head>
    <body>
        <div class="maintenance-container">
            <div class="main-logo">
                <img class="maintenance-img" src="<?php echo $logo ?>">
            </div>
        </div>
    </body>
</html>