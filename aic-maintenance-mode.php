<?php

/*
 * Plugin Name: AIC Maintenance Mode
 * Plugin URI: https://anioncreative.com
 * Description: Displays a maintenance mode page for anyone who's not logged in.
 * Version: 1.0.3
 * Author: An Ion Creative
 * Author URI: https://anioncreative.com
 *
 * @package aic-maintenance-mode
*/

if( ! class_exists( 'Smashing_Updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}

$updater = new Smashing_Updater( __FILE__ );
$updater->set_username( 'ChristinaSW' );
$updater->set_repository( 'aic-maintenance-mode' );
$updater->initialize();

if ( in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // do stuff only if ACF is installed and active
    // Define path and URL to the ACF plugin.
    define( 'MY_ACF_PATH', plugin_dir_path(__DIR__) . 'advanced-custom-fields-pro/' );
    define( 'MY_ACF_URL', plugin_dir_path(__DIR__) . 'advanced-custom-fields-pro/' );
}else{
   // Define path and URL to the ACF plugin.
   define( 'MY_ACF_PATH', plugin_dir_path(__FILE__) . '/includes/acf/' );
   define( 'MY_ACF_URL', plugin_dir_path(__FILE__) . '/includes/acf/' );

}

// Include the ACF plugin.
    include_once( MY_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
    add_filter('acf/settings/url', 'my_acf_settings_url');
    function my_acf_settings_url( $url ) {
        return MY_ACF_URL;
    }

// (Optional) Hide the ACF admin menu item.
    add_filter('acf/settings/show_admin', 'my_acf_settings_show_admin');
    function my_acf_settings_show_admin( $show_admin ) {
        return true;
    }

    add_filter('acf/settings/save_json', 'my_acf_json_save_point');
 
function my_acf_json_save_point( $path ) {
    
    // update path
    $path = get_stylesheet_directory() . '/my-custom-folder';
    
    
    // return
    return $path;
    
}


?>
