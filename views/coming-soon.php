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
<html class="coming-soon-page">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo $styles; ?>">
        <title>Coming Soon</title>
    </head>
    <body>
        <div class="coming-soon-container">
            <div class="main-logo">
                <img class="coming-soon-img" src="<?php echo $logo ?>">
            </div>
        </div>
    </body>
</html>