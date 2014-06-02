<?php

class MetaDataFilterShortcodes extends MetaDataFilterCore {

    public static function init() {
        add_shortcode('meta_data_filter_results', array(__CLASS__, 'do_meta_data_filter_shortcode'));
        add_shortcode('mdf_force_searching', array(__CLASS__, 'force_searching'));
    }

//basic shortcode to show filtered results
    public static function do_meta_data_filter_shortcode() {
        $data = array();
        echo self::render_html(self::get_application_path() . 'views/shortcode/meta_data_filter.php', $data);
    }

    public static function force_searching() {
        if(self::is_page_mdf_data()) {
            $tpl = self::get_setting('output_tpl');
            if($tpl == 'search') {
                $_REQUEST['mdf_do_not_render_shortcode_tpl'] = true;
                $_REQUEST['mdf_get_query_args_only'] = true;
                do_shortcode('[meta_data_filter_results]');
                $args = $_REQUEST['meta_data_filter_args'];
                global $wp_query;
                $wp_query = new WP_Query($args);
                $_REQUEST['meta_data_filter_count'] = $wp_query->found_posts;
            }
        }
    }

}
