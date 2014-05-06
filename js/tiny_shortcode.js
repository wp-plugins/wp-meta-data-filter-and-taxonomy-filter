/*
jQuery(function() {

    tinymce.create("tinymce.plugins.mdf_tiny_shortcodes",
            {
                _self: null,
                sc_info: {},
                init: function(ed, url)
                {
                    _self = this;

                    ed.addCommand("mdf_shortcode_tiny_popup", function(a, params)
                    {


                        console.log('mdf_shortcode_tiny_popup');



                    });

                },
                get_active_editor: function() {
                    return tinyMCE.activeEditor.editorId;
                },
                createControl: function(btn, e)
                {
                    if (btn == "meta-data-filter")
                    {
                        var a = this;

                        //tinymce button adding
                        btn = e.createMenuButton("pn_tinymce_button",
                                {
                                    title: 'Wordpress Meta Data & Taxonomies Filter',
                                    image: mdf_plugin_url + "images/shortcode_icon.png",
                                    icons: false,
                                    onclick: function() {

                                        if (!jQuery('#pn_html_buffer').length) {
                                            jQuery('body').append('<div id="pn_html_buffer" class="info_popup" style="display: none;"></div>');
                                        }

                                        mdf_show_stat_info_popup(lang_one_moment);

                                        var data = {
                                            action: "mdf_init_custom_shortcode_popup"
                                        };
                                        jQuery.post(ajaxurl, data, function(html) {

                                            mdf_hide_stat_info_popup();

                                            var popup_params = {
                                                content: html,
                                                title: 'Wordpress Meta Data & Taxonomies Filter',
                                                overlay: true,
                                                open: function() {
                                                    //***
                                                },
                                                buttons: {
                                                    0: {
                                                        name: 'Apply',
                                                        action: function(__self) {
                                                            var shortcode = jQuery("#pn_html_buffer").html();
                                                            var editor = _self.get_active_editor();

                                                            if (window.tinyMCE) {
                                                                window.tinyMCE.execInstanceCommand(editor, 'mceInsertContent', false, jQuery.trim(shortcode));
                                                                //ed.execCommand('mceRepaint');
                                                                ed.execCommand('mceSetContent', false, tinyMCE.activeEditor.getContent());
                                                            }

                                                        },
                                                        close: true
                                                    },
                                                    1: {
                                                        name: 'Close',
                                                        action: 'close'
                                                    }
                                                }
                                            };
                                            pn_advanced_wp_popup_mdf.popup(popup_params);
                                        });

                                    }
                                });

                        return btn;
                    }

                    return null;
                },
                addWithPopup: function(ed, title, id) {
                    ed.add({
                        title: title,
                        onclick: function() {
                            tinyMCE.activeEditor.execCommand("mdf_shortcode_tiny_popup", false, {
                                title: title,
                                identifier: id
                            });
                        }
                    });
                },
                addImmediate: function(ed, title, sc) {
                    ed.add({
                        title: title,
                        onclick: function() {
                            tinyMCE.activeEditor.execCommand("mceInsertContent", false, sc);
                        }
                    });
                },
                getInfo: function() {
                    return {
                        longname: 'Shortcodes pack by realmag777',
                        version: "1.1.1"
                    };
                }
            });

    tinymce.PluginManager.add("mdf_tiny_shortcodes", tinymce.plugins.mdf_tiny_shortcodes);

});
*/

function mdf_show_stat_info_popup(text) {
    jQuery("#pn_html_buffer").text(text);
    jQuery("#pn_html_buffer").fadeTo(400, 0.9);
}


function mdf_hide_stat_info_popup() {
    window.setTimeout(function() {
        jQuery("#pn_html_buffer").fadeOut(400);
    }, 500);
}



