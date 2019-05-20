<?php
/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );

/***************************************************
UTILS FUNCTIONS
 ***************************************************/

/**
 * Search logo by date
 *
 * @param int $year
 * @param int $month
 * @param int $day
 * @param int $hour
 * @param int $minute
 *
 * @return Daily_Logo
 */
function daily_logo_search_logo( $year = null, $month = null, $day = null, $hour = null, $minute = null ) {
	// Retrieve rows from DB option
	$rows = get_option( DLP_OPTION_DATA );

    // Get date to check
    if ( empty( $year ) || empty( $month ) ||  empty( $day ) ||  empty( $hour ) ||  empty( $minute ) ) {
        // Get now
        $check_date = date( 'U', current_time( 'timestamp', 0 ) );
        $check_date_int = (int) $check_date;
        //$check_date = current_time( 'mysql' );
    }
    else {
        // Get provided date
        $check_date = new DateTime( $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute );
        $check_date_int = (int) $check_date->format('U');
    }

	// Loop over rows
	$logo = null;
	if ( ! empty( $rows ) ) {
		foreach ( $rows as $row ) {
		    try {
                $date_start = new DateTime( $row->year_start . '-' . $row->month_start . '-' . $row->day_start . ' ' . $row->hour_start . ':' . $row->minute_start );
                $date_end = new DateTime( $row->year_end . '-' . $row->month_end . '-' . $row->day_end . ' ' . $row->hour_end . ':' . $row->minute_end );
                $date_start_int = (int) $date_start->format('U');
                $date_end_int = (int) $date_end->format('U');

                // Check logo date
                if ( $check_date_int >= $date_start_int && $check_date_int < $date_end_int ) {
                    // Date logo founded
                    $logo = $row;
                }
            }
            catch (Exception $e) {}
		}	
	}

	return $logo;
}

/**
 * Create HTML snippet for displaying logo
 *
 * @param Daily_Logo $logo
 * @param boolean $alternative
 *
 * @return string
 */
function daily_logo_create_logo_snippet( $logo, $alternative = false ) {
	// Get logo snippet
	$snippet = daily_logo_get_logo_snippet( $logo, $alternative );

	// With custom logo replace template placeholders
	if ( ! empty( $logo ) ) {
		// Check conditional blocks
		if ( strpos( $snippet, '[?]##HAS_IMAGE##[?]' ) !== false ) {
			$snippet_query_position = strpos( $snippet, '[?]##HAS_IMAGE##[?]' );
			$snippet_query_length = strlen( '[?]##HAS_IMAGE##[?]' );
			$snippet_switch_position = strpos( $snippet, '[:]' );
			$snippet_switch_length = strlen( '[:]' );
			$snippet_end_position = strpos( $snippet, '[;]' );
			$snippet_end_length = strlen( '[;]' );

			// Get snippet for true/false value
			$snippet_condition_true = substr( $snippet, $snippet_query_position + $snippet_query_length, $snippet_switch_position - ( $snippet_query_position + $snippet_query_length ) );
			$snippet_condition_false = substr( $snippet, $snippet_switch_position + $snippet_switch_length, $snippet_end_position - ( $snippet_switch_position + $snippet_switch_length ) );

			// Get snippet for all the condition
			$snippet_condition_all = substr( $snippet, $snippet_query_position, $snippet_end_position + $snippet_end_length - $snippet_query_position );

			// Manage the right condition
			if ( ( ! $alternative && ! empty( $logo->image ) ) || ( $alternative && ! empty( $logo->image_alternative ) ) ) $snippet = str_replace( $snippet_condition_all, $snippet_condition_true, $snippet );
			else $snippet = str_replace( $snippet_condition_all, $snippet_condition_false, $snippet );
		}

		if ( strpos( $snippet, '[?]##HAS_IMAGE_ALTERNATIVE##[?]' ) !== false ) {
			$snippet_query_position = strpos( $snippet, '[?]##HAS_IMAGE_ALTERNATIVE##[?]' );
			$snippet_query_length = strlen( '[?]##HAS_IMAGE_ALTERNATIVE##[?]' );
			$snippet_switch_position = strpos( $snippet, '[:]' );
			$snippet_switch_length = strlen( '[:]' );
			$snippet_end_position = strpos( $snippet, '[;]' );
			$snippet_end_length = strlen( '[;]' );

			// Get snippet for true/false value
			$snippet_condition_true = substr( $snippet, $snippet_query_position + $snippet_query_length, $snippet_switch_position - ( $snippet_query_position + $snippet_query_length ) );
			$snippet_condition_false = substr( $snippet, $snippet_switch_position + $snippet_switch_length, $snippet_end_position - ( $snippet_switch_position + $snippet_switch_length ) );

			// Get snippet for all the condition
			$snippet_condition_all = substr( $snippet, $snippet_query_position, $snippet_end_position + $snippet_end_length - $snippet_query_position );

			// Manage the right condition
			if ( ! empty( $logo->image_alternative ) ) $snippet = str_replace( $snippet_condition_all, $snippet_condition_true, $snippet );
			else $snippet = str_replace( $snippet_condition_all, $snippet_condition_false, $snippet );
		}

		// Manage ##LINK## placeholder
		if ( ! empty( $logo->link ) ) $snippet = str_replace( '##LINK##', $logo->link, $snippet );
		else $snippet = str_replace( '##LINK##', 'javascript:void(0)', $snippet );

		// Manage ##NAME## placeholder
		if ( ! empty( $logo->name ) ) $snippet = str_replace( '##NAME##', $logo->name, $snippet );
		else $snippet = str_replace( '##NAME##', '', $snippet );

		// Manage ##TARGET## placeholder
		if ( $logo->target === 1 ) $snippet = str_replace( '##TARGET##', '_blank', $snippet );
		else $snippet = str_replace( '##TARGET##', '_self', $snippet );

		// Manage ##CLASS## placeholder
		if ( ! empty( $logo->class ) ) $snippet = str_replace( '##CLASS##', $logo->class, $snippet );
		else $snippet = str_replace( '##CLASS##', '', $snippet);

		// Manage ##IMAGE## placeholder
		if ( ! $alternative && ! empty( $logo->image ) ) $snippet = str_replace( '##IMAGE##', $logo->image, $snippet );
		else if ( $alternative && ! empty( $logo->image_alternative ) ) $snippet = str_replace( '##IMAGE##', $logo->image_alternative, $snippet );
		else $snippet = str_replace( '##IMAGE##', '', $snippet );

		// Manage ##IMAGE_ALTERNATIVE## placeholder
		if ( ! empty( $logo->image_alternative ) ) $snippet = str_replace( '##IMAGE_ALTERNATIVE##', $logo->image_alternative, $snippet );
		else $snippet = str_replace( '##IMAGE_ALTERNATIVE##', '', $snippet );
	}

	return $snippet;
}

