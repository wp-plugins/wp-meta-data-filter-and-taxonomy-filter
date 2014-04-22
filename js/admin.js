jQuery(function() {

    if (!jQuery('#pn_html_buffer').length) {
        jQuery('body').append('<div id="pn_html_buffer" class="info_popup" style="display: none;"></div>');
    }

    //***
    try {
        jQuery("#data_group_items").sortable();
        jQuery('.data_item_select_options').sortable();

        //***

        jQuery(".add_item_to_data_group").click(function() {
            var add_to = jQuery(this).data('add-to');
            var data = {
                action: "meta_data_filter_add_item_to_data_group"
            };
            jQuery.post(ajaxurl, data, function(template) {
                if (add_to == 'top') {
                    jQuery('#data_group_items').prepend('<li class="admin-drag-holder">' + template + '</li>');
                } else {
                    jQuery('#data_group_items').append('<li class="admin-drag-holder">' + template + '</li>');
                }

                jQuery("#data_group_items").sortable();
            });

            return false;
        });

        jQuery(".delete_item_from_data_group").life('click', function() {
            jQuery(this).parent().hide(200, function() {
                jQuery(this).remove();
            });
            /*
             if (confirm('Sure?')) {
             var _this=this;
             var data = {
             action: "mdf_delete_html_item",
             post_id: mdf_current_post_id,
             meta_key: jQuery(_this).data('item-key')
             };
             jQuery.post(ajaxurl, data, function() {
             jQuery(_this).parent().hide(200, function() {
             jQuery(_this).remove();
             });
             });
             }
             */
            return false;
        });


        jQuery(".data_group_item_select").life('change', function() {
            var value = jQuery(this).val();
            jQuery(this).parents('li').find(".data_group_input_type").hide(333);
            jQuery(this).parents('li').find(".data_group_item_template_" + value).show(333);
        });


        jQuery(".add_option_to_data_item_select").life('click', function() {
            var index = jQuery(this).data('group-item-index');
            var template = '<input type="text" value="" name="html_item[' + index + '][select][]" />&nbsp;<a href="#" class="delete_option_from_data_item_select remove-button"></a><br />';
            var append = jQuery(this).data('append');
            if (append) {
                jQuery(this).parent().find('.data_item_select_options').append('<li>' + template + '</li>');
            } else {
                jQuery(this).parent().find('.data_item_select_options').prepend('<li>' + template + '</li>');
            }

            jQuery('.data_item_select_options').sortable();
            return false;
        });


        jQuery(".delete_option_from_data_item_select").life('click', function(e) {
            jQuery(this).parent().remove();
            return false;
        });

    } catch (e) {

    }

    jQuery('.meta_data_filter_sequence').change(function() {
        var data = {
            action: "meta_data_filter_set_sequence",
            post_id: jQuery(this).data('post-id'),
            sequence: jQuery(this).val()
        };
        jQuery.post(ajaxurl, data, function(template) {
            mdf_show_info_popup(lang_updated, 1000);
        });
    });

    //+++

    jQuery(".mdf_option_checkbox").life('click', function() {
        if (jQuery(this).is(":checked")) {
            jQuery(this).prev("input[type=hidden]").val(1);
            jQuery(this).next("input[type=hidden]").val(1);
            jQuery(this).val(1);
        } else {
            jQuery(this).prev("input[type=hidden]").val(0);
            jQuery(this).next("input[type=hidden]").val(0);
            jQuery(this).val(0);
        }
    });


    jQuery('#' + mdf_slug_cat).change(function() {
        mdf_set_post_category(this);
    });

    //change meta keys
    jQuery('.mdf_change_meta_key_butt').life('click', function() {
        var old_key = jQuery(this).data('old-key');
        var new_key = jQuery(this).prev('input[type=text]').val();
        var _this = this;
        //+++
        var data = {
            action: "mdf_change_meta_key",
            post_id: mdf_current_post_id,
            old_key: old_key,
            new_key: new_key
        };
        jQuery.post(ajaxurl, data, function(request) {
            request = jQuery.parseJSON(request);
            if (request.content.length > 0) {
                jQuery(_this).parents('.mdf_filter_item').html(request.content);
            }
            mdf_show_info_popup(request.notice, 5000);
        });

        return false;
    });
    //reflections
    jQuery('.mdf_is_reflected').life('click', function() {
        if (jQuery(this).is(":checked")) {
            jQuery(this).prev("input[type=hidden]").val(1);
            jQuery(this).next("input[type=text]").prop('disabled', false);
            jQuery(this).next("input[type=text]").prop('placeholder', 'enabled');
            jQuery(this).val(1);
        } else {
            jQuery(this).prev("input[type=hidden]").val(0);
            jQuery(this).next("input[type=text]").prop('disabled', true);
            jQuery(this).next("input[type=text]").prop('placeholder', 'disabled');
            jQuery(this).val(0);
        }

        return true;
    });

});

function mdf_show_info_popup(text, delay) {
    jQuery("#pn_html_buffer").text(text);
    jQuery("#pn_html_buffer").fadeTo(400, 0.9);
    window.setTimeout(function() {
        jQuery("#pn_html_buffer").fadeOut(400);
    }, delay);
}

function mdf_show_stat_info_popup(text) {
    jQuery("#pn_html_buffer").text(text);
    jQuery("#pn_html_buffer").fadeTo(400, 0.9);
}


function mdf_hide_stat_info_popup() {
    window.setTimeout(function() {
        jQuery("#pn_html_buffer").fadeOut(400);
    }, 500);
}

//to assign to post its filter category
function mdf_set_post_category(_this) {
    var cat_id = jQuery(_this).val();
    if (cat_id > 0) {
        var data = {
            action: "meta_data_filter_get_data_group_topage_items",
            cat_id: jQuery(_this).val()
        };
        jQuery.post(ajaxurl, data, function(html) {
            jQuery('#meta_data_filter_area').html(html);
        });
    } else {
        jQuery('#meta_data_filter_area').html('<input type="hidden" name="page_meta_data_filter" value="" />');
    }
}

