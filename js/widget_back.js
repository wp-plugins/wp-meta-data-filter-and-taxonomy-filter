var mdf_popup_terms_hidden = [];//for working with terms popup data with hidden data
var mdf_popup_terms_show_how = 'select';
var mdf_popup_terms_select_size = 1;
var mdf_popup_terms_show_child_terms = 0;
var mdf_popup_terms_section_toggles = 0;
var mdf_popup_terms_tax_title = '';
var mdf_popup_checkbox_max_height = 0;
//+++
jQuery(function () {
    jQuery.fn.life = function (types, data, fn) {
        jQuery(this.context).on(types, this.selector, data, fn);
        return this;
    };

    jQuery(".mdf_delete_filter_item").life('click', function (e) {
        jQuery(this).parent().remove();
        return false;
    });

    if (!jQuery('#pn_html_buffer').length) {
        jQuery('body').append('<div id="pn_html_buffer" class="info_popup" style="display: none;"></div>');
    }

    jQuery('.meta_data_filter_tax_ul a.mdf_tax_options').life('click', function () {
        var tax_name = jQuery(this).data('tax-name');
        var id_hide = jQuery(this).data('hide');
        var id_show_how = jQuery(this).data('show-how');
        var id_select_size = jQuery(this).data('select-size');
        var name_show_child_terms = jQuery(this).data('show-child-terms');
        var name_section_toggle = jQuery(this).data('terms-section-toggle');
        var id_tax_title = jQuery(this).data('select-title');
        var id_checkbox_max_height = jQuery(this).data('checkbox-max-height');
        mdf_popup_terms_hidden = jQuery('#' + id_hide).val().split(',');
        mdf_popup_terms_show_how = jQuery('#' + id_show_how).val();
        mdf_popup_terms_select_size = jQuery('#' + id_select_size).val();
        mdf_popup_terms_show_child_terms = jQuery('input[name="' + name_show_child_terms + '"]').val();
        mdf_popup_terms_section_toggles = jQuery('input[name="' + name_section_toggle + '"]').val();
        mdf_popup_terms_tax_title = jQuery('#' + id_tax_title).val();
        mdf_popup_checkbox_max_height = jQuery('#' + id_checkbox_max_height).val();

        var widget = jQuery(this).closest('div.widget');
        //+++
        mdf_show_stat_info_popup(mdf_lang_loading);
        var data = {
            action: "mdf_get_tax_options_in_widget",
            tax_name: tax_name,
            hidden_terms: jQuery('#' + id_hide).val(),
            show_how: mdf_popup_terms_show_how,
            select_size: mdf_popup_terms_select_size,
            show_child_terms: mdf_popup_terms_show_child_terms,
            terms_section_toggle: mdf_popup_terms_section_toggles,
            tax_title: mdf_popup_terms_tax_title,
            checkbox_max_height: mdf_popup_checkbox_max_height
        };
        jQuery.post(ajaxurl, data, function (html) {
            mdf_hide_stat_info_popup();
            var popup_params = {
                content: html,
                title: tax_name,
                overlay: true,
                open: function () {
                    //***
                },
                buttons: {
                    0: {
                        name: mdf_lang_apply,
                        action: function (__self) {
                            jQuery('#' + id_hide).val(mdf_popup_terms_hidden.join(','));
                            jQuery('#' + id_show_how).val(mdf_popup_terms_show_how);
                            jQuery('#' + id_select_size).val(mdf_popup_terms_select_size);
                            jQuery('input[name="' + name_show_child_terms + '"]').val(mdf_popup_terms_show_child_terms);
                            jQuery('input[name="' + name_section_toggle + '"]').val(mdf_popup_terms_section_toggles);
                            console.log(mdf_popup_terms_section_toggles);
                            jQuery('#' + id_tax_title).val(mdf_popup_terms_tax_title);
                            jQuery('#' + id_checkbox_max_height).val(mdf_popup_checkbox_max_height);
                            mdf_popup_terms_hidden = [];
                            mdf_popup_terms_show_how = '';
                            mdf_popup_terms_select_size = 1;
                            mdf_popup_terms_show_child_terms = 0;
                            mdf_popup_terms_section_toggles = 0;
                            mdf_popup_terms_tax_title = '',
                                    mdf_popup_checkbox_max_height = 0;
                            wpWidgets.save(widget, 0, 1, 0);
                        },
                        close: true
                    },
                    1: {
                        name: mdf_lang_close,
                        action: 'close'
                    }
                }
            };
            pn_advanced_wp_popup_mdf.popup(popup_params);
        });

        return false;
    });
    //+++work in popup terms list
    jQuery('.mdf_popup_terms_checkbox').life('click', function () {
        if (jQuery(this).is(":checked")) {
            mdf_popup_terms_hidden.push(parseInt(jQuery(this).data('term-id'), 10));
            jQuery(this).closest('li').find('ul').eq(0).hide();
        } else {
            mdf_popup_terms_hidden.splice(mdf_popup_terms_hidden.indexOf(parseInt(jQuery(this).data('term-id')), 10), 1);
            jQuery(this).closest('li').find('ul').eq(0).show();
        }

        return true;
    });

    jQuery('.mdf_popup_terms_show_how').life('change', function () {
        mdf_popup_terms_show_how = jQuery(this).val();
    });

    jQuery('.mdf_popup_terms_select_size').life('change', function () {
        mdf_popup_terms_select_size = jQuery(this).val();
    });

    jQuery('.mdf_popup_terms_show_child_terms').life('change', function () {
        mdf_popup_terms_show_child_terms = jQuery(this).val();
    });

    jQuery('.mdf_popup_terms_section_toggles').life('change', function () {
        mdf_popup_terms_section_toggles = jQuery(this).val();
    });

    jQuery('.mdf_popup_terms_tax_title').life('change', function () {
        mdf_popup_terms_tax_title = jQuery(this).val();
    });

    jQuery('.mdf_popup_terms_checkbox_max_height').life('change', function () {
        mdf_popup_checkbox_max_height = jQuery(this).val();
    });



});

function mdf_add_filter_item_to_widget(unique_id, field_name, select_cat_id) {
    var data = {
        action: "mdf_add_filter_item_to_widget",
        field_name: field_name,
        filter_cat: jQuery('#' + select_cat_id).val()
    };
    jQuery.post(ajaxurl, data, function (select) {
        if (jQuery.trim(select).length > 1) {
            jQuery('#meta_data_filter_ul_' + unique_id).append('<li>' + select + '</li>');
            jQuery('#meta_data_filter_ul_' + unique_id).sortable();
        } else {
            alert(lang_no_ui_sliders);
        }
    });

    return false;
}


function mdf_show_stat_info_popup(text) {
    jQuery("#pn_html_buffer").text(text);
    jQuery("#pn_html_buffer").fadeTo(400, 0.9);
}


function mdf_hide_stat_info_popup() {
    window.setTimeout(function () {
        jQuery("#pn_html_buffer").fadeOut(400);
    }, 500);
}



