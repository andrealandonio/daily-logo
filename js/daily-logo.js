// Constants
var items = 20;
var page = 1;

jQuery(document).ready(function() {
    /**
     * Init uploader
     */
    jQuery('#image_button').click(function() {
        jQuery('#image_frame').val('image');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    jQuery('#image_reset').click(function() {
        jQuery('#image').val('');
        return false;
    });

    jQuery('#image_alternative_button').click(function() {
        jQuery('#image_frame').val('image_alternative');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    jQuery('#image_alternative_reset').click(function() {
        jQuery('#image_alternative').val('');
        return false;
    });

    window.send_to_editor = function(html) {
        var img_url = jQuery('img', html).attr('src');
        if (img_url == undefined || img_url == '') img_url = jQuery(html).attr('src');
        var img_frame = jQuery('#image_frame').val();

        jQuery('#' + img_frame).val(img_url);
        tb_remove();
    };

    /**
     * Init datetime picker
     */
    jQuery('.date').datepicker({
        dateFormat : 'yy-mm-dd'
    });

    /**
     * Save row
     */
    jQuery("#daily_logo_table").submit(function() {
        var ajax_url = "/wp-admin/admin-ajax.php";
        var form = jQuery('#daily_logo_table');

        // Validate form
        form.validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                date_start: {
                    required: true
                },
                link: {
                    url: true
                },
                image: {
                    url: true
                }
            },
            messages: {
                name: "Please enter a valid name",
                date_start: "Please enter a valid date (format: 'YYYY-MM-DD')",
                link: "Please enter a valid link",
                image: "Please enter a valid image"
            }
        });

        var is_form_valid = form.valid();
        if (is_form_valid) {
            var data = form.serializeArray();

            // Prepare post data
            data.push({
                name: 'action',
                value: 'daily_logo_save_row',
                type: 'post',
                dataType: 'json'
            });

            // Post data
            jQuery.post(ajax_url, data, function(response) {
                // Manage logo list
                jQuery('#logo_rows').hide().empty().append(response).fadeIn();

                // Clean form fields
                jQuery("#daily_logo_table").find('#id').val('');
                jQuery("#daily_logo_table").find('#name').val('');
                jQuery("#daily_logo_table").find('#date_start').val('');
                jQuery("#daily_logo_table").find('#date_hour_start').val(0);
                jQuery("#daily_logo_table").find('#date_minute_start').val(0);
                jQuery("#daily_logo_table").find('#date_end').val('');
                jQuery("#daily_logo_table").find('#date_hour_end').val(0);
                jQuery("#daily_logo_table").find('#date_minute_end').val(0);
                jQuery("#daily_logo_table").find('#link').val('');
                jQuery("#daily_logo_table").find('#target').prop('checked', false);
                jQuery("#daily_logo_table").find('#image').val('');
                jQuery("#daily_logo_table").find('#image_alternative').val('');
                jQuery("#daily_logo_table").find('#class').val('');

                // Restore load more button
                restore_load_daily_logos_button();
            });
        }

        return false;
    });

    /**
     * Save setting action
     */
    jQuery("#daily_logo_settings").submit(function() {
        var form = jQuery('#daily_logo_settings');

        // Validate form
        form.validate({
            rules: {
                template_without_logo: {
                    required: true
                },
                alternative_template_without_logo: {
                    required: true
                },
                template_with_logo: {
                    required: true
                },
                alternative_template_with_logo: {
                    required: true
                }
            },
            messages: {
                template_without_logo: "Please enter a valid template",
                alternative_template_without_logo: "Please enter a valid template",
                template_with_logo: "Please enter a valid template",
                alternative_template_with_logo: "Please enter a valid template"
            }
        });

        return form.valid();
    });

    /**
     * Reset action
     */
    jQuery('body').on('click', '.button-primary.reset', function(e) {
        jQuery('#action').val('restore');
    });

    /**
     * Use current site URL click management
     */
    jQuery('#use_current_site').click(function() {
        jQuery('#link').val('http://' + window.location.hostname + '/');
    });

    /**
     * Load more rows
     */
    jQuery('#logo_rows_more').on('click', function() {
        // Show loading
        var button = jQuery('#logo_rows_more_button');
        button.html(button.data('text-loading'));

        // Load rows
        load_daily_logos();
    });
});

/**
 * Modify row
 */
function modify_row(id) {
    var ajax_url = '/wp-admin/admin-ajax.php';
    var nonce = jQuery("#daily_logo_table").find('input#nonce').val();

    // Prepare post data
    var data = {
        name: 'action',
        action: 'daily_logo_get_row',
        type: 'post',
        dataType: 'json',
        id: id,
        nonce: nonce
    };

    // Post data
    jQuery.post(ajax_url, data, function(response) {
        // Fill data
        jQuery("#daily_logo_table").find('#id').val(response.id);
        jQuery("#daily_logo_table").find('#name').val(response.name);
        jQuery("#daily_logo_table").find('#date_start').val(response.year_start + '-' + format_digit_date(response.month_start) + '-' + format_digit_date(response.day_start));
        jQuery("#daily_logo_table").find('#date_hour_start').val(response.hour_start);
        jQuery("#daily_logo_table").find('#date_minute_start').val(response.minute_start);
        jQuery("#daily_logo_table").find('#date_end').val(response.year_end + '-' + format_digit_date(response.month_end) + '-' + format_digit_date(response.day_end));
        jQuery("#daily_logo_table").find('#date_hour_end').val(response.hour_end);
        jQuery("#daily_logo_table").find('#date_minute_end').val(response.minute_end);
        jQuery("#daily_logo_table").find('#link').val(response.link);
        jQuery("#daily_logo_table").find('#target').prop('checked',(response.target == 1));
        jQuery("#daily_logo_table").find('#image').val(response.image);
        jQuery("#daily_logo_table").find('#image_alternative').val(response.image_alternative);
        jQuery("#daily_logo_table").find('#class').val(response.class);

        // Restore load more button
        restore_load_daily_logos_button();

        // Scroll to top
        jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
    });
}

/**
 * Clone row
 */
function clone_row(id) {
    var ajax_url = '/wp-admin/admin-ajax.php';
    var nonce = jQuery("#daily_logo_table").find('input#nonce').val();

    // Prepare post data
    var data = {
        name: 'action',
        action: 'daily_logo_clone_row',
        type: 'post',
        dataType: 'json',
        id: id,
        nonce: nonce
    };

    // Post data
    jQuery.post(ajax_url, data, function(response) {
        // Manage logo list
        jQuery('#logo_rows').hide().empty().append(response).fadeIn();

        // Clean form fields
        jQuery("#daily_logo_table").find('#id').val('');
        jQuery("#daily_logo_table").find('#name').val('');
        jQuery("#daily_logo_table").find('#date_start').val('');
        jQuery("#daily_logo_table").find('#date_hour_start').val(0);
        jQuery("#daily_logo_table").find('#date_minute_start').val(0);
        jQuery("#daily_logo_table").find('#date_end').val('');
        jQuery("#daily_logo_table").find('#date_hour_end').val(0);
        jQuery("#daily_logo_table").find('#date_minute_end').val(0);
        jQuery("#daily_logo_table").find('#link').val('');
        jQuery("#daily_logo_table").find('#target').prop('checked', false);
        jQuery("#daily_logo_table").find('#image').val('');
        jQuery("#daily_logo_table").find('#image_alternative').val('');
        jQuery("#daily_logo_table").find('#class').val('');

        // Restore load more button
        restore_load_daily_logos_button();
    });
}

/**
 * Delete row
 */
 function delete_row(id) {
    var ajax_url = "/wp-admin/admin-ajax.php";
    var choice = confirm('Are you sure?');
    var nonce = jQuery("#daily_logo_table").find('input#nonce').val();

    if (choice == true) {
        // Prepare post data
        var data = {
            name: 'action',
            action: 'daily_logo_remove_row',
            type: 'post',
            dataType: 'json',
            id: id,
            nonce: nonce
        };

        // Post data
        jQuery.post(ajax_url, data, function(response) {
            // Manage logo list
            jQuery('#logo_rows').hide().empty().append(response).fadeIn();

            // Restore load more button
            restore_load_daily_logos_button();
        });
    }

    return false;
}

/**
 * Format digit date appending the leading 0
 *
 * @param value
 * @returns {string}
 */
function format_digit_date(value) {
    return value < 10 ? "0" + value : value;
}

/**
 * Load more logos
 *
 * @returns {boolean}
 */
function load_daily_logos() {
    var ajax_url = '/wp-admin/admin-ajax.php';

    jQuery.ajax({
        type: 'GET',
        dataType: 'html',
        url: ajax_url,
        data: 'page=' + (++page) + '&items=' + items + '&action=daily_logo_get_rows',
        success: function(data) {
            var $data = jQuery(data);
            var button = jQuery('#logo_rows_more_button');

            if ($data.length) {
                jQuery('#logo_rows').append($data);
                button.html(button.data('text-default'));
            }
            else {
                jQuery('#logo_rows_more').hide();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            jQuery('#logo_rows_more_button').html('Error');
        }
    });

    return false;
}

/**
 * Restore load more button
 */
function restore_load_daily_logos_button() {
    // Reset page count
    page = 1;

    // Set default text
    var button = jQuery('#logo_rows_more_button');
    button.html(button.data('text-default'));

    // Show button
    jQuery('#logo_rows_more').show();
}