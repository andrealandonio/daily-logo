<?php
/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );

/***************************************************
DATABASE FUNCTIONS
 ***************************************************/

/**
 * Retrieve rows
 *
 * @param int $limit
 *
 * @return mixed
 */
function daily_logo_get_rows( $limit = DLP_PAGINATION ) {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Retrieve rows
    if ( ( $limit == -1 ) ) {
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
            logo_minute_end DESC'
        );
    }
    else {
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
        LIMIT ' . $limit . ' 
        OFFSET 0'
        );
    }

	return $rows;
}

/**
 * Show rows
 *
 * @param mixed $rows
 * @param bool $echo
 *
 * @return string
 */
function daily_logo_show_rows( $rows, $echo = false ) {
    $i = 0;
    $out = '';

    // Loop over rows
    foreach ( $rows as $row ) {
        $year_start = $row->logo_year_start;
        $month_start = daily_logo_format_digit_date( $row->logo_month_start );
        $day_start = daily_logo_format_digit_date( $row->logo_day_start );
        $hour_start = daily_logo_format_digit_date( $row->logo_hour_start );
        $minute_start = daily_logo_format_digit_date( $row->logo_minute_start );
        $year_end = ( $row->logo_year_end != 0 ) ? $row->logo_year_end : $year_start;
        $month_end = ( $row->logo_year_end != 0 ) ? daily_logo_format_digit_date( $row->logo_month_end ) : $month_start;
        $day_end = ( $row->logo_year_end != 0 ) ? daily_logo_format_digit_date( $row->logo_day_end ) : $day_start;
        $hour_end = ( $row->logo_year_end != 0 ) ? daily_logo_format_digit_date( $row->logo_hour_end ) : DLP_DEFAULT_END_DATE_HOUR;
        $minute_end = ( $row->logo_year_end != 0 ) ? daily_logo_format_digit_date( $row->logo_minute_end ) : DLP_DEFAULT_END_DATE_MINUTE;

        if ( $echo ) {
            $out .= '<tr id="logo-' . $row->id . '" ' . ( ( $i % 2 == 0 ) ? 'class="alternate"' : '' ) . ' valign="top">
                <td class="label-column">' . $row->logo_name . '</td>
                <td class="date-column">
                    ' . $year_start . '-' . $month_start . '-' . $day_start . ' ' . $hour_start . ':' . $minute_start . '&nbsp;=>&nbsp;' . $year_end . '-' . $month_end . '-' . $day_end . ' ' . $hour_end . ':' . $minute_end . '
                </td>
                <td class="bool-column">' . ( ! empty( $row->logo_link ) ? __( 'yes', DLP_PREFIX ) : __( '-', DLP_PREFIX ) ) . '</td>
                <td class="label-column">
                    ' . ( ! empty( $row->logo_image ) ? '<img src="' . $row->logo_image . '" alt="' . $row->logo_name . '" class="daily-image" />' : __( '-', DLP_PREFIX ) ) . '
                </td>
                <td class="label-column">
                    ' . ( ! empty( $row->logo_image_alternative ) ? '<img src="' . $row->logo_image_alternative . '" alt="' . $row->logo_name . '" class="daily-image" />' : __( '-', DLP_PREFIX ) ) . '
                </td>
                <td class="label-column">' . ( ! empty( $row->logo_class ) ? $row->logo_class : '-' ) . '</td>
                <td class="options-column">
                    <a href="javascript:void(0)" class="modify_row" onclick="modify_row(' . $row->id . ')">' . __( 'Modify', DLP_PREFIX ) . '</a>&nbsp;
                    <a href="javascript:void(0)" class="clone_row" onclick="clone_row(' . $row->id . ')">' . __( 'Clone', DLP_PREFIX ) . '</a>&nbsp;
                    <a href="javascript:void(0)" class="delete_row" onclick="delete_row(' . $row->id . ')">' . __( 'Delete', DLP_PREFIX ) . '</a>
                </td>
            </tr>';
        }
        else {
            ?>
            <tr id="logo-<?php echo $row->id ?>" <?php echo ( $i % 2 == 0 ) ? 'class="alternate"' : '' ?> valign="top">
                <td class="label-column"><?php echo $row->logo_name ?></td>
                <td class="date-column">
                    <?php echo $year_start ?>-<?php echo $month_start ?>-<?php echo $day_start ?> <?php echo $hour_start ?>:<?php echo $minute_start ?>&nbsp;=>&nbsp;<?php echo $year_end ?>-<?php echo $month_end ?>-<?php echo $day_end ?> <?php echo $hour_end ?>:<?php echo $minute_end ?>
                </td>
                <td class="bool-column"><?php echo ( ! empty( $row->logo_link ) ? __( 'yes', DLP_PREFIX ) : __( '-', DLP_PREFIX ) ) ?></td>
                <td class="label-column">
                    <?php echo ( ! empty( $row->logo_image ) ? '<img src="' . $row->logo_image . '" alt="' . $row->logo_name . '" class="daily-image" />' : __( '-', DLP_PREFIX ) ) ?>
                </td>
                <td class="label-column">
                    <?php echo ( ! empty( $row->logo_image_alternative ) ? '<img src="' . $row->logo_image_alternative . '" alt="' . $row->logo_name . '" class="daily-image" />' : __( '-', DLP_PREFIX ) ) ?>
                </td>
                <td class="label-column"><?php echo ( ! empty( $row->logo_class ) ? $row->logo_class : '-' ) ?></td>
                <td class="options-column">
                    <a href="javascript:void(0)" class="modify_row" onclick="modify_row(<?php echo $row->id ?>)"><?php _e( 'Modify', DLP_PREFIX ) ?></a>&nbsp;
                    <a href="javascript:void(0)" class="clone_row" onclick="clone_row(<?php echo $row->id ?>)"><?php _e( 'Clone', DLP_PREFIX ) ?></a>&nbsp;
                    <a href="javascript:void(0)" class="delete_row" onclick="delete_row(<?php echo $row->id ?>)"><?php _e( 'Delete', DLP_PREFIX ) ?></a>
                </td>
            </tr>
            <?php
        }
        $i++;
    }

    return $out;
}

