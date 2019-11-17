<?php
/*
Plugin Name: Daily logo
Description: Daily logo is a simple and flexible plugin which allow users to display a different header/logo in their site every day.
Author: Andrea Landonio
Author URI: http://www.andrealandonio.it
Text Domain: daily_logo
Domain Path: /languages/
Version: 2.1.2
License: GPL v3

Daily logo
Copyright (C) 2013-2019, Andrea Landonio - landonio.andrea@gmail.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Path missing "__DIR__" constant on environment
if ( ! defined( '__DIR__' ) ) {
    define( '__DIR__', dirname( __FILE__ ) );
}

/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );
require_once( __DIR__ . '/daily-logo-settings.php' );
require_once( __DIR__ . '/daily-logo-utils.php' );

/***************************************************
PLUGIN ACTIVATION
 ***************************************************/

/**
 * Register activation hook
 */
function daily_logo_activation() {
	global $wpdb;
    $table_name = $wpdb->prefix . DLP_DB_TABLE;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
          id int(9) NOT NULL AUTO_INCREMENT,
          blog_id int(9) NOT NULL,
          logo_name varchar(255) NOT NULL,
          logo_day_start int(2) NOT NULL  DEFAULT 0,
          logo_month_start int(2) NOT NULL DEFAULT 0,
          logo_year_start int(4) NOT NULL DEFAULT 0,
          logo_hour_start int(2) NOT NULL DEFAULT 0,
          logo_minute_start int(2) NOT NULL DEFAULT 0,
          logo_day_end int(2) NOT NULL DEFAULT 0,
          logo_month_end int(2) NOT NULL DEFAULT 0,
          logo_year_end int(4) NOT NULL DEFAULT 0,
          logo_hour_end int(2) NOT NULL DEFAULT 0,
          logo_minute_end int(2) NOT NULL DEFAULT 0,
          logo_link varchar(255),
          logo_target int(1) NOT NULL,
          logo_image varchar(255),
          logo_image_alternative varchar(255),
          logo_class varchar(255),
          PRIMARY KEY (ID)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $wpdb->query( $sql );
}
register_activation_hook( WP_PLUGIN_DIR . '/daily-logo/daily-logo.php', 'daily_logo_activation' );

/***************************************************
PLUGIN DEACTIVATION
 ***************************************************/

/**
 * Register deactivation hook
 */
function daily_logo_deactivation() {
	global $wpdb;
    $table_name = $wpdb->prefix . DLP_DB_TABLE;

	// If table is empty, remove it
    $table_rows = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name );

    if ( $table_rows == 0 ) {
        // Drop table
        $wpdb->query( 'DROP TABLE IF EXISTS ' . $table_name );

	    delete_option( DLP_OPTION_DATA );
	    delete_option( DLP_OPTION_SETTINGS );
    }
}
register_deactivation_hook( WP_PLUGIN_DIR . '/daily-logo/daily-logo.php', 'daily_logo_deactivation' );

/***************************************************
PLUGIN UPGRADE
 ***************************************************/

/**
 * Upgrade database
 */
function daily_logo_database_upgrade() {
    global $wpdb;
    $table_name = $wpdb->prefix . DLP_DB_TABLE;

    $sql = "ALTER TABLE $table_name 
          CHANGE COLUMN logo_day logo_day_start int(2) NOT NULL DEFAULT 0,
          CHANGE COLUMN logo_month logo_month_start int(2) NOT NULL DEFAULT 0,
          CHANGE COLUMN logo_year logo_year_start int(4) NOT NULL DEFAULT 0,
          ADD logo_hour_start int(2) NOT NULL DEFAULT 0 AFTER logo_year_start,
          ADD logo_minute_start int(2) NOT NULL DEFAULT 0 AFTER logo_hour_start,
          ADD logo_day_end int(2) NOT NULL DEFAULT 0 AFTER logo_minute_start,
          ADD logo_month_end int(2) NOT NULL DEFAULT 0 AFTER logo_day_end,
          ADD logo_year_end int(4) NOT NULL DEFAULT 0 AFTER logo_month_end,
          ADD logo_hour_end int(2) NOT NULL DEFAULT 0 AFTER logo_year_end,
          ADD logo_minute_end int(2) NOT NULL DEFAULT 0 AFTER logo_hour_end
    ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $wpdb->query( $sql );
}

