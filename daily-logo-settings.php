<?php
/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );
require_once( __DIR__ . '/daily-logo-database.php' );
require_once( __DIR__ . '/daily-logo-services.php' );

/***************************************************
PLUGIN SETTINGS
 ***************************************************/

// Check request data before save settings
if ( isset( $_REQUEST[ 'page' ] ) && $_REQUEST[ 'page' ] == DLP_MENU_SETTINGS && isset( $_REQUEST[ 'action' ] ) && ( $_REQUEST[ 'action' ] == 'settings' || $_REQUEST[ 'action' ] == 'restore' ) ) {
	daily_logo_save_settings();
}

/**
 * Add daily logo settings before the admin page is rendered
 */
function daily_logo_admin_init() {
	add_settings_section( 'daily_logo_main', '', 'daily_logo_option_main_show', 'daily_logo_plugin' );
}
add_action( 'admin_init', 'daily_logo_admin_init' );

/**
 * Load internalization supports
 */
function daily_logo_load_textdomain() {
    load_plugin_textdomain( DLP_PREFIX, false, dirname(plugin_basename( __FILE__ )) . '/languages/' );
}
add_action( 'plugins_loaded', 'daily_logo_load_textdomain' );

/**
 * Render admin menu page
 */
function daily_logo_menu_page() {
    // Get info
    $blog_id = get_current_blog_id();
	?>
	<div class="wrap">
        <div class="icon32"><img src="<?php echo plugins_url( 'daily-logo/images/icon32.png' ) ?>" /></div>
        <h2><?php _e( 'Daily logo management', DLP_PREFIX ) ?></h2>

        <form action="" id="daily_logo_table">
            <p>
                <?php _e( 'In this page you can manage all the logos. First of all you can create a new logo completing the following form. After you have filled all your logo\'s data, by clicking the "save" button your logo\'s information will be stored on database. You can see it in the list table in the bottom of this page. Use the list table options to edit or delete a logo.<br /><span class="description">Note: There are only two required fields, the logo name and the logo date.</span>', DLP_PREFIX ) ?>
            </p>

	        <input type="hidden" id="id" name="id" value="">
	        <input type="hidden" id="action" name="action" value="insert">
	        <input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce( DLP_NONCE ) ?>">
	        <input type="hidden" id="blog_id" name="blog_id" value="<?php echo $blog_id ?>">
	        <input type="hidden" id="image_frame" name="image_frame" value="image">

	        <table class="form-table">
		        <tbody>
			        <tr>
				        <th scope="row"><label for="name"><?php _e( 'Name', DLP_PREFIX ) ?></label></th>
				        <td>
				            <input name="name" type="text" id="name" value="" placeholder="" class="regular-text" />
				            <p class="description"><?php _e( 'Provide a name for the logo', DLP_PREFIX ) ?>&nbsp;<?php _e( '(* required)', DLP_PREFIX ) ?></p>
						</td>
			        </tr>
			        <tr>
				        <th scope="row"><label for="date_start"><?php _e( 'Date start', DLP_PREFIX ) ?></label></th>
				        <td>
					        <input name="date_start" type="text" id="date_start" value="" placeholder="" class="date regular-text" />
                            <select name="date_hour_start" id="date_hour_start" class="time regular-text">
                                <?php
                                for ( $i = 0; $i < 24; $i++ ) {
                                    ?><option value="<?php echo $i ?>"><?php echo daily_logo_format_digit_date( $i ) ?></option><?php
                                }
                                ?>
                            </select>
                            :
                            <select name="date_minute_start" id="date_minute_start" class="time regular-text">
                                <?php
                                for ( $i = 0; $i < 60; $i++ ) {
                                    ?><option value="<?php echo $i ?>"><?php echo daily_logo_format_digit_date( $i ) ?></option><?php
                                }
                                ?>
                            </select>
                            <p class="description"><?php _e( 'Provide the start date of the logo', DLP_PREFIX ) ?>&nbsp;<?php _e( '(* required)', DLP_PREFIX ) ?></p>
				        </td>
			        </tr>
                    <tr>
                        <th scope="row"><label for="date_end"><?php _e( 'Date end', DLP_PREFIX ) ?></label></th>
                        <td>
                            <input name="date_end" type="text" id="date_end" value="" placeholder="" class="date regular-text" />
                            <select name="date_hour_end" id="date_hour_end" class="time regular-text">
                                <?php
                                for ( $i = 0; $i < 24; $i++ ) {
                                    ?><option value="<?php echo $i ?>"><?php echo daily_logo_format_digit_date( $i ) ?></option><?php
                                }
                                ?>
                            </select>
                            :
                            <select name="date_minute_end" id="date_minute_end" class="time regular-text">
                                <?php
                                for ( $i = 0; $i < 60; $i++ ) {
                                    ?><option value="<?php echo $i ?>"><?php echo daily_logo_format_digit_date( $i ) ?></option><?php
                                }
                                ?>
                            </select>
                            <p class="description"><?php _e( 'Provide the end date of the logo, if empty, the start date (with end time at 23:59) will be used', DLP_PREFIX ) ?></p>
                        </td>
                    </tr>
			        <tr>
				        <th scope="row"><label for="link"><?php _e( 'Link', DLP_PREFIX ) ?></label></th>
				        <td>
					        <input name="link" type="text" id="link" value="" class="regular-text" />
					        - <label for="target"><?php _e( 'Open in a new window?', DLP_PREFIX ) ?></label>&nbsp;
					        <input name="target" type="checkbox" id="target" value="1" />
					        <p class="description">
								<?php _e( 'Provide the link used in the logo hyperlink', DLP_PREFIX ) ?>&nbsp;
								(<a href="javascript:void(0)" id="use_current_site" name="use_current_site"><?php _e( 'use current site URL', DLP_PREFIX ) ?></a>)
							</p>
				        </td>
			        </tr>
			        <tr>
				        <th scope="row"><label for="image"><?php _e( 'Image', DLP_PREFIX) ?></label></th>
				        <td>
							<input name="image" type="text" id="image" size="36" value="" class="regular-text" />
						    <input name="image_button" type="button" id="image_button" value="<?php _e( 'Upload', DLP_PREFIX ) ?>" />
                            <input name="image_reset" type="button" id="image_reset" value="<?php _e( 'Reset', DLP_PREFIX ) ?>" />
					        <p class="description"><?php _e( 'Enter an URL or upload an image for the logo', DLP_PREFIX ) ?></p>
				        </td>
			        </tr>
			        <tr>
				        <th scope="row"><label for="image_alternative"><?php _e( 'Alternative image', DLP_PREFIX) ?></label></th>
				        <td>
					        <input name="image_alternative" type="text" id="image_alternative" size="36" value="" class="regular-text" />
					        <input name="image_alternative_button" type="button" id="image_alternative_button" value="<?php _e( 'Upload', DLP_PREFIX ) ?>" />
                            <input name="image_alternative_reset" type="button" id="image_alternative_reset" value="<?php _e( 'Reset', DLP_PREFIX ) ?>" />
                            <p class="description"><?php _e( 'Enter an URL or upload an alternative image for the logo (used with alternative template layout', DLP_PREFIX ) ?></p>
				        </td>
			        </tr>
			        <tr>
				        <th scope="row"><label for="class"><?php _e( 'CSS Class', DLP_PREFIX ) ?></label></th>
				        <td>
					        <input name="class" type="text" id="class" value="" placeholder="" class="regular-text" />
					        <p class="description"><?php _e( 'Provide a custom CSS class used in the logo HTML markup', DLP_PREFIX ) ?></p>
				        </td>
			        </tr>
		        </tbody>
	        </table>

	        <p class="submit">
		        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save', DLP_PREFIX ) ?>" />
	        </p>

	        <h3><?php _e( 'Logo list', DLP_PREFIX ) ?></h3>
	        <p>
		        <?php _e( 'In the following table you can see all the existing logo ordered by date. Beside every row you have two option to modify a logo information or delete it from database.', DLP_PREFIX ) ?><br />
                <?php _e( 'After every action (modify, clone or delete) the table will be reloaded.', DLP_PREFIX ) ?>
	        </p>

	        <?php
	        // Prints out all settings of daily logo settings page
	        do_settings_sections( 'daily_logo_plugin' );
	        ?>

        </form>

	</div>
    <?php
}

