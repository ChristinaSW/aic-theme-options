<?php

/*
 * Plugin Name: AIC Theme Options
 * Plugin URI: https://anioncreative.com
 * Description: Adds user options to AIC theme.
 * Version: 4.2.2
 * Author: An Ion Creative
 * Author URI: https://anioncreative.com
 *
 * @package aic-theme-options
*/

// Add updater so we can update plugin from github

    if( ! class_exists( 'Smashing_Updater' ) ){
        include_once( plugin_dir_path( __FILE__ ) . 'lib/updater.php' );
    }

    $updater = new Smashing_Updater( __FILE__ );
    $updater->set_username( 'ChristinaSW' );
    $updater->set_repository( 'aic-theme-options' );
    $updater->initialize();

    

// Check if ACF plugin is already installed and use it instead

    if ( in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        // do stuff only if ACF is installed and active
        // Define path and URL to the ACF plugin.
        define( 'MY_ACF_PATH', plugin_dir_path(__DIR__) . 'advanced-custom-fields-pro/' );
        define( 'MY_ACF_URL', plugin_dir_path(__DIR__) . 'advanced-custom-fields-pro/' );
    }else{
        // Define path and URL to the ACF plugin.
        define( 'MY_ACF_PATH', plugin_dir_path(__FILE__) . 'includes/acf/' );
        define( 'MY_ACF_URL', plugin_dir_path(__FILE__) . 'includes/acf/' );

        // Hide ACF admin menu if it is not natively installed
        add_filter('acf/settings/show_admin', 'my_acf_settings_show_admin');
        function my_acf_settings_show_admin( $show_admin ) {
            return false;
        }
    }

// Include the ACF plugin.
    include_once( MY_ACF_PATH . 'acf.php' );

// Add our custom fields
    include_once( plugin_dir_path(__FILE__) . 'lib/aic-custom-fields.php' );
    
// Create the option page in the admin
    add_action( 'acf/init', 'aic_option_page' );
	function aic_option_page(){
		acf_add_options_page( array(
				'page_title' 	=> 'Theme Options',
				'menu_title'	=> 'Theme Options',
				'menu_slug' 	=> 'aic-theme-options',
				'position' 		=> '6',
                'autoload'      => TRUE,
                'capability'    => 'edit_theme_options',
                'icon_url'      => 'dashicons-art',
				'update_button' => __('Save Settings', 'acf'),
				'updated_message' => __("Settings Saved", 'acf'),
			)
		);
		
	}
    
// Register JQuery
    add_action( 'acf/input/admin_enqueue_scripts', 'aic_theme_options_java', 11 );
    function aic_theme_options_java(){
        wp_register_script( 
            'acf-collapse-fields-admin-js',
            esc_url( plugins_url( 'lib/acf-collapse-fields-admin.js', __FILE__ ) ),
            array( 'jquery' ),
        );

    // Localize the script with new data
        $translation_array = array(
            'expandAll'			=> __( 'Expand All Elements', 'acf-collapse-fields' ),
            'collapseAll'		=> __( 'Collapse All Elements', 'acf-collapse-fields' )
        );
        
        wp_localize_script( 'acf-collapse-fields-admin-js', 'collapsetranslation', $translation_array );
        wp_enqueue_script('acf-collapse-fields-admin-js');
    }

// Add admin styling

    add_action( 'admin_enqueue_scripts', 'aic_emm_admin_styles' );
    function aic_emm_admin_styles(){
        wp_enqueue_style( 'admin-styles', plugin_dir_url(__FILE__) . '/assets/aic-theme-options-admin-styles.css');
    }

// Add front end styling
    add_action( 'wp_enqueue_scripts', 'aic_theme_options_styles' );
    function aic_theme_options_styles(){
        wp_enqueue_style('aic-theme-option-styles', plugin_dir_url( __FILE__ ) . 'assets/aic-theme-options.css' );
        wp_enqueue_style('dynamic-aic-theme-option-styles', plugin_dir_url( __FILE__ ) . 'assets/dynamic-aic-theme-options.css' );
    }
   