/**
 * Get HTML snippet for displaying logo
 *
 * @param Daily_Logo $logo
 * @param boolean $alternative
 *
 * @return string
 */
function daily_logo_get_logo_snippet( $logo, $alternative = false ) {
	// Get settings options
	$options = get_option( DLP_OPTION_SETTINGS );

	if ( ! empty ( $logo ) ) {
		// Retrieve custom logo template
		if ($alternative) $snippet = ( ! empty( $options->alternative_template_with_logo ) ) ? $options->alternative_template_with_logo : DLP_ALTERNATIVE_TEMPLATE_DEFAULT;
		else $snippet = ( ! empty( $options->template_with_logo ) ) ? $options->template_with_logo : DLP_TEMPLATE_DEFAULT;
	}
	else {
		// Retrieve standard logo template
		if ($alternative) $snippet = ( ! empty( $options->alternative_template_without_logo ) ) ? $options->alternative_template_without_logo : DLP_STANDARD_ALTERNATIVE_TEMPLATE_DEFAULT;
		else $snippet = ( ! empty( $options->template_without_logo ) ) ? $options->template_without_logo : DLP_STANDARD_TEMPLATE_DEFAULT;
	}

	return $snippet;
}

/**
 * Display today logo
 *
 * @return string|null
 */
function daily_logo_show_today() {
	// Search logo for today
	$logo = daily_logo_search_logo();

	// Return logo snippet
	echo daily_logo_create_logo_snippet( $logo );

    return null;
}
add_action( 'daily_logo_show_today', 'daily_logo_show_today' );

/**
 * Display today logo
 *
 * @return string|null
 */
function daily_logo_show_today_alternative() {
	// Search logo for today
	$logo = daily_logo_search_logo();

	// Return logo snippet
	echo daily_logo_create_logo_snippet( $logo, true );

    return null;
}
add_action( 'daily_logo_show_today_alternative', 'daily_logo_show_today_alternative' );

/**
 * Display date logo
 *
 * @param int $year
 * @param int $month
 * @param int $day
 * @param int $hour
 * @param int $minute
 *
 * @return string|null
 */
function daily_logo_show_date( $year, $month, $day, $hour, $minute ) {
	// Search logo for date
	$logo = daily_logo_search_logo( $year, $month, $day, $hour, $minute );

	// Return logo snippet
	echo daily_logo_create_logo_snippet( $logo );

    return null;
}
add_action( 'daily_logo_show_date', 'daily_logo_show_date', 10, 3 );

/**
 * Display date logo
 *
 * @param $year
 * @param $month
 * @param $day
 * @param int $hour
 * @param int $minute
 *
 * @return string|null
 */
function daily_logo_show_date_alternative( $year, $month, $day, $hour, $minute ) {
	// Search logo for date
	$logo = daily_logo_search_logo( $year, $month, $day, $hour, $minute );

	// Return logo snippet
	echo daily_logo_create_logo_snippet( $logo, true );

    return null;
}
add_action( 'daily_logo_show_date_alternative', 'daily_logo_show_date_alternative', 10, 3 );
