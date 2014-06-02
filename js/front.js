var mdf_range_update = false;
jQuery(function() {
    if (!jQuery('#pn_html_buffer').length) {
        jQuery('body').append('<div id="pn_html_buffer" class="mdf_info_popup" style="display: none;"></div>');
        jQuery('body').append('<div id="pn_html_buffer2" style="display: none;"></div>');
    }
    //+++

    mdf_hide_empty_blocks_titles();

    //***
    if (post_features_panel_auto == 1) {
        var mdf_title_data = jQuery('.mdf_title_data');
        jQuery.each(mdf_title_data, function(index, value) {
            var clone = jQuery(value).clone();
            jQuery(value).parents(':eq(' + under_title_out + ')').after(clone);
            jQuery(value).remove();
        });
    }
    jQuery('.mdf_title_data').show();

    //+++
    mdf_tooltip_init();
    mdf_init_checkboxes_scroll();
    mdf_init_selects();
    //+++

    jQuery('.mdf_range_min').life('change', function() {
        var slider_id = jQuery(this).data('slider-id');
        mdf_range_update = true;
        jQuery("#" + slider_id).ionRangeSlider("update", {
            from: parseInt(jQuery(this).val(), 10)
        });
        mdf_range_update = false;
        //jQuery("#" + slider_id).slider("values", 0, parseInt(jQuery(this).val(), 10));
    });


    jQuery('.mdf_range_max').life('change', function() {
        var slider_id = jQuery(this).data('slider-id');
        mdf_range_update = true;
        jQuery("#" + slider_id).ionRangeSlider("update", {
            to: parseInt(jQuery(this).val(), 10)
        });
        mdf_range_update = false;
        //jQuery("#" + slider_id).slider("values", 1, parseInt(jQuery(this).val(), 10));
    });
    //css selects
    mdf_init_selects();
    //work with taxonomy
    //select
    jQuery('.mdf_taxonomy').life('change', function() {
        mdf_deinit_chosen_selects();
        var tax_name = jQuery(this).data('tax-name');
        if (tax_name == 'post_tag') {
            //return true;
        }
        //+++
        jQuery(this).next('.mdf_taxonomy_child_container').show(200);
        var _this = this;
        var is_auto_submit = jQuery(this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit');
        var slug = jQuery(this).parents('form').find('input[name="mdf[mdf_widget_options][slug]"]').val();
        var form = jQuery(this).parents('form');
        var data = {
            action: "mdf_draw_term_childs",
            type: 'select',
            tax_name: tax_name,
            mdf_parent_id: jQuery(this).val(),
            hide: jQuery(this).data('hide'),
            page_mdf: jQuery(this).parents('form').find('.hidden_page_mdf_for_ajax').val(),
            meta_data_filter_cat: jQuery(this).parents('form').find('input[name="mdf[mdf_widget_options][meta_data_filter_cat]"]').val(),
            slug: slug,
            is_auto_submit: is_auto_submit
        };
        jQuery.post(ajaxurl, data, function(content) {
            if (is_auto_submit) {
                jQuery(_this).next('.mdf_taxonomy_child_container').hide();
            }
            jQuery(_this).next('.mdf_taxonomy_child_container').html(content);
            if (!content) {
                jQuery(_this).next('.mdf_taxonomy_child_container').hide().html(mdf_tax_loader);
            }
            if (jQuery(_this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit')) {
                jQuery(_this).parents('form').submit();
            }

            //ajax recount
            if (jQuery(form).hasClass('mdf_ajax_auto_recount')) {
                mdf_ajax_data_recount(jQuery(form).attr('id'), slug);
            }
        });

        return true;
    });

    //checkbox
    jQuery('.mdf_taxonomy_checkbox').life('click', function() {
        var tax_name = jQuery(this).data('tax-name');
        var is_auto_submit = jQuery(this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit');
        var form = jQuery(this).parents('form');
        if (!jQuery(this).hasClass('mdf_has_childs') && !jQuery(form).hasClass('mdf_ajax_auto_recount')) {
            if (is_auto_submit) {
                jQuery(this).parents('form').submit();
            }
            return true;
        }

        //+++
        if (tax_name == 'post_tag') {
            //return true;
        }
        //+++
        var _this = this;
        var term_id = jQuery(this).val();
        var slug = jQuery(this).parents('form').find('input[name="mdf[mdf_widget_options][slug]"]').val();
        //+++
        if (jQuery(this).is(":checked")) {
            jQuery(this).prev("input[type=hidden]").val(term_id);
            jQuery(_this).parent().find('.mdf_taxonomy_child_container').show(200);
            var data = {
                action: "mdf_draw_term_childs",
                type: 'checkbox',
                tax_name: tax_name,
                mdf_parent_id: term_id,
                hide: jQuery(this).data('hide'),
                page_mdf: jQuery(this).parents('form').find('.hidden_page_mdf_for_ajax').val(),
                meta_data_filter_cat: jQuery(this).parents('form').find('input[name="mdf[mdf_widget_options][meta_data_filter_cat]"]').val(),
                slug: slug,
                is_auto_submit: is_auto_submit
            };
            jQuery.post(ajaxurl, data, function(content) {
                if (is_auto_submit) {
                    jQuery(_this).parent().find('.mdf_taxonomy_child_container').hide();
                }
                jQuery(_this).parent().find('.mdf_taxonomy_child_container').html(content);
                if (!content) {
                    jQuery(_this).parent().find('.mdf_taxonomy_child_container').hide().html(mdf_tax_loader);
                }
                if (jQuery(_this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit')) {
                    jQuery(_this).parents('form').submit();
                }

                //ajax recount
                if (jQuery(form).hasClass('mdf_ajax_auto_recount')) {
                    mdf_ajax_data_recount(jQuery(form).attr('id'), slug);
                }
            });
        } else {
            jQuery(_this).parent().find('.mdf_taxonomy_child_container').hide().html(mdf_tax_loader);
            if (jQuery(this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit')) {
                jQuery(this).parents('form').submit();
            }
            //ajax recount
            if (jQuery(form).hasClass('mdf_ajax_auto_recount')) {
                mdf_ajax_data_recount(jQuery(form).attr('id'), slug);
            }
        }

        return true;
    });

    //+++
    //for shortcode
    try {
        jQuery('.mdf_shortcode_container .mdf_widget_found_count span').html(mdf_found_totally);
    } catch (e) {

    }

});



function mdf_draw_ui_slider_items(act_without_button, uniqid) {
    var items = jQuery(".ui_slider_item_" + uniqid);
    jQuery.each(items, function(key, item) {
        var input = jQuery(item).next('input');
        mdf_init_range_sliders(item, input, act_without_button, uniqid);
    });
}


function mdf_get_ui_slider_step(input) {
    var step = jQuery(input).data('step');
    if (!step) {
        step = Math.ceil(parseInt((jQuery(input).data('max') - jQuery(input).data('min')) / 100, 10));
    }
    return step;
}

function mdf_init_range_sliders(item, input, act_without_button, uniqid) {
    try {
        jQuery(item).ionRangeSlider({
            min: jQuery(input).data('min'),
            max: jQuery(input).data('max'),
            from: jQuery(input).data('min-now'),
            to: jQuery(input).data('max-now'),
            type: 'double',
            prefix: jQuery(input).data('slider-prefix'),
            postfix: jQuery(input).data('slider-postfix'),
            //maxPostfix: "+",
            prettify: jQuery(input).data('slider-prettify'),
            hideMinMax: false,
            hideFromTo: false,
            hasGrid: true,
            step: mdf_get_ui_slider_step(input),
            onFinish: function(ui) {

                jQuery(input).val(ui.fromNumber + '^' + ui.toNumber);
                jQuery(input).parent().find('.mdf_range .mdf_range_min').val(ui.fromNumber);
                jQuery(input).parent().find('.mdf_range .mdf_range_max').val(ui.toNumber);
                if (act_without_button) {
                    jQuery("#meta_data_filter_" + uniqid).submit();
                }

                //ajax recount
                if (jQuery("#meta_data_filter_" + uniqid).hasClass('mdf_ajax_auto_recount')) {
                    mdf_ajax_data_recount(jQuery("#meta_data_filter_" + uniqid).attr('id'), jQuery("#meta_data_filter_" + uniqid).data('slug'));
                }
                return false;
            },
            onChange: function(ui) {
                jQuery(input).val(ui.fromNumber + '^' + ui.toNumber);
                jQuery(input).parent().find('.mdf_range .mdf_range_min').val(ui.fromNumber);
                jQuery(input).parent().find('.mdf_range .mdf_range_max').val(ui.toNumber);
            },
            onLoad: function(ui) {
                if (mdf_range_update) {
                    jQuery(input).val(ui.fromNumber + '^' + ui.toNumber);
                    jQuery(input).parent().find('.mdf_range .mdf_range_min').val(ui.fromNumber);
                    jQuery(input).parent().find('.mdf_range .mdf_range_max').val(ui.toNumber);
                    if (act_without_button) {
                        jQuery("#meta_data_filter_" + uniqid).submit();
                    }

                    //ajax recount
                    if (jQuery("#meta_data_filter_" + uniqid).hasClass('mdf_ajax_auto_recount')) {
                        mdf_ajax_data_recount(jQuery("#meta_data_filter_" + uniqid).attr('id'), jQuery("#meta_data_filter_" + uniqid).data('slug'));
                    }
                    return false;
                }
            }
        });
    } catch (e) {
        /*
         
         jQuery(item).slider({
         min: jQuery(input).data('min'),
         max: jQuery(input).data('max'),
         values: [jQuery(input).data('min-now'), jQuery(input).data('max-now')],
         range: true,
         step: mdf_get_ui_slider_step(input),
         slide: function(event, ui) {
         jQuery(input).val(ui.values[0] + '^' + ui.values[1]);
         jQuery(input).parent().find('.mdf_range .mdf_range_min').val(ui.values[0]);
         jQuery(input).parent().find('.mdf_range .mdf_range_max').val(ui.values[1]);
         },
         change: function(event, ui) {
         jQuery(input).val(ui.values[0] + '^' + ui.values[1]);
         jQuery(input).parent().find('.mdf_range .mdf_range_min').val(ui.values[0]);
         jQuery(input).parent().find('.mdf_range .mdf_range_max').val(ui.values[1]);
         if (act_without_button) {
         jQuery("#meta_data_filter_" + uniqid).submit();
         }
         
         //ajax recount
         if (jQuery("#meta_data_filter_" + uniqid).hasClass('mdf_ajax_auto_recount')) {
         mdf_ajax_data_recount(jQuery("#meta_data_filter_" + uniqid).attr('id'), jQuery("#meta_data_filter_" + uniqid).data('slug'));
         }
         }
         });
         */
    }
}

function mdf_click_checkbox(_this) {
    if (jQuery(_this).is(":checked")) {
        jQuery(_this).prev("input[type=hidden]").val(1);
        jQuery(_this).next("input[type=hidden]").val(1);
        jQuery(_this).val(1);
    } else {
        jQuery(_this).prev("input[type=hidden]").val('~');
        jQuery(_this).next("input[type=hidden]").val('~');
        jQuery(_this).val('~');
    }

    return true;
}


function mdf_init_search_form(uniqid, slug, search_url, act_without_button, ajax_searching) {

    if (act_without_button === 1) {
        //checkbox actions
        jQuery("#meta_data_filter_" + uniqid + " .mdf_option_checkbox").life('click', function() {
            mdf_click_checkbox(this);
            jQuery("#meta_data_filter_" + uniqid).submit();
            return true;
        });
        //select actions
        jQuery("#meta_data_filter_" + uniqid + " .mdf_filter_select").life('change', function() {
            jQuery("#meta_data_filter_" + uniqid).submit();
            return true;
        });
    } else {
        jQuery("#meta_data_filter_" + uniqid + " .mdf_option_checkbox").unbind('click');
        jQuery("#meta_data_filter_" + uniqid + " .mdf_option_checkbox").life('click', function() {
            mdf_click_checkbox(this);
            //recount items count by ajax
            if (ajax_searching) {
                mdf_ajax_data_recount("meta_data_filter_" + uniqid, slug);
            }
        });
        jQuery("#meta_data_filter_" + uniqid + " .mdf_filter_select").unbind('change');
        jQuery("#meta_data_filter_" + uniqid + " .mdf_filter_select").life('change', function() {
            //recount items count by ajax
            if (ajax_searching) {
                mdf_ajax_data_recount("meta_data_filter_" + uniqid, slug);
            }
        });
    }

    //+++
    mdf_draw_ui_slider_items(act_without_button, uniqid);
    //+++

    mdf_init_submit_button(uniqid, slug, search_url);

}

function mdf_init_submit_button(uniqid, slug, search_url) {
   
    var submit_mode = 'submit';
    jQuery('#meta_data_filter_' + uniqid + ' .mdf_reset_button').click(function() {
        submit_mode = 'reset';
        jQuery("#meta_data_filter_" + uniqid).submit();
        return false;
    });

    var form_id = "meta_data_filter_" + uniqid;

    var is_ajaxed_reset = false;
    //check is form inserted in popup
    var is_in_popup = false;
    if (jQuery(this).parents('.advanced_wp_popup_content')) {
        is_in_popup = true;
        is_ajaxed_reset = true;
    }
    //***
    var type = 'widget';
    var shortcode_id = 0;
    var widget_id = 0;
    var sidebar_name = "";
    var sidebar_id = 0;

    if (jQuery("#" + form_id).hasClass('mdf_shortcode_form')) {
        type = 'shortcode';
        shortcode_id = jQuery("#" + form_id).data('shortcode-id');
    }

    if (type == 'widget') {
        sidebar_name = jQuery("#" + form_id).data('sidebar-name');
        sidebar_id = jQuery("#" + form_id).data('sidebar-id');
        widget_id = jQuery("#" + form_id).data('widget-id');
    }


    jQuery("#meta_data_filter_" + uniqid).submit(function() {
        jQuery(this).find("input[type='submit'], .mdf_reset_button").replaceWith(mdf_tax_loader);
        jQuery("#meta_data_filter_" + uniqid + " .mdf_one_moment_txt span").show();

        var mdf_widget_search_url = search_url + "slg=" + slug + "&";

        var data = {
            action: "mdf_encode_search_get_params",
            vars: jQuery(this).serialize(),
            mode: submit_mode,
            mdf_front_qtrans_lang: mdf_front_qtrans_lang,
            type: type,
            shortcode_id: shortcode_id,
            sidebar_id: sidebar_id,
            sidebar_name: sidebar_name,
            widget_id: widget_id,
            is_ajaxed_reset: is_ajaxed_reset
        };
        jQuery.post(ajaxurl, data, function(response) {
            if (is_ajaxed_reset && submit_mode == 'reset' && type == 'shortcode') {
                jQuery("#meta_data_filter_" + uniqid).parents('.mdf_shortcode_container').replaceWith(response);
                mdf_init_selects();
                //mdf_ajax_data_recount(form_id, slug);
            } else {
                if (mdf_widget_search_url.substring(0, 4) == 'self') {
                    mdf_widget_search_url = mdf_widget_search_url.replace('self', (mdf_current_page_url.length>0 ? mdf_current_page_url : window.location.href));
                }

                if (mdf_widget_search_url.match(/\?/g).length > 1) {
                    var index = mdf_widget_search_url.lastIndexOf('?');
                    mdf_widget_search_url = mdf_widget_search_url.substr(0, index) + '&' + mdf_widget_search_url.substr(index + 1);

                }
                //only for project TODO
                //mdf_widget_search_url = mdf_widget_search_url.replace("#butique_woo_products", "");
                window.location = mdf_widget_search_url + response;
            }

        });

        return false;
    });
}

var mdf_ajax_lock = false;//remove twice ajax request on the same time
function mdf_ajax_data_recount(form_id, slug) {
    if (mdf_ajax_lock) {
        return;
    }
    mdf_ajax_lock = true;
    //+++
    mdf_show_stat_info_popup(lang_one_moment);
    var type = 'widget';
    var shortcode_id = 0;
    var widget_id = 0;
    var sidebar_name = "";
    var sidebar_id = 0;

    if (jQuery("#" + form_id).hasClass('mdf_shortcode_form')) {
        type = 'shortcode';
        shortcode_id = jQuery("#" + form_id).data('shortcode-id');
    }

    if (type == 'widget') {
        sidebar_id = jQuery("#" + form_id).data('sidebar-id');
        sidebar_name = jQuery("#" + form_id).data('sidebar-name');
        widget_id = jQuery("#" + form_id).data('widget-id');
    }

    var data = {
        action: "mdf_get_ajax_auto_recount_data",
        vars: jQuery("#" + form_id).serialize(),
        slug: slug,
        type: type,
        shortcode_id: shortcode_id,
        sidebar_id: sidebar_id,
        sidebar_name: sidebar_name,
        widget_id: widget_id,
        mode: 'submit',
        mdf_front_qtrans_lang: mdf_front_qtrans_lang,
        mdf_front_wpml_lang: mdf_front_wpml_lang
    };
    jQuery.post(ajaxurl, data, function(response) {
        mdf_hide_stat_info_popup();
        if (type == 'shortcode') {
            jQuery("#" + form_id).parents('.mdf_shortcode_container').replaceWith(response);
        } else {
            jQuery('#pn_html_buffer2').html(response);
            var widget = jQuery('#pn_html_buffer2').find('.widget-meta-data-filter').clone();
            //jQuery("#" + form_id).parents('.widget-meta-data-filter').replaceWith(widget);
            jQuery("#" + form_id).parents('.widget-meta-data-filter').replaceWith(response);
            jQuery('#pn_html_buffer2').html("");
            mdf_draw_ui_slider_items(false, jQuery(widget).find('form').data('unique-id'));

            mdf_hide_empty_blocks_titles();
            mdf_init_submit_button(jQuery(widget).find('form').data('unique-id'), slug, jQuery(widget).find('form').data('search-url'));

        }
        mdf_tooltip_init();
        mdf_init_checkboxes_scroll();
        mdf_init_selects();
        mdf_ajax_lock = false;
    });
}


function mdf_hide_empty_blocks_titles() {
    var section = jQuery('.widget-meta-data-filter .mdf_filter_section');
    jQuery.each(section, function(index, value) {
        var count = jQuery(value).find('table').find('tr').size();
        if (!count) {
            jQuery(value).hide();
            jQuery(value).find('table').hide();
            jQuery(value).prev('h4.data-filter-section-title').hide();
        }
    });
}

function mdf_tooltip_init() {
    try {
        jQuery('.mdf_tooltip').tooltipster({
            maxWidth: tooltip_max_width,
            //iconDesktop:true,
            animation: 'fade',
            delay: 200,
            theme: 'tooltipster-' + mdf_tooltip_theme,
            touchDevices: false,
            trigger: 'hover',
            contentAsHTML: true
                    //content: jQuery('<span><strong>' + jQuery(this).find('i').html() + '</strong></span>')
        });
    } catch (e) {
        console.log(e);
    }
}


function mdf_init_checkboxes_scroll() {
    try {
        jQuery(".mdf_tax_filter_section, .mdf_filter_section").mCustomScrollbar('destroy');
        jQuery(".mdf_tax_filter_section, .mdf_filter_section").mCustomScrollbar({
            scrollButtons: {
                enable: true
            },
            advanced: {
                updateOnContentResize: true,
                updateOnBrowserResize: true
            },
            theme: "dark-2",
            horizontalScroll: false,
            mouseWheel: true,
            scrollType: 'pixels',
            contentTouchScroll: true
        });
    } catch (e) {
        console.log(e);
    }
}

//by chosen js
function mdf_init_selects() {
    mdf_deinit_chosen_selects();
/*
    if (mdf_use_chosen_js_w) {
        jQuery(".mdf_widget_form select").chosen();
    }
    if (mdf_use_chosen_js_s) {
        jQuery(".mdf_shortcode_container select").chosen();
    }
*/
}

function mdf_deinit_chosen_selects() {
    try {

        if (mdf_use_chosen_js_w) {
            jQuery(".mdf_widget_form select").chosen('destroy').trigger("liszt:updated");
        }
        if (mdf_use_chosen_js_s) {
            jQuery(".mdf_shortcode_container select").chosen('destroy').trigger("liszt:updated");
        }

    } catch (e) {

    }

    //jQuery(".mdf_shortcode_form select, .mdf_widget_form select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
    //jQuery("*[class*=chzn]").remove();
}

function mdf_show_stat_info_popup(text) {
    jQuery("#pn_html_buffer").text(text);
    jQuery("#pn_html_buffer").fadeTo(200, 0.9);
}


function mdf_hide_stat_info_popup() {
    window.setTimeout(function() {
        jQuery("#pn_html_buffer").fadeOut(400);
    }, 500);
}