// Add dynamic styles from ACF options

    add_action('parse_request', 'parse_dynamic_css_request');
    function parse_dynamic_css_request($wp) {
        $get_colors = get_field( 'theme_colors', 'option' );
        if( $get_colors != '' ){
            $ss_dir = plugin_dir_path( __FILE__ ); // Shorten code, save 1 call
            ob_start(); // Capture all output (output buffering)
            require($ss_dir . 'assets/dynamic-aic-theme-options.css.php'); // Generate CSS
            $css = ob_get_clean(); // Get generated CSS (output buffering)
            file_put_contents($ss_dir . 'assets/dynamic-aic-theme-options.css', $css, LOCK_EX); // Save it    
        }
    }

// Function to run when maintenance mode is switched on
    function aic_maintenance_mode(){
        if(!current_user_can('edit_themes') || !is_user_logged_in()){
           
            if ( file_exists( plugin_dir_path( __FILE__ ) . 'views/maintenance.php' ) ) {
                require_once( plugin_dir_path( __FILE__ ) . 'views/maintenance.php' );
            }
            die();
        }
    }
    
    add_action( 'acf/init', 'aic_status_check' );
    function aic_status_check(){
        $status = get_field( 'enable_maintenance_mode', 'option');

        if( $status != FALSE ){
            add_action('get_header', 'aic_maintenance_mode');
        }
    }
    
// Theme Colors

	// Custom colors for editor
        add_action( 'acf/init', 'aic_editor_colors' );
        function aic_editor_colors(){
            $get_colors = get_field( 'theme_colors', 'option' );
            $colors = ( $get_colors != '' )?$get_colors:'';
            $color_array = array();
            if( $colors != '' ){
                foreach( $colors as $color ){
                    $custom_colors = array(
                        'name' => __( $color['color_name'], 'genesis-sample' ),
                        'slug' => strtolower( $color['color_name'] ),
                        'color' => $color['color_hex']
                    );
                    $color_array[] = $custom_colors;
                }
                
                array_push( $color_array, array(
                    'name' => 'White',
                    'slug' => 'white',
                    'color' => '#ffffff'
                ),array(
                    'name' => 'Black',
                    'slug' => 'black',
                    'color' => '#000000'
                ));

                add_theme_support( 'editor-color-palette', $color_array );    
            }
        }

    // Add theme colors to ACF WYSIWYG

    function aic_theme_wysiwyg_colors($init) {

        $custom_colors = '';
        $get_colors = get_field( 'theme_colors', 'option');

        foreach( $get_colors as $color ){
            $get_hex = str_replace('#', '', $color['color_hex']);
            $c_name = $color['color_name'];
            $custom_colors .= '"'.$get_hex.'", "'.$c_name.'",';
        }

        $custom_colors .= '
            "000000", "Black",
            "FFFFFF", "White",
            "808080", "Gray"
        ';

        // build colour grid default+custom colors
        $init['textcolor_map'] = '['.$custom_colors.']';

        // change the number of rows in the grid if the number of colors changes
        // 8 swatches per row
        $init['textcolor_rows'] = 2;

        return $init;
    }
    add_filter('tiny_mce_before_init', 'aic_theme_wysiwyg_colors');

    // Add theme colors to ACF color picker

    function aic_colorpicker_colors() { 
        
        $get_colors = get_field( 'theme_colors', 'option');

        if($get_colors != '' ){
            $colors = '';

            foreach( $get_colors as $color ){
                $colors .= "'".$color['color_hex']."', ";
            }
            ?>
            <script type="text/javascript">
                (function($){
            
                    acf.add_filter('color_picker_args', function( args, $field ){
            
                        args.palettes = [<?php echo $colors ?> '#ffffff', '#000000']
                        return args;
                    });
            
                })(jQuery);
            </script>
            <?php
    
        }

    }
    add_action('acf/input/admin_footer', 'aic_colorpicker_colors');

?>
