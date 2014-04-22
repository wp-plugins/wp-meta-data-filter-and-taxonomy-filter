jQuery(function() {
    jQuery('.mdf_search_button').click(function() {
        var id = parseInt(jQuery(this).data('id'), 10);
        if (id > 0) {
            mdf_show_stat_info_popup(lang_one_moment);
            var data = {
                action: "mdf_search_button_get_content",
                shortcode_id: id
            };
            jQuery.post(ajaxurl, data, function(html) {

                mdf_hide_stat_info_popup();

                var popup_params = {
                    content: html,
                    title: 'Wordpress Meta Data & Taxonomies Filter',
                    overlay: true,
                    open: function() {
                        mdf_tooltip_init();
                    },
                    buttons: {
                        0: {
                            name: 'Close',
                            action: 'close'
                        }
                    }
                };
                pn_advanced_wp_popup_mdf.popup(popup_params);
            });
        }
        
        return false;
    });
});
