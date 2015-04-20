//backend
jQuery(function () {
    if (!jQuery('#pn_html_buffer').length) {
        jQuery('body').append('<div id="pn_html_buffer" class="info_popup" style="display: none;"></div>');
    }


    jQuery.fn.life = function (types, data, fn) {
        jQuery(this.context).on(types, this.selector, data, fn);
        return this;
    };

    //+++

    jQuery('.options_meta_box_html_items').sortable();
    jQuery('#mdf_custom_popup_selected_filters').sortable();
    jQuery('#mdf_shotcode_taxonomies').sortable();
    jQuery('#mdf_shortcodes_sort_panel').sortable();

    //+++

    jQuery('#mdf_shortcode_cat').change(function () {
        var term_id = jQuery(this).val();
        jQuery('#mdf_custom_popup_selected_filters').html("");
        if (term_id == '-1') {
            return;
        }
        //+++
        jQuery(this).prop('disabled', 'disabled');
        var _this = this;
        mdf_show_stat_info_popup(lang_one_moment);
        var data = {
            term_id: term_id,
            action: "mdf_draw_shortcode_html_items"
        };
        jQuery.post(ajaxurl, data, function (html) {
            mdf_hide_stat_info_popup();
            jQuery(_this).prop('disabled', false);
            jQuery('#mdf_custom_popup_selected_filters').html(html);
            jQuery('#mdf_custom_popup_selected_filters').sortable();
            jQuery('.options_meta_box_html_items').sortable();
        });
    });

    //***

    jQuery(".mdf_hide_shortcode_html_block").life('click', function () {
        if (jQuery(this).is(":checked")) {
            jQuery(this).prev("input[type=hidden]").val(1);
            jQuery(this).parents('li').find('.options_meta_box_html_items').hide(200);
        } else {
            jQuery(this).prev("input[type=hidden]").val(0);
            jQuery(this).parents('li').find('.options_meta_box_html_items').show(200);
        }
    });

    jQuery(".mdf_shortcode_options").life('click', function () {
        if (jQuery(this).is(":checked")) {
            jQuery(this).prev("input[type=hidden]").val(1);
        } else {
            jQuery(this).prev("input[type=hidden]").val(0);
        }
    });
});


function mdf_show_stat_info_popup(text) {
    jQuery("#pn_html_buffer").text(text);
    jQuery("#pn_html_buffer").fadeTo(400, 0.9);
}


function mdf_hide_stat_info_popup() {
    window.setTimeout(function () {
        jQuery("#pn_html_buffer").fadeOut(400);
    }, 500);
}
