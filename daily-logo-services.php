<?php
/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );
require_once( __DIR__ . '/daily-logo-database.php' );
require_once( __DIR__ . '/classes/daily-logo.php' );

/***************************************************
SERVICES FUNCTIONS
 ***************************************************/

/**
 * Get row service callback
 */
function daily_logo_get_row_callback() {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Check nonce
	if ( !wp_verify_nonce( $_REQUEST[ 'nonce' ], DLP_NONCE ) ) {
		die( 'No naughty business please' );
	}

	// Read parameters
	$id = sanitize_text_field( $_POST[ 'id' ] );

	// Get rows
	$rows = $wpdb->get_results( 'SELECT * FROM ' . $table_name . ' WHERE id = ' . $id );

	// Loop over rows
	$logo = null;
	foreach ( $rows as $row ) {
		// Create Logo object
		$logo = new Daily_Logo( $row );
	}

	// Return JSON response
	wp_send_json( $logo );
}
add_action( 'wp_ajax_daily_logo_get_row', 'daily_logo_get_row_callback' );
add_action( 'wp_ajax_nopriv_daily_logo_get_row', 'daily_logo_get_row_callback' );

/**
 * Get rows service callback
 */
function daily_logo_get_rows_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . DLP_DB_TABLE;
    header( "Content-Type: text/html" );

    // Read parameters
    $page = intval( sanitize_text_field( $_REQUEST[ 'page' ] ) ) - 1;
    $items = ( isset( $_REQUEST[ 'items' ] ) ) ? intval( sanitize_text_field( $_REQUEST[ 'items' ] ) ) : DLP_PAGINATION;

    // Get rows
    $rows = $wpdb->get_results( '
        SELECT * 
        FROM ' . $table_name . ' 
        ORDER BY 
            logo_year_start DESC, 
            logo_month_start DESC, 
            logo_day_start DESC, 
            logo_hour_start DESC, 
            logo_minute_start DESC,
            logo_year_end DESC, 
            logo_month_end DESC, 
            logo_day_end DESC, 
            logo_hour_end DESC, 
            logo_minute_end DESC 
        LIMIT ' . $items . ' 
        OFFSET ' . ( $page * $items )
    );

    // Loop over rows
    $logos = daily_logo_show_rows( $rows, true );

    // Return logo rows
    die( $logos );
}
add_action( 'wp_ajax_daily_logo_get_rows', 'daily_logo_get_rows_callback' );
add_action( 'wp_ajax_nopriv_daily_logo_get_rows', 'daily_logo_get_rows_callback' );

/**
 * Save row service callback
 */
