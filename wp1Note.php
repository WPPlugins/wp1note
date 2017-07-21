<?php

/*
  Plugin Name: wp1Note
  Version: 1.1
  Author: Robert Murdock
  Author URI: http://www.robmurdock.com/category/wordpress/
  Plugin URI: http://wordpress.org/extend/plugins/wp1Note/
 */

add_action('admin_menu', 'create_wp1Note_menu');
add_shortcode('display_wp1Note', 'reo_slider_listner');
// donate link on manage plugin page

add_filter('plugin_row_meta', 'wp1Note_donate_link', 10, 2);
function wp1Note_donate_link($links, $file) {
        if ($file == plugin_basename(__FILE__)) {
                $donate_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=robert%40darpac%2ecom">Donate</a>';
                $links[] = $donate_link;
        }
        return $links;
}
/**
 * One page menu actions 
 */
function create_wp1Note_menu() {
    add_menu_page('wp1Note Manager', 'wp1Note Manager', 'manage_options', 'page_manage', 'page_updater');    
}


function page_updater() {
    global $wpdb;
    include 'wp1Note_update.php';
}


/**
 * Setting for TinyMCE editor
 */
add_filter('admin_head', 'ShowTinyMCEForOnePage');

function ShowTinyMCEForOnePage() {
    // conditions here
    wp_enqueue_script('common');
    wp_enqueue_script('jquery-color');
    wp_print_scripts('editor');
    if (function_exists('add_thickbox'))
        add_thickbox();
    //wp_print_scripts('media-upload');
    if (function_exists('wp_tiny_mce'))
        wp_tiny_mce();
    
    wp_admin_css();
    //wp_enqueue_script('utils');
    do_action("admin_print_styles-post-php");
    do_action('admin_print_styles');
}

/**
 * Activate hook for create table during activation of the plugin 
 */

/**
 * Install table
 * @global type $wpdb
 */
function wp1Note_createtable() {
    global $wpdb;

    $table_name1 = $wpdb->prefix . "wp1Note";
    $sql1 = "CREATE TABLE $table_name1 (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `content_name` tinytext NOT NULL,
    `content_details` text NOT NULL,       
    UNIQUE KEY `id` (`id`)
    );";
    
    $insertsql = "INSERT INTO $table_name1 (`id`,`content_name`,`content_details`) VALUES('','Heading','Sample Contents');";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql1);    
    dbDelta($insertsql);
}

register_activation_hook(__FILE__, 'wp1Note_createtable');

/**
 * Function to delete table which created during install
 * @global type $wpdb 
 */
function wp1Note_uninstall() {
    global $wpdb;
    $table_name1 = $wpdb->prefix . "wp1Note";
    $wpdb->query("DROP TABLE IF EXISTS $table_name1");
}

register_deactivation_hook(__FILE__, 'wp1Note_uninstall');