/**
 * Render admin settings page
 */
function daily_logo_menu_page_settings() {
	// Get settings options
	$options = get_option( DLP_OPTION_SETTINGS );

	// Get page fields
	$template_without_logo = ( ! empty( $options->template_without_logo ) ) ? $options->template_without_logo : DLP_STANDARD_TEMPLATE_DEFAULT;
	$alternative_template_without_logo = ( ! empty( $options->alternative_template_without_logo ) ) ? $options->alternative_template_without_logo : DLP_STANDARD_ALTERNATIVE_TEMPLATE_DEFAULT;
	$template_with_logo = ( ! empty( $options->template_with_logo ) ) ? $options->template_with_logo : DLP_TEMPLATE_DEFAULT;
	$alternative_template_with_logo = ( ! empty( $options->alternative_template_with_logo ) ) ? $options->alternative_template_with_logo : DLP_ALTERNATIVE_TEMPLATE_DEFAULT;
	?>
	<div class="wrap">
		<div class="icon32"><img src="<?php echo plugins_url( 'daily-logo/images/icon32.png' ) ?>" /></div>
		<h2><?php _e( 'Daily logo settings', DLP_PREFIX ) ?></h2>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				<?php if (isset($_GET['updated'])) { ?>
				jQuery('#setting-error-settings_updated').delay(3000).slideUp(400);
				<?php }	?>
			});
		</script>

		<form action="" id="daily_logo_settings" method="POST">
			<p>
				<?php _e( 'In this page you can manage all the logos. First of all you can create a new logo completing the following form. After you have filled all your logo\'s data, by clicking the "save" button your logo\'s information will be stored on database. You can see it in the list table in the bottom of this page. Use the list table options to edit or delete a logo.<br /><span class="description">Note: There are only two required fields, the logo name and the logo date.</span>', DLP_PREFIX ) ?>
			</p>

			<input type="hidden" id="action" name="action" value="settings">
			<input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce( DLP_NONCE ) ?>">

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="template_without_logo"><?php _e( 'Template without daily logo', DLP_PREFIX ) ?></label></th>
						<td>
							<textarea name="template_without_logo" id="template_without_logo" cols="150" rows="5"><?php echo $template_without_logo ?></textarea>
							<p class="description"><?php _e( 'Provide the logo HTML template used if no daily logo exists. Do not use PHP code or WordPress functions, please provide only static text.', DLP_PREFIX ) ?>&nbsp;<?php _e( '(* required)', DLP_PREFIX ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="alternative_template_without_logo"><?php _e( 'Alternative template without daily logo', DLP_PREFIX ) ?></label></th>
						<td>
							<textarea name="alternative_template_without_logo" id="alternative_template_without_logo" cols="150" rows="5"><?php echo $alternative_template_without_logo ?></textarea>
							<p class="description"><?php _e( 'Provide an alternative logo HTML template used if no daily logo exists. The alternative logo should be used to manage different templates and layouts, for example: desktop layout or mobile layout. If you don\'t use different layouts please fill the same value of the above field. Do not use PHP code or WordPress functions, please provide only static text.', DLP_PREFIX ) ?>&nbsp;<?php _e( '(* required)', DLP_PREFIX ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="template_with_logo"><?php _e( 'Template with daily logo', DLP_PREFIX ) ?></label></th>
						<td>
							<textarea name="template_with_logo" id="template_with_logo" cols="150" rows="5"><?php echo $template_with_logo ?></textarea>
							<p class="description"><?php _e( 'Provide the logo HTML template used if a daily logo exists. Do not use PHP code or WordPress functions, please provide only static text.', DLP_PREFIX ) ?>&nbsp;<?php _e( '(* required)', DLP_PREFIX ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="alternative_template_with_logo"><?php _e( 'Alternative template with daily logo', DLP_PREFIX ) ?></label></th>
						<td>
							<textarea name="alternative_template_with_logo" id="alternative_template_with_logo" cols="150" rows="5"><?php echo $alternative_template_with_logo ?></textarea>
							<p class="description"><?php _e( 'Provide an alternative logo HTML template used if a daily logo exists. The alternative logo should be used to manage different templates and layouts, for example: desktop layout or mobile layout. If you don\'t use different layouts please fill the same value of the above field. Do not use PHP code or WordPress functions, please provide only static text.', DLP_PREFIX ) ?>&nbsp;<?php _e( '(* required)', DLP_PREFIX ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Legend', DLP_PREFIX ) ?></th>
						<td>
							<b><i><?php _e( '##LINK##', DLP_PREFIX ) ?></i></b>
							<p class="legend_description"><?php _e( 'A placeholder to set logo link', DLP_PREFIX ) ?></p>
							<b><i><?php _e( '##NAME##', DLP_PREFIX ) ?></i></b>
							<p class="legend_description"><?php _e( 'A placeholder to set logo name', DLP_PREFIX ) ?></p>
							<b><i><?php _e( '##TARGET##', DLP_PREFIX ) ?></i></b>
							<p class="legend_description"><?php _e( 'A placeholder to set logo link target', DLP_PREFIX ) ?></p>
							<b><i><?php _e( '##CLASS##', DLP_PREFIX ) ?></i></b>
							<p class="legend_description"><?php _e( 'A placeholder to set logo custom CSS class', DLP_PREFIX ) ?></p>
							<b><i><?php _e( '##IMAGE##', DLP_PREFIX ) ?></i></b>
							<p class="legend_description"><?php _e( 'A placeholder to set logo image', DLP_PREFIX ) ?></p>
                            <b><i><?php _e( '##IMAGE_ALTERNATIVE##', DLP_PREFIX ) ?></i></b>
                            <p class="legend_description"><?php _e( 'A placeholder to set logo alternative image', DLP_PREFIX ) ?></p>

                            <br />

							<b><i><?php _e( 'Conditional tags', DLP_PREFIX ) ?></i></b>
							<div class="legend_description">
								<?php _e( 'A placeholder to insert a conditional tag', DLP_PREFIX ) ?>.
								<?php _e( 'You can insert <em>only one</em> of the following conditional tag:', DLP_PREFIX ) ?><br />
								<p class="legend_conditional_tags_description">
									<i><?php _e( '##HAS_IMAGE##', DLP_PREFIX ) ?></i><br>
									<?php _e( 'A placeholder to check if logo has image', DLP_PREFIX ) ?>
								</p>
                                <p class="legend_conditional_tags_description">
                                    <i><?php _e( '##HAS_IMAGE_ALTERNATIVE##', DLP_PREFIX ) ?></i><br>
									<?php _e( 'A placeholder to check if logo has alternative image', DLP_PREFIX ) ?>
                                </p>
							</div>

                            <br />

							<b><i><?php _e( 'Default template', DLP_PREFIX ) ?></i></b>
							<p class="legend_description"><?php _e( 'The standard template used by the plugin for the daily logo HTML snippet', DLP_PREFIX ) ?></p>
							<div class="legend_code_container">
								<div class="legend_code">
									<code class="html plain">&lt;a href="##LINK##" title="##NAME##" target="##TARGET##" class="##CLASS##"&gt;</code><br />
									<code class="html plain">[?]##HAS_IMAGE##[?]&lt;img src="##IMAGE##" alt="##NAME##" /&gt;[:]##NAME##[;]</code><br />
									<code class="html plain">&lt;/a&gt;</code>
								</div>
							</div>

                            <br />

                            <b><i><?php _e( 'Examples', DLP_PREFIX ) ?></i></b>
                            <p class="legend_description"><?php _e( 'A "##HAS_IMAGE_ALTERNATIVE##" conditional tag example:', DLP_PREFIX ) ?></p>
                            <div class="legend_code_container">
                                <div class="legend_code">
                                    <code class="html plain">&lt;a href="##LINK##" title="##NAME##" target="##TARGET##" class="##CLASS##"&gt;</code><br />
                                    <code class="html plain">[?]##HAS_IMAGE_ALTERNATIVE##[?]&lt;img src="##IMAGE_ALTERNATIVE##" alt="##NAME##" /&gt;[:]&lt;img src="##IMAGE##" alt="##NAME##" /&gt;[;]</code><br />
                                    <code class="html plain">&lt;/a&gt;</code>
                                </div>
                            </div>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Extra info', DLP_PREFIX ) ?></th>
						<td>
							<b><i><?php _e( 'Shortcodes', DLP_PREFIX ) ?></i></b>
							<p class="legend_description"><?php _e( 'The plugin provides a shortcode for displaying a table of historical data showing used names, dates and images.', DLP_PREFIX ) ?></p>
							<div class="legend_code_container">
								<div class="legend_code">
									<code class="html plain">[daily_logo_history_table]</code>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>

			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save', DLP_PREFIX ) ?>" />
				<input type="submit" name="reset" id="reset" class="reset button-primary" value="<?php _e( 'Restore', DLP_PREFIX ) ?>" />
			</p>

		</form>

	</div>
<?php
}

/**
 * Render admin menu page options and fields
 */
function daily_logo_fields() {
	?>
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
                <th scope="col" id="name" class="manage-column label-column"><?php _e( 'Name', DLP_PREFIX ) ?></th>
                <th scope="col" id="date" class="manage-column date-column"><?php _e( 'Date (start => end)', DLP_PREFIX ) ?></th>
				<th scope="col" id="link" class="manage-column bool-column"><?php _e( 'Link', DLP_PREFIX ) ?></th>
				<th scope="col" id="image" class="manage-column label-column"><?php _e( 'Image', DLP_PREFIX ) ?></th>
				<th scope="col" id="image_alternative" class="manage-column label-column"><?php _e( 'Alternative image', DLP_PREFIX ) ?></th>
				<th scope="col" id="class" class="manage-column label-column"><?php _e( 'CSS Class', DLP_PREFIX ) ?></th>
				<th scope="col" id="options" class="manage-column options-column"><?php _e( 'Options', DLP_PREFIX ) ?></th>
			</tr>
		</thead>
		<tbody id="logo_rows">
            <?php
			// Get rows from database
            $rows = daily_logo_get_rows();

            // Show rows
            daily_logo_show_rows( $rows );
            ?>
		</tbody>
        <tfoot id="logo_rows_more">
            <tr>
                <th scope="col" id="logo_rows_more_button" class="manage-column more-column" colspan="7" data-text-default="<?php _e( 'Load more', DLP_PREFIX ) ?>"  data-text-loading="<?php _e( 'Loading...', DLP_PREFIX ) ?>"><?php _e( 'Load more', DLP_PREFIX ) ?></th>
            </tr>
        </tfoot>
	</table>
    <?php
}

/**
 * Show admin settings page
 */
function daily_logo_option_main_show() {
	daily_logo_fields();
}

/**
 * Save admin settings page
 */
function daily_logo_save_settings() {
	// Get settings options
	$options = get_option( DLP_OPTION_SETTINGS );

	// Create new class for settings options if empty
	if ( ! isset( $options ) || empty( $options ) ) $options = new stdClass();

	// Manage save actions
	if ( $_POST['action'] != "restore" ) {
		// Read post data
		if ( isset( $_POST[ 'template_without_logo' ] ) ) $template_without_logo = $title = wp_kses_post( $_POST[ 'template_without_logo' ] );
		else $template_without_logo = '';
		if ( isset( $_POST[ 'alternative_template_without_logo' ] ) ) $alternative_template_without_logo = $title = wp_kses_post( $_POST[ 'alternative_template_without_logo' ] );
		else $alternative_template_without_logo = '';
		if ( isset( $_POST[ 'template_with_logo' ] ) ) $template_with_logo = wp_kses_post( $_POST[ 'template_with_logo' ] );
		else $template_with_logo = '';
		if ( isset( $_POST[ 'alternative_template_with_logo' ] ) ) $alternative_template_with_logo = wp_kses_post( $_POST[ 'alternative_template_with_logo' ] );
		else $alternative_template_with_logo = '';

		// Save template
		$options->template_without_logo = $template_without_logo;
		$options->alternative_template_without_logo = $alternative_template_without_logo;
		$options->template_with_logo = $template_with_logo;
		$options->alternative_template_with_logo = $alternative_template_with_logo;
	}
	else {
		// Restore template
		$options->template_without_logo = DLP_STANDARD_TEMPLATE_DEFAULT;
		$options->alternative_template_without_logo = DLP_STANDARD_ALTERNATIVE_TEMPLATE_DEFAULT;
		$options->template_with_logo = DLP_TEMPLATE_DEFAULT;
		$options->alternative_template_with_logo = DLP_ALTERNATIVE_TEMPLATE_DEFAULT;
	}

	// Save options and reload admin settings page
	update_option( DLP_OPTION_SETTINGS, $options );
	header( "Location: admin.php?page=" . DLP_MENU_SETTINGS . "&updated=true" );
}