function daily_logo_save_row_callback() {
	// Check nonce
	if ( !wp_verify_nonce( $_REQUEST[ 'nonce' ], DLP_NONCE ) ) {
		die( 'No naughty business please' );
	}

	// Read data parameters
	$id = (int) sanitize_text_field( $_POST[ 'id' ] );
	$blog_id = (int) sanitize_text_field( $_POST[ 'blog_id' ] );
	$name = (string) sanitize_text_field( $_POST[ 'name' ] );
	$link = (string) sanitize_text_field( $_POST[ 'link' ] );
	$target = (int) sanitize_text_field( $_POST[ 'target' ] );
	$image = (string) sanitize_text_field( $_POST[ 'image' ] );
	$image_alternative = (string) sanitize_text_field( $_POST[ 'image_alternative' ] );
	$class = (string) sanitize_text_field( $_POST[ 'class' ] );

	// Manage start date
    $date_start = (string) sanitize_text_field( $_POST[ 'date_start' ] );
    $year_start = intval( substr( $date_start, 0, 4 ) );
    $month_start = intval( substr( $date_start, 5, 2 ) );
    $day_start = intval( substr( $date_start, 8, 2 ) );
    $hour_start = (int) sanitize_text_field( $_POST[ 'date_hour_start' ] );
    $minute_start = (int) sanitize_text_field( $_POST[ 'date_minute_start' ] );

    // Manage start date and defaults
    $date_end = (string) sanitize_text_field( $_POST[ 'date_end' ] );
    if ( ! empty( $date_end ) ) {
        // End date is not empty, use it
        $year_end = intval( substr( $date_end, 0, 4 ) );
        $month_end = intval( substr( $date_end, 5, 2 ) );
        $day_end = intval( substr( $date_end, 8, 2 ) );
        $hour_end = (int) sanitize_text_field( $_POST[ 'date_hour_end' ] );
        $minute_end = (int) sanitize_text_field( $_POST[ 'date_minute_end' ] );
    }
    else {
        // End date is empty, use start date with 23:59 as time
        $year_end = $year_start;
        $month_end = $month_start;
        $day_end = $day_start;
        $hour_end = DLP_DEFAULT_END_DATE_HOUR;
        $minute_end = DLP_DEFAULT_END_DATE_MINUTE;
    }

	// Check id value to detect if action is an insert or an update
	if ( is_int( $id ) && $id != 0 ) {
		// Modify row
		daily_logo_modify_row( $id, $blog_id, $name, $year_start, $month_start, $day_start, $hour_start, $minute_start, $year_end, $month_end, $day_end, $hour_end, $minute_end, $link, $target, $image, $image_alternative, $class );
	}
	else {
		// Insert row
		daily_logo_insert_row( $blog_id, $name, $year_start, $month_start, $day_start, $hour_start, $minute_start, $year_end, $month_end, $day_end, $hour_end, $minute_end, $link, $target, $image, $image_alternative, $class );
	}

	// Update rows in option DB field
	daily_logo_update_option_data();

	// Get rows
	$rows = daily_logo_get_rows();

	// Show rows in table
	daily_logo_show_rows( $rows );

	die();
}
add_action( 'wp_ajax_daily_logo_save_row', 'daily_logo_save_row_callback' );
add_action( 'wp_ajax_nopriv_daily_logo_save_row', 'daily_logo_save_row_callback' );

/**
 * Clone row service callback
 */
function daily_logo_clone_row_callback() {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Check nonce
	if ( !wp_verify_nonce( $_REQUEST[ 'nonce' ], DLP_NONCE ) ) {
		die( 'No naughty business please' );
	}

	// Read data parameters
	$id = (int) sanitize_text_field( $_POST[ 'id' ] );

	// Get rows
	$rows = $wpdb->get_results( 'SELECT * FROM ' . $table_name . ' WHERE id = ' . $id );

	// Loop over rows
	$logo = null;
	foreach ( $rows as $row ) {
		// Create Logo object
		$logo = new Daily_Logo( $row );
	}

	daily_logo_insert_row( $logo->blog_id, $logo->name . ' (clone)', $logo->year_start, $logo->month_start, $logo->day_start, $logo->hour_start, $logo->minute_start, $logo->year_end, $logo->month_end, $logo->day_end, $logo->hour_end, $logo->minute_end, $logo->link, $logo->target, $logo->image, $logo->image_alternative, $logo->class );

	// Update rows in option DB field
	daily_logo_update_option_data();

	// Get rows
	$rows = daily_logo_get_rows();

	// Show rows in table
	daily_logo_show_rows( $rows );

	die();
}
add_action( 'wp_ajax_daily_logo_clone_row', 'daily_logo_clone_row_callback' );
add_action( 'wp_ajax_nopriv_daily_logo_clone_row', 'daily_logo_clone_row_callback' );

/**
 * Delete row service callback
 */
function daily_logo_remove_row_callback() {
    global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Check nonce
	if ( !wp_verify_nonce( $_REQUEST[ 'nonce' ], DLP_NONCE ) ) {
		die( 'No naughty business please' );
	}

	// Read parameters
	$id = sanitize_text_field( $_POST[ 'id' ] );

	// Delete row
	$wpdb->delete( $table_name, array( 'id' => $id ) );

	// Update rows in option DB field
	daily_logo_update_option_data();

    // Get rows
    $rows = daily_logo_get_rows();

    // Show rows in table
    daily_logo_show_rows( $rows );

    die();
}
add_action( 'wp_ajax_daily_logo_remove_row', 'daily_logo_remove_row_callback' );
add_action( 'wp_ajax_nopriv_daily_logo_remove_row', 'daily_logo_remove_row_callback' );