/***************************************************
PLUGIN VERSION
 ***************************************************/

/**
 * Check database version
 */
function daily_logo_update_db_check() {
    global $wpdb;
    $table_name = $wpdb->prefix . DLP_DB_TABLE;
    $installed_version = get_option( 'daily_logo_db_version' );

    // If table does not exists, create it
    if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
        daily_logo_activation();

        // Update version
        update_option( 'daily_logo_db_version', DLP_DB_VERSION );
    }
    else {
        // Check installed version, then upgrade database, if necessary
        if ( $installed_version != DLP_DB_VERSION ) {
            // Update database schema
            daily_logo_database_upgrade();

            update_option( 'daily_logo_db_version', DLP_DB_VERSION );
        }
    }
}
add_action( 'plugins_loaded', 'daily_logo_update_db_check' );

/***************************************************
PLUGIN ACTIONS
 ***************************************************/

/**
 * Add menu settings
 */
function daily_logo_setting_menu() {
    // Register stylesheet
    wp_register_style( 'daily_logo_style', plugins_url( 'daily-logo/css/daily-logo.css' ) );
    wp_enqueue_style( 'daily_logo_style' );

    // Get WordPress version
    $wp_version = get_bloginfo( 'version' );

    // Check WordPress version, apply dashicons only for WordPress 4.0 or higher
    if ( $wp_version >= 4.0 ) {
        // Add menu pages
        $menu = add_menu_page( 'Daily logo', 'Daily logo', 'manage_options', DLP_MENU, 'daily_logo_menu_page', 'dashicons-calendar-alt' );
        $submenu_manage = add_submenu_page( DLP_MENU, __( 'Manage', DLP_PREFIX ), __( 'Manage', DLP_PREFIX ), 'manage_options', DLP_MENU );
        $submenu_settings = add_submenu_page( DLP_MENU, __( 'Settings', DLP_PREFIX ), __( 'Settings', DLP_PREFIX ), 'manage_options', DLP_MENU_SETTINGS, 'daily_logo_menu_page_settings' );
    }
    else {
        // Add menu pages
        $menu = add_menu_page( 'Daily logo', 'Daily logo', 'manage_options', DLP_MENU, 'daily_logo_menu_page' );
        $submenu_manage = add_submenu_page( DLP_MENU, __( 'Manage', DLP_PREFIX ), __( 'Manage', DLP_PREFIX ), 'manage_options', DLP_MENU );
        $submenu_settings = add_submenu_page( DLP_MENU, __( 'Settings', DLP_PREFIX ), __( 'Settings', DLP_PREFIX ), 'manage_options', DLP_MENU_SETTINGS, 'daily_logo_menu_page_settings' );
    }

    // Add actions to enqueue style and scripts
	add_action( 'admin_print_styles-' . $menu, 'daily_logo_admin_custom_css' );
    add_action( 'admin_print_styles-' . $submenu_manage, 'daily_logo_admin_custom_css' );
	add_action( 'admin_print_styles-' . $submenu_settings, 'daily_logo_admin_custom_css' );

	add_action( 'admin_print_scripts-' . $menu, 'daily_logo_admin_custom_js' );
    add_action( 'admin_print_scripts-' . $submenu_manage, 'daily_logo_admin_custom_js' );
	add_action( 'admin_print_scripts-' . $submenu_settings, 'daily_logo_admin_custom_js' );
}
add_action( 'admin_menu', 'daily_logo_setting_menu' );

/**
 * Enqueue styles
 */
function daily_logo_admin_custom_css() {
	// Enqueue date picker CSS
	wp_enqueue_style( 'jquery-ui-css', plugins_url( 'daily-logo/css/jquery-ui.css' ) );

	// Enqueue thickbox CSS
	wp_enqueue_style( 'thickbox');
}

/**
 * Enqueue scripts
 */
function daily_logo_admin_custom_js() {
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'jquery-validate', plugins_url( 'daily-logo/js/jquery.validate.min.js' ), array( 'jquery' ), '1.10.0', true );
	wp_register_script( 'daily_logo_script', plugins_url( 'daily-logo/js/daily-logo.js' ), array( 'jquery', 'media-upload', 'thickbox' ) );
	wp_enqueue_script( 'daily_logo_script' );
}
