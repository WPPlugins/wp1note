<?php

/*
  Plugin Name: wp1Note
  Version: 1.1
  Description: This plugin creates a 1 page notepad for an admin (or other roles). It has multi-language support. 
 
  Author: Robert Murdock
  Author URI: http://www.robmurdock.com/category/wordpress/
  Plugin URI: http://wordpress.org/extend/plugins/wp1Note/
*/

add_action('admin_menu', 'create_wp1Note_admin_menu');
add_shortcode('display_wp1Note', 'reo_slider_listner');
add_filter('plugin_row_meta', 'wp1Note_donate_link', 10, 2);

function wp1Note_donate_link($links, $file) {
        if ($file == plugin_basename(__FILE__)) {
                $donate_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=robert%40digitaledge%2ecom">Donate</a>';
                $links[] = $donate_link;
        }
        return $links;
}

/**
 * wp1Note menu actions 
 */
function create_wp1Note_admin_menu() {
	if(return_previlages()!=2){
    	add_menu_page('wp1Note Manager', 'wp1Note Manager', 'read', 'wp1Note_manage', 'wp1Note_updater'); 	
	}
	add_submenu_page('wp1Note_manage', 'Settings', 'Settings', 'manage_options', 'wp1Note_setting', 'wp1Note_config');   
}


function wp1Note_updater() {
    global $wpdb;
    include 'wp1Note_update.php';
}
function wp1Note_config() {
    global $wpdb;
    include 'wp1Note_setting.php';
}


/**
 * Setting for TinyMCE editor
 */
add_filter('admin_head', 'ShowTinyMCEForOnePageSuper');

function ShowTinyMCEForwp1Note() {
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
    $table_name1 = $wpdb->prefix . "options";       
    $insertsql1 = "INSERT INTO $table_name1 (`option_id`,`option_name`,`option_value`) VALUES('','wp1Note_contents','Sample Contents');";
	$insertsql2 = "INSERT INTO $table_name1 (`option_id`,`option_name`,`option_value`) VALUES('','wp1Note_language','english');";
	$insertsql3 = "INSERT INTO $table_name1 (`option_id`,`option_name`,`option_value`) VALUES('','wp1Note_settings','1,2,2,2,2');";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );  
    dbDelta($insertsql1);
	dbDelta($insertsql2);
	dbDelta($insertsql3);
}

register_activation_hook(__FILE__, 'wp1Note_createtable');

/**
 * Function to delete table which created during install
 * @global type $wpdb 
 */
function wp1Note_uninstall() {
    global $wpdb;
    $table_name1 = $wpdb->prefix . "options";
    $wpdb->query("DELETE FROM $table_name1 where `option_name` = 'wp1Note_contents'");
	$wpdb->query("DELETE FROM $table_name1 where `option_name` = 'wp1Note_language'");
	$wpdb->query("DELETE FROM $table_name1 where `option_name` = 'wp1Note_settings'");
}

register_deactivation_hook(__FILE__, 'wp1Note_uninstall');



function return_previlages(){
	global $wp_roles;
	global $wpdb;
	$current_user = wp_get_current_user();
	$roles = $current_user->roles;
	$role = array_shift($roles);
	$userRole = isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
	
	
	$showSave = 1;
	
	$settings           = "";
	$details = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE `option_name` = 'wp1Note_settings'");
	if (count($details) > 0) {            
		$settings = $details[0]->option_value;
	}
	$settingsArray = explode(',',$settings);
	$administrator_visibility   = $settingsArray[0];
	$subscriber_visibility      = $settingsArray[1];
	$editor_visibility          = $settingsArray[2];
	$author_visibility          = $settingsArray[3];
	$contributor_visibility     = $settingsArray[4];
	
	if($administrator_visibility == 1 && $userRole=='Administrator'){
		$showSave = 1;
	}else if($subscriber_visibility == 1 && $userRole=='Subscriber'){
		$showSave = 1;
	}else if($editor_visibility == 1 && $userRole=='Editor'){
		$showSave = 1;
	}else if($author_visibility == 1 && $userRole=='Author'){
		$showSave = 1;
	}else if($contributor_visibility == 1 && $userRole=='Contributor'){
		$showSave = 1;
	}else if($administrator_visibility == 2 && $userRole=='Administrator'){
		$showSave = 0;
	}else if($subscriber_visibility == 2 && $userRole=='Subscriber'){
		$showSave = 2;
	}else if($editor_visibility == 2 && $userRole=='Editor'){
		$showSave = 2;
	}else if($author_visibility == 2 && $userRole=='Author'){
		$showSave = 2;
	}else if($contributor_visibility == 2 && $userRole=='Contributor'){
		$showSave = 2;
	}else{
		$showSave = 0;
	}
	return $showSave;
}
register_activation_hook( __FILE__, 'return_previlages' );
