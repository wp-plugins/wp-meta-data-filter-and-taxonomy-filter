var mdf_range_update = false;
var mdf_ajax_content_redraw_page = 0;
try {
    var mdf_woo_catalog_order_by = mdf_default_order_by;
    var mdf_woo_catalog_order = mdf_default_order;
} catch (e) {

}
//***
jQuery(function () {
    jQuery.fn.life = function (types, data, fn) {
        jQuery(this.context).on(types, this.selector, data, fn);
        return this;
    };
    //+++
    mdf_init();
});
//***
function mdf_init() {

    if (!jQuery('#pn_html_buffer').length) {
        jQuery('body').append('<div id="pn_html_buffer" class="mdf_info_popup" style="display: none;"></div>');
        jQuery('body').append('<div id="pn_html_buffer2" style="display: none;"></div>');
    }
    //+++

    mdf_hide_empty_blocks_titles();

    //+++
    mdf_tooltip_init();
    mdf_init_checkboxes_scroll();
    mdf_init_selects();
    mdf_init_checkboxes();
    //+++

    jQuery('.mdf_range_min').life('change', function () {
        var slider_id = jQuery(this).data('slider-id');
        mdf_range_update = true;
        jQuery("#" + slider_id).ionRangeSlider("update", {
            from: parseFloat(jQuery(this).val(), 10)
        });
        mdf_range_update = false;
        mdf_ajax_content_redraw_page = 0;
        //jQuery("#" + slider_id).slider("values", 0, parseInt(jQuery(this).val(), 10));
    });


    jQuery('.mdf_range_max').life('change', function () {
        var slider_id = jQuery(this).data('slider-id');
        mdf_range_update = true;
        jQuery("#" + slider_id).ionRangeSlider("update", {
            to: parseFloat(jQuery(this).val(), 10)
        });
        mdf_range_update = false;
        mdf_ajax_content_redraw_page = 0;
        //jQuery("#" + slider_id).slider("values", 1, parseInt(jQuery(this).val(), 10));
    });
    //css selects
    mdf_init_selects();
    //work with taxonomy
    //select
    jQuery('.mdf_taxonomy').life('change', function () {
        mdf_deinit_chosen_selects();
        var tax_name = jQuery(this).data('tax-name');
        //+++
        jQuery(this).next('.mdf_taxonomy_child_container').show(200);
        var _this = this;
        var is_auto_submit = jQuery(this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit');
        var is_tax_ajax_autorecount = jQuery(this).parents('.mdf_input_container').hasClass('mdf_tax_ajax_autorecount');
        var slug = jQuery(this).parents('form').find('input[name="mdf[mdf_widget_options][slug]"]').val();
        var form = jQuery(this).parents('form');
        mdf_ajax_content_redraw_page = 0;
        var data = {
            action: "mdf_draw_term_childs",
            type: 'select',
            tax_name: tax_name,
            mdf_parent_id: jQuery(this).val(),
            hide: jQuery(this).data('hide'),
            page_mdf: jQuery(this).parents('form').find('.hidden_page_mdf_for_ajax').val(),
            mdf_cat: jQuery(this).parents('form').find('input[name="mdf[mdf_widget_options][meta_data_filter_cat]"]').val(),
            slug: slug,
            is_auto_submit: is_auto_submit
        };
        jQuery.post(ajaxurl, data, function (content) {
            if (is_auto_submit || is_tax_ajax_autorecount) {
                jQuery(_this).next('.mdf_taxonomy_child_container').hide();
            }

            //+++

            jQuery(_this).next('.mdf_taxonomy_child_container').html(content);

            if (jQuery(_this).next('.mdf_taxonomy_child_container').next('.mdf_taxonomy_child_container2').length) {
                jQuery(_this).next('.mdf_taxonomy_child_container').next('.mdf_taxonomy_child_container2').remove();
            } else {
                jQuery(_this).next('.mdf_taxonomy_child_container').next('.mdf_taxonomy_child_container').remove();
            }

            //+++

            if (!content) {
                jQuery(_this).next('.mdf_taxonomy_child_container').hide().html(mdf_tax_loader);
            }
            if (jQuery(_this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit')) {
                jQuery(_this).parents('form').submit();
            }

            //ajax recount
            if (jQuery(form).hasClass('mdf_ajax_auto_recount')) {
                mdf_ajax_data_recount(jQuery(form).attr('id'), slug, false);
            }
        });

        return true;
    });

    //checkbox
    jQuery('.mdf_taxonomy_checkbox').life('change', function () {
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
        var _this = this;
        var term_id = jQuery(this).val();
        var slug = jQuery(this).parents('form').find('input[name="mdf[mdf_widget_options][slug]"]').val();
        //+++
        mdf_ajax_content_redraw_page = 0;
        if (jQuery(this).is(":checked")) {
            jQuery(this).prev("input[type=hidden]").val(term_id);

            if (!show_tax_all_childs)
            {
                if (mdf_use_custom_icheck) {
                    jQuery(_this).parent().parent().find('.mdf_taxonomy_child_container').show(200);
                } else {
                    jQuery(_this).parent().find('.mdf_taxonomy_child_container').show(200);
                }

                var data = {
                    action: "mdf_draw_term_childs",
                    type: 'checkbox',
                    tax_name: tax_name,
                    mdf_parent_id: term_id,
                    hide: jQuery(this).data('hide'),
                    page_mdf: jQuery(this).parents('form').find('.hidden_page_mdf_for_ajax').val(),
                    mdf_cat: jQuery(this).parents('form').find('input[name="mdf[mdf_widget_options][meta_data_filter_cat]"]').val(),
                    slug: slug,
                    is_auto_submit: is_auto_submit
                };
                jQuery.post(ajaxurl, data, function (content) {
                    if (is_auto_submit) {
                        jQuery(_this).parent().find('.mdf_taxonomy_child_container').hide();
                    }

                    if (mdf_use_custom_icheck) {
                        jQuery(_this).parent().parent().find('.mdf_taxonomy_child_container').html(content);
                        mdf_init_checkboxes();
                    } else {
                        jQuery(_this).parent().find('.mdf_taxonomy_child_container').html(content);
                    }

                    if (!content) {
                        jQuery(_this).parent().find('.mdf_taxonomy_child_container').hide().html(mdf_tax_loader);
                    }
                    if (jQuery(_this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit')) {
                        jQuery(_this).parents('form').submit();
                    }

                    //ajax recount
                    if (jQuery(form).hasClass('mdf_ajax_auto_recount')) {
                        mdf_ajax_data_recount(jQuery(form).attr('id'), slug, false);
                    }
                });
            } else {
                if (jQuery(_this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit')) {
                    jQuery(_this).parents('form').submit();
                }

                //ajax recount
                if (jQuery(form).hasClass('mdf_ajax_auto_recount')) {
                    mdf_ajax_data_recount(jQuery(form).attr('id'), slug, false);
                }
            }
        } else {

            if (!show_tax_all_childs) {
                if (mdf_use_custom_icheck) {
                    jQuery(_this).parent().parent().find('.mdf_taxonomy_child_container').hide().html(mdf_tax_loader);
                } else {
                    jQuery(_this).parent().find('.mdf_taxonomy_child_container').hide().html(mdf_tax_loader);
                }
            }
            //***
            if (jQuery(this).parents('.mdf_input_container').hasClass('mdf_tax_auto_submit')) {
                jQuery(this).parents('form').submit();
            }
            //ajax recount
            if (jQuery(form).hasClass('mdf_ajax_auto_recount')) {
                mdf_ajax_data_recount(jQuery(form).attr('id'), slug, false);
            }
        }

        return true;
    });

    //+++
    //for shortcode
    try {
        //console.log(mdf_found_totally);
        jQuery('.mdf_widget_found_count span').html(mdf_found_totally);
    } catch (e) {

    }

    jQuery('.mdf_sort_panel_order_by').change(function () {
        var val = jQuery(this).val();
        if (val == 0) {
            val = mdf_default_order_by;
        }
        var href = window.location.href;
        if (mdf_is_search_going == 1) {
            window.location.href = href + '&order_by=' + val + '&order=' + mdf_sort_order;
        } else {
            mdf_util_order_no_search(mdf_sort_order, val);
            return false;
        }

    });


    jQuery('.mdf_sort_panel_ordering').change(function () {
        var val = jQuery(this).val();
        var href = window.location.href;
        if (mdf_is_search_going == 1) {
            window.location.href = href + '&order_by=' + mdf_order_by + '&order=' + val;
        } else {
            mdf_util_order_no_search(val, mdf_order_by);
            return false;
        }
    });

    jQuery('.mdf_custom_filter_panel a').click(function () {
        var href = window.location.href;
        if (mdf_is_search_going == 1) {
            window.location.href = href + '&order_by=' + jQuery(this).data('order-by') + '&order=' + jQuery(this).data('order');
        } else {
            mdf_util_order_no_search(jQuery(this).data('order'), jQuery(this).data('order-by'));
            return false;
        }
        return false;
    });


    //+++
    jQuery('.mdf_front_toggle').life('click', function () {
        var condition = jQuery(this).data('condition');
        if (condition == 'opened') {
            jQuery(this).removeClass('mdf_front_toggle_opened');
            jQuery(this).addClass('mdf_front_toggle_closed');
            jQuery(this).data('condition', 'closed');
            jQuery(this).text(mdf_toggle_open_sign);
            jQuery(this).parent().prev('.mdf_filter_post_blocks_toggles').val(2);
        } else {
            jQuery(this).addClass('mdf_front_toggle_opened');
            jQuery(this).removeClass('mdf_front_toggle_closed');
            jQuery(this).data('condition', 'opened');
            jQuery(this).text(mdf_toggle_close_sign);
            jQuery(this).parent().prev('.mdf_filter_post_blocks_toggles').val(1);
        }

        if (jQuery(this).parent().next('.mdf_filter_section, .mdf_tax_filter_section').hasClass('mdf_front_toggle_closed_section')) {
            jQuery(this).parent().next('.mdf_filter_section, .mdf_tax_filter_section').hide();
            jQuery(this).parent().next('.mdf_filter_section, .mdf_tax_filter_section').removeClass('mdf_front_toggle_closed_section');
        }
        jQuery(this).parent().next('.mdf_filter_section, .mdf_tax_filter_section').toggle(500);
        return false;
    });

    //activate submit button
    jQuery('.mdf_widget_form, .mdf_shortcode_form').find('input').life('click', function () {
        jQuery(this).parents('form').find("input[type=submit]").removeProp('disabled');
    });

    jQuery('.mdf_widget_form, .mdf_shortcode_form').find('input').life('change', function () {
        jQuery(this).parents('form').find("input[type=submit]").removeProp('disabled');
    });

    jQuery('.mdf_widget_form, .mdf_shortcode_form').find('select').life('change', function () {
        jQuery(this).parents('form').find("input[type=submit]").removeProp('disabled');
    });

    //+++
    jQuery('.mdf_filter_categories').life('change', function () {
        var term_id = jQuery(this).val();
        //for ajax searching
        if (jQuery(this).parents('.widget-meta-data-filter').find('form').hasClass('mdf_ajax_content_redraw')) {
            var s = jQuery('#mdf_results_by_ajax').data('shortcode');
            var old_mdf_cat = jQuery(this).parents('.widget-meta-data-filter').find('form').find('input[name="mdf[mdf_widget_options][meta_data_filter_cat]"]').val();
            s = s.replace('meta_data_filter_cat=' + old_mdf_cat, 'meta_data_filter_cat=' + term_id);
            jQuery('#mdf_results_by_ajax').data('shortcode', s);
        }
        jQuery(this).parents('.widget-meta-data-filter').find('form').find('input[name="mdf[mdf_widget_options][meta_data_filter_cat]"]').val(term_id);
        jQuery(this).parents('.widget-meta-data-filter').find('form').find('input[name="meta_data_filter_cat"]').val(term_id);

        mdf_ajax_data_recount(jQuery(this).parents('.widget-meta-data-filter').find('form').attr('id'), jQuery(this).data('slug'), true);
    });

    //fix for textinput if its under ajax redraw (another way redirect works)
    jQuery('form.mdf_ajax_content_redraw .mdf_textinput, form.mdf_ajax_auto_recount .mdf_textinput').keydown(function (e) {
        if (e.keyCode == 13) { // enter key was pressed
            jQuery(this).blur();
            return false; // prevent execution of rest of the script + event propagation / event bubbling + prevent default behaviour
        }
    });

    //+++ remove empty taxonomies selects
    remove_empty_mdf_taxonomy();
}

//if we do sort while no searching
function mdf_util_order_no_search(order, order_by) {
    mdf_tmp_order = order;
    mdf_tmp_orderby = order_by;
    var form = jQuery('.mdf_search_form').eq(0);
    if (form.hasClass('mdf_ajax_content_redraw')) {
        mdf_ajax_data_recount(form.attr('id'), form.data('slug'), false);
    } else {
        form.submit();
    }
}

//fixed 20-07-2014
function remove_empty_mdf_taxonomy() {
    jQuery.each(jQuery('.mdf_widget_form select.mdf_taxonomy'), function (index, select) {
        var len = jQuery(select).children('option').length;
        if (len < 2) {
            jQuery(select).parent().remove();
        }
    });
}

function mdf_draw_ui_slider_items(act_without_button, uniqid) {
    var items = jQuery(".ui_slider_item_" + uniqid);
    mdf_ajax_content_redraw_page = 0;
    jQuery.each(items, function (key, item) {
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
        var type = jQuery(input).data('type');
        //if (type == 'double') {
        jQuery(item).ionRangeSlider({
            min: jQuery(input).data('min'),
            max: jQuery(input).data('max'),
            from: jQuery(input).data('min-now'),
            to: jQuery(input).data('max-now'),
            type: type,
            prefix: jQuery(input).data('slider-prefix'),
            postfix: jQuery(input).data('slider-postfix'),
            //maxPostfix: "+",
            prettify: jQuery(input).data('slider-prettify'),
            hideMinMax: false,
            hideFromTo: false,
            hasGrid: true,
            step: mdf_get_ui_slider_step(input),
            onFinish: function (ui) {

                jQuery(input).val(ui.fromNumber + '^' + ui.toNumber);
                jQuery(input).parent().find('.mdf_range .mdf_range_min').val(ui.fromNumber);
                jQuery(input).parent().find('.mdf_range .mdf_range_max').val(ui.toNumber);
                if (act_without_button) {
                    jQuery("#meta_data_filter_" + uniqid).submit();
                }

                //ajax recount
                if (jQuery("#meta_data_filter_" + uniqid).hasClass('mdf_ajax_auto_recount')) {
                    mdf_ajax_data_recount(jQuery("#meta_data_filter_" + uniqid).attr('id'), jQuery("#meta_data_filter_" + uniqid).data('slug'), false);
                }
                return false;
            },
            onChange: function (ui) {
                jQuery(input).val(ui.fromNumber + '^' + ui.toNumber);
                jQuery(input).parent().find('.mdf_range .mdf_range_min').val(ui.fromNumber);
                jQuery(input).parent().find('.mdf_range .mdf_range_max').val(ui.toNumber);
                jQuery(input).parents('form').find("input[type=submit]").removeProp('disabled');
                mdf_ajax_content_redraw_page = 0;
            },
            onLoad: function (ui) {
                if (mdf_range_update) {
                    jQuery(input).val(ui.fromNumber + '^' + ui.toNumber);
                    jQuery(input).parent().find('.mdf_range .mdf_range_min').val(ui.fromNumber);
                    jQuery(input).parent().find('.mdf_range .mdf_range_max').val(ui.toNumber);
                    if (act_without_button) {
                        jQuery("#meta_data_filter_" + uniqid).submit();
                    }

                    //ajax recount
                    if (jQuery("#meta_data_filter_" + uniqid).hasClass('mdf_ajax_auto_recount')) {
                        mdf_ajax_data_recount(jQuery("#meta_data_filter_" + uniqid).attr('id'), jQuery("#meta_data_filter_" + uniqid).data('slug'), false);
                    }
                    return false;
                }
            }
        });
        //}
    } catch (e) {

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
        jQuery("#meta_data_filter_" + uniqid + " .mdf_option_checkbox").life('change', function () {
            mdf_click_checkbox(this);
            jQuery("#meta_data_filter_" + uniqid).submit();
            return true;
        });
        //select actions
        jQuery("#meta_data_filter_" + uniqid + " .mdf_filter_select").life('change', function () {
            jQuery("#meta_data_filter_" + uniqid).submit();
            return true;
        });
        //textinput
        jQuery("#meta_data_filter_" + uniqid + " .mdf_textinput").life('change', function () {
            jQuery("#meta_data_filter_" + uniqid).submit();
            return true;
        });
    } else {
        jQuery("#meta_data_filter_" + uniqid + " .mdf_option_checkbox").unbind('click');
        jQuery("#meta_data_filter_" + uniqid + " .mdf_option_checkbox").unbind('change');
        jQuery("#meta_data_filter_" + uniqid + " .mdf_option_checkbox").life('change', function () {
            mdf_click_checkbox(this);
            //recount items count by ajax
            if (ajax_searching) {
                mdf_ajax_data_recount("meta_data_filter_" + uniqid, slug, false);
            }
        });
        jQuery("#meta_data_filter_" + uniqid + " .mdf_filter_select").unbind('change');

        //recount items count by ajax
        jQuery("#meta_data_filter_" + uniqid + " .mdf_filter_select").life('change', function () {
            if (ajax_searching) {
                mdf_ajax_data_recount("meta_data_filter_" + uniqid, slug, false);
            }
        });
        jQuery("#meta_data_filter_" + uniqid + " .mdf_textinput").life('change', function () {
            if (ajax_searching) {
                mdf_ajax_data_recount("meta_data_filter_" + uniqid, slug, false);
            }
        });
        //***
        redraw_ajax_pagination(slug);
    }

    //+++
    mdf_draw_ui_slider_items(act_without_button, uniqid);
    //+++

    mdf_init_submit_button(uniqid, slug, search_url);
    //calendar
    mdf_init_calendars(uniqid, act_without_button, ajax_searching, slug);
}

function mdf_init_submit_button(uniqid, slug, search_url) {

    var submit_mode = 'submit';
    var type = 'widget';
    var form_id = "meta_data_filter_" + uniqid;
    if (jQuery("#" + form_id).hasClass('mdf_shortcode_form')) {
        type = 'shortcode';
        shortcode_id = jQuery("#" + form_id).data('shortcode-id');
    }

    //+++

    jQuery('#' + form_id + ' .mdf_reset_button').click(function () {

        submit_mode = 'reset';
        //if (type == 'widget') {
        if (jQuery("#" + form_id).hasClass('mdf_ajax_content_redraw')) {
            mdf_ajax_content_redraw_page = 0;
            mdf_ajax_data_recount(form_id, slug, true);
            return;
        }
        //}

        if (jQuery(this).attr('href') && !jQuery("#" + form_id).hasClass('mdf_ajax_content_redraw')) {
            var href = jQuery(this).attr('href');
            if (href.length > 1) {
                window.location = href;
                return;
            }
        }

        jQuery("#" + form_id).submit();
        return false;
    });



    var is_ajaxed_reset = false;
    //check is form inserted in popup
    var is_in_popup = false;
    if (jQuery(this).parents('.advanced_wp_popup_content')) {
        is_in_popup = true;
        is_ajaxed_reset = true;
    }
    //***

    var shortcode_id = 0;
    var widget_id = 0;
    var sidebar_name = "";
    var sidebar_id = 0;


    if (type == 'widget') {
        sidebar_name = jQuery("#" + form_id).data('sidebar-name');
        sidebar_id = jQuery("#" + form_id).data('sidebar-id');
        widget_id = jQuery("#" + form_id).data('widget-id');
    }




    jQuery("#" + form_id).submit(function () {
        jQuery(this).find("input[type='submit'], .mdf_reset_button").replaceWith(mdf_tax_loader);
        jQuery("#" + form_id + " .mdf_one_moment_txt span").show();

        var mdf_widget_search_url = search_url + "slg=" + slug + "&";

        var data = {
            action: "mdf_encode_search_get_params",
            vars: jQuery(this).serialize(),
            mode: submit_mode,
            mdf_front_qtrans_lang: mdf_front_qtrans_lang,
            mdf_front_wpml_lang: mdf_front_wpml_lang,
            type: type,
            shortcode_id: shortcode_id,
            sidebar_id: sidebar_id,
            sidebar_name: sidebar_name,
            widget_id: widget_id,
            is_ajaxed_reset: is_ajaxed_reset
        };
        jQuery.post(ajaxurl, data, function (response) {
            if (is_ajaxed_reset && submit_mode == 'reset' && type == 'shortcode') {
                //jQuery('#pn_html_buffer2').html(response);
                if (response.indexOf('mdf_ajax_content_redraw') != -1) {
                    //ajax content redraw
                    var parent = jQuery("#" + form_id).parent('.mdf_shortcode_container');
                    jQuery(parent).replaceWith('<div id="mdtf_replace_me"></div>');
                    jQuery('#mdtf_replace_me').html(response);
                    mdf_ajax_content_redraw_page = 0;
                    var f = jQuery('#mdtf_replace_me').find('.mdf_ajax_content_redraw').eq(0);
                    jQuery(f).parent().unwrap();
                    mdf_ajax_data_recount(jQuery(f).attr('id'), slug, false);
                } else {
                    jQuery("#" + form_id).parents('.mdf_shortcode_container').replaceWith(response);
                    mdf_init_selects();
                }
            } else {
                if (mdf_widget_search_url.substring(0, 4) == 'self') {
                    mdf_widget_search_url = mdf_widget_search_url.replace('self', (mdf_current_page_url.length > 0 ? mdf_current_page_url : window.location.href));
                }

                if (mdf_widget_search_url.match(/\?/g).length > 1) {
                    var index = mdf_widget_search_url.lastIndexOf('?');
                    mdf_widget_search_url = mdf_widget_search_url.substr(0, index) + '&' + mdf_widget_search_url.substr(index + 1);

                }
                //only for project TODO
                //mdf_widget_search_url = mdf_widget_search_url.replace("#butique_woo_products", "");
                var redirect_url = mdf_widget_search_url + response;
                if (mdf_front_wpml_lang.length) {
                    //redirect_url = mdf_site_url + '/' + mdf_front_wpml_lang + '/' + redirect_url;
                    //redirect_url = redirect_url.replace(mdf_site_url, mdf_site_url + '/' + mdf_front_wpml_lang + '/');
                }

                if (mdf_front_qtrans_lang.length) {
                    redirect_url = redirect_url + '&lang=' + mdf_front_qtrans_lang;
                }

                //ordering when sort panel is not while search going
                if (mdf_tmp_order != 0 && mdf_tmp_orderby != 0) {
                    redirect_url += '&order_by=' + mdf_tmp_orderby + '&order=' + mdf_tmp_order;
                }
                //+++
                window.location = redirect_url;
            }


        });

        return false;
    });
}

var mdf_ajax_lock = false;//remove twice ajax request on the same time
function mdf_ajax_data_recount(form_id, slug, simple_form_redraw) {
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

    //***
    var mdf_ajax_content_redraw = false;
    if (jQuery("#" + form_id).hasClass('mdf_ajax_content_redraw')) {
        mdf_ajax_content_redraw = true;
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
        mdf_current_term_id: mdf_current_term_id,
        mdf_current_tax: mdf_current_tax,
        simple_form_redraw: (simple_form_redraw ? 1 : 0), //need for filter-category changing
        mdf_front_qtrans_lang: mdf_front_qtrans_lang,
        mdf_front_wpml_lang: mdf_front_wpml_lang,
        mdf_ajax_content_redraw: mdf_ajax_content_redraw,
        shortcode_txt: jQuery('#mdf_results_by_ajax').data('shortcode'),
        content_redraw_page: mdf_ajax_content_redraw_page,
        mdf_tmp_order: mdf_tmp_order, //only when we do sort before searching
        mdf_tmp_orderby: mdf_tmp_orderby, //only when we do sort before searching
        mdf_is_search_going: mdf_is_search_going
    };
    mdf_is_search_going = 1;
    //+++
    if (mdf_ajax_content_redraw && jQuery('#mdf_results_by_ajax').length) {
        data.order_by = mdf_woo_catalog_order_by;
        data.order = mdf_woo_catalog_order;
    }
    //***
    jQuery.post(ajaxurl, data, function (response) {
        response = jQuery.parseJSON(response);
        //+++
        mdf_hide_stat_info_popup();
        if (type == 'shortcode') {
            jQuery("#" + form_id).parents('.mdf_shortcode_container').replaceWith(response.form);
        } else {
            jQuery('#pn_html_buffer2').html(response.form);
            var widget = jQuery('#pn_html_buffer2').find('.widget-meta-data-filter').clone();
            //jQuery("#" + form_id).parents('.widget-meta-data-filter').replaceWith(widget);
            jQuery("#" + form_id).parents('.widget-meta-data-filter').replaceWith(response.form);
            jQuery('#pn_html_buffer2').html("");
            mdf_draw_ui_slider_items(false, jQuery(widget).find('form').data('unique-id'));

            mdf_hide_empty_blocks_titles();
            mdf_init_submit_button(jQuery(widget).find('form').data('unique-id'), slug, jQuery(widget).find('form').data('search-url'));
            //+++
            //повторная перерисовка формы. после изменений в index.php#1034 ненужна
            /* fixed 20-07-2014
             if (simple_form_redraw) {
             mdf_ajax_lock = false;
             mdf_ajax_data_recount('meta_data_filter_' + jQuery(widget).find('form').data('unique-id'), slug, false);
             return;
             }
             */
        }
        remove_empty_mdf_taxonomy();
        mdf_tooltip_init();
        mdf_init_checkboxes_scroll();
        mdf_init_checkboxes();
        mdf_init_selects();
        mdf_ajax_lock = false;
        //next code row need when show disabled drop-downs - butaforia
        jQuery(".mdf_input_container").find('.mdf_taxonomy_child_container2').next('.mdf_taxonomy_child_container').remove();

        //redraw posts if we need do this by options
        if (mdf_ajax_content_redraw && jQuery('#mdf_results_by_ajax').length) {
            jQuery('#mdf_results_by_ajax').html(response.content);
            jQuery('.woo-pagination').remove();
            redraw_ajax_pagination(slug);
            //+++
            if (jQuery('.mdf_sort_panel_select').length) {
                jQuery('.mdf_sort_panel_order_by').change(function () {
                    var form_id = jQuery('.mdf_ajax_content_redraw.mdf_ajax_auto_recount').attr('id');
                    mdf_woo_catalog_order_by = jQuery(this).val();
                    mdf_ajax_data_recount(form_id, slug, false);
                });
                jQuery('.mdf_sort_panel_ordering').change(function () {
                    var form_id = jQuery('.mdf_ajax_content_redraw.mdf_ajax_auto_recount').attr('id');
                    mdf_woo_catalog_order = jQuery(this).val();
                    mdf_ajax_data_recount(form_id, slug, false);
                });
            }

            if (jQuery('.mdf_custom_filter_panel').length) {
                jQuery('.mdf_custom_filter_panel a').life('click', function () {
                    var form_id = jQuery('.mdf_ajax_content_redraw.mdf_ajax_auto_recount').attr('id');
                    mdf_woo_catalog_order_by = jQuery(this).data('order-by');
                    mdf_woo_catalog_order = jQuery(this).data('order');
                    mdf_ajax_data_recount(form_id, slug, false);
                    return false;
                });
            }
        }


        //fix for textinput if its under ajax redraw (another way redirect works)
        jQuery('form.mdf_ajax_content_redraw .mdf_textinput, form.mdf_ajax_auto_recount .mdf_textinput').keydown(function (e) {
            if (e.keyCode == 13) { // enter key was pressed
                jQuery(this).blur();
                return false; // prevent execution of rest of the script + event propagation / event bubbling + prevent default behaviour
            }
        });

    });
}

//for mdf_ajax_content_redraw mode
function redraw_ajax_pagination(slug) {
    var pagination_links = jQuery('#mdf_results_by_ajax .tw-pagination li a');
    jQuery.each(pagination_links, function (i, o) {
        jQuery(o).click(function () {
            mdf_ajax_content_redraw_page = parseInt(jQuery(o).attr('title'), 10);
            var form_id = jQuery('.mdf_ajax_content_redraw.mdf_ajax_auto_recount').attr('id');
            mdf_ajax_data_recount(form_id, slug, false);
            return false;
        });
    });
    //jump to top
    //document.getElementById('mdf_results_by_ajax').scrollIntoView();

    if (jQuery('#mdf_results_by_ajax').data('animate')) {
        var target = jQuery('#mdf_results_by_ajax').data('animate-target');
        if (jQuery(target).length)
        {
            var top = jQuery(target).offset().top;
            jQuery('html,body').animate({scrollTop: top}, 577);
            return false;
        }
    }

}


function mdf_hide_empty_blocks_titles() {
    var section = jQuery('.widget-meta-data-filter .mdf_filter_section');
    jQuery.each(section, function (index, value) {
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
        if (!mdf_use_custom_scroll_bar) {
            return;
        }
    } catch (e) {

    }
    //+++
    try {
        jQuery(".mdf_filter_section_scrolled").mCustomScrollbar('destroy');
        jQuery(".mdf_filter_section_scrolled").mCustomScrollbar({
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
    try {
        if (mdf_use_chosen_js_w) {
            jQuery(".mdf_widget_form select").chosen({disable_search_threshold: 10});
        }
        if (mdf_use_chosen_js_s) {
            jQuery(".mdf_shortcode_container select").chosen({disable_search_threshold: 10});
        }
    } catch (e) {

    }
}

//http://fronteed.com/iCheck/
function mdf_init_checkboxes() {
    try {
        if (mdf_use_custom_icheck == 0) {
            return;
        }

        //+++

        var selectors = ".mdf_shortcode_form input, .mdf_widget_form input";
        //jQuery(selectors).iCheck('destroy');

        jQuery(selectors).iCheck({
            checkboxClass: 'icheckbox_' + icheck_skin.skin + '-' + icheck_skin.color,
            radioClass: 'iradio_flat'
        });


        jQuery(selectors).on('ifChecked', function (event) {
            //jQuery(this).attr("checked", true);
            if (jQuery(this).hasClass('mdf_option_checkbox')) {
                jQuery(this).parent().prev("input[type=hidden]").val(1);
                jQuery(this).parent().next("input[type=hidden]").val(1);
                jQuery(this).val(1);
            }
            jQuery(this).trigger('change');
        });

        jQuery(selectors).on('ifUnchecked', function (event) {
            //jQuery(this).attr("checked", false);
            if (jQuery(this).hasClass('mdf_option_checkbox')) {
                jQuery(this).parent().prev("input[type=hidden]").val('~');
                jQuery(this).parent().next("input[type=hidden]").val('~');
                jQuery(this).val('~');
            }
            jQuery(this).trigger('change');
        });
    } catch (e) {

    }
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
    window.setTimeout(function () {
        jQuery("#pn_html_buffer").fadeOut(400);
    }, 500);
}

function mdf_init_calendars(uniqid, act_without_button, ajax_searching, slug) {
    try {
        jQuery("#meta_data_filter_" + uniqid + " .mdf_calendar").datepicker(
                {
                    showWeek: true,
                    firstDay: mdf_week_first_day,
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    onSelect: function (selectedDate, self) {
                        var date = new Date(parseInt(self.currentYear, 10), parseInt(self.currentMonth, 10), parseInt(self.currentDay, 10), 23, 59, 59);
                        var mktime = (date.getTime() / 1000);
                        var css_class = 'mdf_calendar_from';
                        if (jQuery(this).hasClass('mdf_calendar_from')) {
                            css_class = 'mdf_calendar_to';
                            jQuery(this).parent().find('.' + css_class).datepicker("option", "minDate", selectedDate);
                        } else {
                            jQuery(this).parent().find('.' + css_class).datepicker("option", "maxDate", selectedDate);
                        }
                        jQuery(this).prev('input[type=hidden]').val(mktime);

                        //+++

                        if (ajax_searching) {
                            window.setTimeout(function () {
                                mdf_ajax_data_recount("meta_data_filter_" + uniqid, slug, false);
                            }, 300);
                        }

                        if (act_without_button) {
                            window.setTimeout(function () {
                                jQuery("#meta_data_filter_" + uniqid).submit();
                            }, 300);
                        }

                    }
                }
        );
        jQuery(".mdf_calendar").datepicker("option", "dateFormat", mdf_calendar_date_format);
        jQuery(".mdf_calendar").datepicker("option", "showAnim", 'fadeIn');
        //+++
        jQuery(".mdf_calendar").life('keyup', function (e) {
            if (e.keyCode == 8 || e.keyCode == 46) {
                jQuery.datepicker._clearDate(this);
                jQuery(this).prev('input[type=hidden]').val("");
            }
        });
        //+++
        jQuery(".mdf_calendar").each(function () {
            var mktime = parseInt(jQuery(this).prev('input[type=hidden]').val(), 10);
            if (mktime > 0) {
                var date = new Date(mktime * 1000);
                jQuery(this).datepicker('setDate', new Date(date));
                //+++
                var css_class = 'mdf_calendar_from';
                var selectedDate = jQuery(this).datepicker('getDate');
                if (jQuery(this).hasClass('mdf_calendar_from')) {
                    css_class = 'mdf_calendar_to';
                    jQuery(this).parent().find('.' + css_class).datepicker("option", "minDate", selectedDate);
                } else {
                    jQuery(this).parent().find('.' + css_class).datepicker("option", "maxDate", selectedDate);
                }
            }
        });
    } catch (e) {

    }
}

