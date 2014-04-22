<?php

class MetaDataFilterShortcodes extends MetaDataFilterCore {

    public static function init() {

        add_shortcode('meta_data_filter_results', array(__CLASS__, 'do_meta_data_filter_shortcode'));
    }

//basic shortcode to show filtered results
    public static function do_meta_data_filter_shortcode() {
        $data = array();
        echo self::render_html(self::get_application_path() . 'views/shortcode/meta_data_filter.php', $data);
    }

}