/**
 * Format digit date appending the leading 0
 *
 * @param $value
 *
 * @return string
 */
function daily_logo_format_digit_date($value) {
	return $value < 10 ? "0" . $value : $value;
}

/**
 * Insert row
 *
 * @param int $blog_id
 * @param string $name
 * @param int $year_start
 * @param int $month_start
 * @param int $day_start
 * @param int $hour_start
 * @param int $minute_start
 * @param int $year_end
 * @param int $month_end
 * @param int $day_end
 * @param int $hour_end
 * @param int $minute_end
 * @param string $link
 * @param int $target
 * @param string $image
 * @param string $image_alternative
 * @param string $class
 *
 * @return mixed
 */
function daily_logo_insert_row( $blog_id, $name, $year_start, $month_start, $day_start, $hour_start, $minute_start, $year_end, $month_end, $day_end, $hour_end, $minute_end, $link, $target, $image, $image_alternative, $class ) {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Insert row
	$rows_affected = $wpdb->insert(
		$table_name,
		array(
            'blog_id' => $blog_id,
			'logo_name' => $name,
			'logo_year_start' => $year_start,
			'logo_month_start' => $month_start,
            'logo_day_start' => $day_start,
            'logo_hour_start' => $hour_start,
            'logo_minute_start' => $minute_start,
            'logo_year_end' => $year_end,
            'logo_month_end' => $month_end,
            'logo_day_end' => $day_end,
            'logo_hour_end' => $hour_end,
            'logo_minute_end' => $minute_end,
			'logo_link' => $link,
            'logo_target' => $target,
			'logo_image' => $image,
            'logo_image_alternative' => $image_alternative,
			'logo_class' => $class
		)
	);
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $rows_affected );

	// Return affected db rows
	return $rows_affected;
}

/**
 * Modify row
 *
 * @param int $id
 * @param int $blog_id
 * @param string $name
 * @param int $year_start
 * @param int $month_start
 * @param int $day_start
 * @param int $hour_start
 * @param int $minute_start
 * @param int $year_end
 * @param int $month_end
 * @param int $day_end
 * @param int $hour_end
 * @param int $minute_end
 * @param string $link
 * @param int $target
 * @param string $image
 * @param string $image_alternative
 * @param string $class
 *
 * @return false|int
 */
function daily_logo_modify_row( $id, $blog_id, $name, $year_start, $month_start, $day_start, $hour_start, $minute_start, $year_end, $month_end, $day_end, $hour_end, $minute_end, $link, $target, $image, $image_alternative, $class ) {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Update row
	$rows_affected = $wpdb->update(
		$table_name,
		array(
			'blog_id' => $blog_id,
			'logo_name' => $name,
            'logo_year_start' => $year_start,
            'logo_month_start' => $month_start,
            'logo_day_start' => $day_start,
            'logo_hour_start' => $hour_start,
            'logo_minute_start' => $minute_start,
            'logo_year_end' => $year_end,
            'logo_month_end' => $month_end,
            'logo_day_end' => $day_end,
            'logo_hour_end' => $hour_end,
            'logo_minute_end' => $minute_end,
			'logo_link' => $link,
			'logo_target' => $target,
			'logo_image' => $image,
			'logo_image_alternative' => $image_alternative,
			'logo_class' => $class,
		),
		array( 'id' => $id ),
		array(
			'%d',
			'%s',
			'%d',
			'%d',
			'%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%s',
			'%d',
			'%s',
			'%s',
			'%s'
		),
		array( '%d' )
	);

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $rows_affected );

	// Return affected db rows
	return $rows_affected;
}

/**
 * Update DB option
 */
function daily_logo_update_option_data() {
	// Get rows
	$rows = daily_logo_get_rows( -1 );
	$logo_array = array();

	// Loop over rows
	$logo = null;
	foreach ( $rows as $row ) {
		// Create Logo object
		$logo = new Daily_Logo( $row );

		// Add Logo object to logos array (only recent (less than 1 day) or future logos)
		$logo_date = new DateTime( $logo->year_end . '-' . $logo->month_end . '-' . $logo->day_end . ' ' . $logo->hour_end . ':' . $logo->minute_end );
		$now_date = new DateTime();
		$interval = intval( $now_date->diff( $logo_date )->format( '%R%a' ) );
		if ( $interval >= -1 ) $logo_array[] = $logo;
	}

	// Manage DB option
	if ( get_option( DLP_OPTION_DATA ) !== false ) {
		// The option already exists, update it
		update_option( DLP_OPTION_DATA, $logo_array );
	}
	else {
		// The option hasn't been added yet, add it
		add_option( DLP_OPTION_DATA, $logo_array );
	}
}