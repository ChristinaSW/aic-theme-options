<?php

/*
 * Plugin Name: AIC Theme Options
 * Plugin URI: https://anioncreative.com
 * Description: Adds user options to AIC theme.
 * Version: 10.0
 * Author: An Ion Creative
 * Author URI: https://anioncreative.com
 *
 * @package aic-theme-options
*/

// Add updater so we can update plugin from github

        if( ! class_exists( 'Smashing_Updater' ) ){
        include_once( plugin_dir_path( __FILE__ ) . 'lib/updater.php' );
    }

    $updater = new Smashing_Updater( __FILE__);
    $updater->set_username( 'ChristinaSW' );
    $updater->set_repository( 'aic-theme-options' );
    $updater->initialize();

// Check if ACF plugin is installed and add a notice if it is not

    function general_admin_notice(){
        global $pagenow;
        if ( in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            // Define path and URL to the ACF plugin.
                define( 'MY_ACF_PATH', plugin_dir_path(__DIR__) . 'advanced-custom-fields-pro/' );
                define( 'MY_ACF_URL', plugin_dir_path(__DIR__) . 'advanced-custom-fields-pro/' );
            // Include the ACF plugin.
                include_once( MY_ACF_PATH . 'acf.php' );
        }else{
            echo '
                <div class="notice notice-error is-dismissible">
                    <p>AIC Theme Options will not work without Advanced Custom Fields Pro. Please install and/or activate the ACF Pro plugin A.S.A.P.</p>
                </div>
            ';
        }
    }
    add_action('admin_notices', 'general_admin_notice');

// Add our custom fields
    include_once( plugin_dir_path(__FILE__) . 'lib/aic-custom-fields.php' );
    
// Create the option page in the admin

    add_action('acf/init', 'aic_option_pages');
    function aic_option_pages(){
        // Check function exists
        if( function_exists('acf_add_options_page') ) {

            // Add parent
            $parent = acf_add_options_page(array(
                'page_title' 	=> 'Options',
				'menu_title'	=> 'Options',
				'menu_slug' 	=> 'aic-options',
				'position' 		=> '6',
                'autoload'      => TRUE,
                'redirect'      => TRUE,
                'capability'    => 'edit_theme_options',
                'icon_url'      => 'dashicons-art',
				'update_button' => __('Save Settings', 'acf'),
				'updated_message' => __("Settings Saved", 'acf')
            ));

            // Add sub pages
            $child = acf_add_options_page(array(
                'page_title'  => __('Theme Options'),
                'menu_title'  => __('Theme Options'),
                'menu_slug' 	=> 'aic-theme-options',
                'parent_slug' => $parent['menu_slug'],
                'update_button' => __('Save Settings', 'acf'),
                'updated_message' => __("Settings Saved", 'acf')
            ));

            $child = acf_add_options_page(array(
                'page_title'  => __('Site Options'),
                'menu_title'  => __('Site Options'),
                'parent_slug' => $parent['menu_slug'],
                'update_button' => __('Save Settings', 'acf'),
                'updated_message' => __("Settings Saved", 'acf')
            ));
        }
    }

