<?php 

$get_colors = get_field( 'theme_colors', 'option');

foreach( $get_colors as $color ){
    $color_name = strtolower($color['color_name']);
    $color_name = str_replace(' ','-', $color_name);
    $color_hex = $color['color_hex'];

    ?>
    .has-<?php echo $color_name; ?>-color {
        color: <?php echo $color_hex; ?>;
    }
    .has-<?php echo $color_name; ?>-background-color {
        background-color: <?php echo $color_hex; ?>;
    }
    .has-inline-color.has-<?php echo $color_name; ?>-color {
        color: <?php echo $color_hex; ?>;
    }
<?php

}

?>
