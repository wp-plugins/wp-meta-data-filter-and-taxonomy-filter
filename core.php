<?php

class MetaDataFilterCore {

    public static $items_types = array();
    public static $tax_items_types = array();
    //+++
    public static $slug = 'meta_data_filter';
    public static $slug_cat = 'meta_data_filter_cat';
    public static $slug_links = 'mdf_links';
    public static $slug_links_cat = 'mdf_links_cat';
    public static $slug_shortcodes = 'mdf_shortcodes';

    public static function get_application_path() {
        return plugin_dir_path(__FILE__);
    }

    public static function get_application_uri() {
        return plugin_dir_url(__FILE__);
    }

    public static function get_html_items( $post_id ) {
        return get_post_meta($post_id, 'html_items', true);
    }

    public static function get_settings() {
        return get_option('meta_data_filter_settings');
    }

    public static function get_setting( $key ) {
        $settings = self::get_settings();
        return (isset($settings[$key]) ? $settings[$key] : 0);
    }

    public static function get_marketing_settings() {
        return get_option('mdf_marketing_settings');
    }

    public static function get_marketing_setting( $key ) {
        $settings = self::get_marketing_settings();
        return (isset($settings[$key]) ? $settings[$key] : 0);
    }

    public static function get_search_url_page() {
        $s = self::get_settings();
        return $s['search_url'];
    }

    public static function get_page_mdf_string() {
        $string = "";

        if(isset($_REQUEST['page_mdf'])) {
            $string = $_REQUEST['page_mdf'];
        }


        if(!isset($_REQUEST['page_mdf'])) {
            if(isset($_SERVER) AND isset($_SERVER['QUERY_STRING']) AND ! empty($_SERVER['QUERY_STRING'])) {
                $query_string = $_SERVER['QUERY_STRING'];
                if(substr_count($query_string, 'page_mdf=')) {
                    $page_mdf = explode('page_mdf', $query_string);
                    if(isset($page_mdf[1]) AND ! empty($page_mdf[1])) {
                        $string = $page_mdf[1];
                    }
                }
            }
        }


        return $string;
    }

    public static function get_page_mdf_data() {
        static $page_meta_data_filter;
        //+++        
        if(!empty($page_meta_data_filter)) {
            $_GLOBALS['MDF_META_DATA_FILTER'] = $page_meta_data_filter;
            return $page_meta_data_filter;
        }
        //+++        
        $page_mdf_string = self::get_page_mdf_string();
        $page_meta_data_filter = unserialize(base64_decode($page_mdf_string));

        //print_r($page_meta_data_filter);
        //***
        $_GLOBALS['MDF_META_DATA_FILTER'] = $page_meta_data_filter;
        return $page_meta_data_filter;
    }

    public static function is_page_mdf_data() {//if isset get page_mdf
        $data = self::get_page_mdf_data();

        if(!empty($data)) {
            return true;
        }

        return false;
    }

    public static function get_html_items_by_cat( $cat_id ) {
        $args = array(
            'post_type'=>self::$slug,
            'orderby'=>'meta_value_num',
            'meta_key'=>'sequence',
            'order'=>'ASC',
            'tax_query'=>array(
                'relation'=>'AND',
                array(
                    'taxonomy'=>self::$slug_cat,
                    'field'=>'term_id',
                    'terms'=>$cat_id
                )
            ),
            'post_status'=>array('publish'),
            'nopaging'=>true
        );
        $res = array();
        $query = new WP_Query($args);

        if($query->have_posts()) {
            while($query->have_posts()) {
                $query->the_post();
                global $post;
                $tmp = array();
                $tmp['post_title'] = $post->post_title;
                $tmp['html_items'] = self::get_html_items($post->ID);
                $res[$post->ID] = $tmp;
            }
        }

        return $res;
    }

    public static function render_html( $pagepath, $data = array() ) {
        @extract($data);
        ob_start();
        include($pagepath);
        return ob_get_clean();
    }

}

class WP_QueryMDFCounter extends WP_Query {

    function set_found_posts( $q, $limits ) {
        return false;
    }

}