// Add Support Ticket Form

    add_filter( 'wp_kses_allowed_html', 'acf_add_allowed_iframe_tag', 10, 2 );
    function acf_add_allowed_iframe_tag( $tags, $context ) {
        if ( $context === 'acf' ) {
            $tags['iframe'] = array(
                'src'             => true,
                'height'          => true,
                'width'           => true,
                'frameborder'     => true,
                'allowfullscreen' => true,
            );
        }

        return $tags;
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
        if(!current_user_can('administrator')){
           
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

// Function to run when a site is suspended

    function aic_suspension_mode(){
        $ip = get_field('ip_address', 'option');
        if($_SERVER['REMOTE_ADDR'] != $ip){
        
            if ( file_exists( plugin_dir_path( __FILE__ ) . 'views/suspended.php' ) ) {
                require_once( plugin_dir_path( __FILE__ ) . 'views/suspended.php' );
            }
            die();
        }
    }

    add_action( 'acf/init', 'aic_suspension_check' );
    function aic_suspension_check(){
        $status = get_field( 'suspend_site', 'option');

        if( $status != FALSE ){
            add_action('get_header', 'aic_suspension_mode');
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

	if( $get_colors != '' ){
                foreach( $get_colors as $color ){
                        $get_hex = str_replace('#', '', $color['color_hex']);
                        $c_name = $color['color_name'];
                        $custom_colors .= '"'.$get_hex.'", "'.$c_name.'",';
                }
        }

        $custom_colors .= '
            "000000", "Black",
            "FFFFFF", "White",
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

// Add color classes so devlopers can use them.

    function add_color_class($post){

        if( have_rows('theme_colors', 'option') ){
            while( have_rows('theme_colors', 'option') ){
                the_row();
                $color_name = strtolower( get_sub_field('color_name') );
                $color_name = str_replace(' ','-', $color_name);
                $color_hex = get_sub_field('color_hex');
                $color_classes = 'Hex: '.$color_hex.'
Text Color: has-'.$color_name.'-color
Background Color: has-'.$color_name.'-background-color';
                
                update_sub_field( 'field_6213605f5be87', $color_classes, 'option' );
            }
        }
    }

    add_action('acf/save_post', 'add_color_class', 20);


// Hide if not AIC developer

    function dev_only(){
        $current_user = wp_get_current_user();
        $current_user = $current_user->user_login;
        $choices = get_field('tabs_visibility', 'option');
        $hide_tab = '';
        if( $choices != '' ){
            foreach( $choices as $choice ){
                $hide_tab .= '.acf-tab-button[data-key="'.$choice.'"],';
            }
        }
        $chosen_fields = get_field('other_options_visibility', 'option');
        $filters = '';
        // echo '<pre>';print_r($chosen_fields);echo '</pre>';
        if( $chosen_fields != '' ){
            foreach( $chosen_fields as $chosen_field ){
                $filters .= add_filter('acf/load_field/key='.$chosen_field['field_key'].'', 'aic_hide');
            }
            echo $filters;
        }

        if( $current_user != 'super' ){

            echo('
                <style type="text/css">
                    .acf-tab-button[data-key="field_6284c7d0bd89e"],
                    .acf-tab-button[data-key="field_62dff6febee11"],
                    '.$hide_tab.'
                    .acf-field.dev-only,
                    .dev-only {
                        display: none;
                    }
                </style>
            ');
        }
    }
    add_action('admin_head','dev_only');

// Function adding the dev-only class

    function aic_hide($field){
            
        $field['wrapper']['class'] = 'dev-only';
        return $field;  
    }

// Debugging Option

        function debug_var($var){
            $debug_switch = get_field('enable_debug', 'option');
            $ip = get_field('ip_address', 'option');

            if($debug_switch != false && $_SERVER['REMOTE_ADDR'] == $ip){
                error_reporting( E_ALL );
                ini_set( 'display_errors', 1 );			
                echo('<pre>');
                    print_r($var);
                echo('</pre>');
            }
        }

// Strip Banner

    add_action('genesis_before_header', 'theme_banner');
    function theme_banner() {
        $active = get_field('banner_active', 'option');

        if( $active != FALSE ){
            $background_color = get_field('background_color', 'option');
            $background = ( $background_color != '' )?'style="background-color: '.$background_color.'"':'';
            $get_text = get_field('text', 'option');
            $text = ( $get_text != '' )?$get_text:'';
            $banner = '
                <div class="theme-banner"'.$background.'>
                    '.$text.'
                </div>
            ';    
        }else{
            $banner = '';
        }

        echo $banner;
    }

// Disable Gutenberg Editor

    function aic_disable_editor( $id = false ) {

        $block_disable_list = get_field('disable_editor_list', 'options');

        if( $block_disable_list == '' ){
            return;
        }

        $list = array();

        foreach( $block_disable_list as $p_id ){
            $list[] .= $p_id->ID;
        }

        $excluded_ids = $list;

        return $excluded_ids;
    }

//     function aic_disable_gutenberg( $can_edit, $post_type ) {

//         if( ! ( is_admin() && !empty( $_GET['post'] ) ) )
//             return $can_edit;

//         if( aic_disable_editor( $_GET['post'] ) )
//             $can_edit = false;

//         return $can_edit;

    }
//     add_filter( 'gutenberg_can_edit_post_type', 'aic_disable_gutenberg', 10, 2 );
//     add_filter( 'use_block_editor_for_post_type', 'aic_disable_gutenberg', 10, 2 );

    function aic_disable_gutenberg( $use_block_editor, $post ) {

        $excluded_ids = get_field('disable_editor_list', 'option');
        if ( in_array( $post->ID, $excluded_ids ) ) {
            return false;
        }
        return $use_block_editor;
    }
    add_filter( 'use_block_editor_for_post', 'aic_disable_gutenberg', 10, 2 );

?>
