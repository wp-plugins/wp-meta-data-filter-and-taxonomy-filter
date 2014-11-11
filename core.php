<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
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
    public static $slug_woo_sort = 'mdf_woo_sort';
    public static $max_int = 2147483647;
    public static $session_id = 0;
    public static $default_order_by = 'date';
    public static $default_order = 'DESC';

    public static function init() {
        if (!session_id()) {
            session_start();
        }
        self::$session_id = session_id();
        //+++
        $default_order_by = self::get_setting('default_order_by');
        $default_order = self::get_setting('default_order');
        //+++
        if (!empty($default_order)) {
            self::$default_order = $default_order;
        }
        if (!empty($default_order_by)) {
            self::$default_order_by = $default_order_by;
        }
    }

    public static function get_application_path() {
        return plugin_dir_path(__FILE__);
    }

    public static function get_application_uri() {
        return plugin_dir_url(__FILE__);
    }

    public static function get_html_items($post_id) {
        return get_post_meta($post_id, 'html_items', true);
    }

    public static function get_settings() {
        return get_option('meta_data_filter_settings');
    }

    public static function get_setting($key) {
        $settings = self::get_settings();
        return (isset($settings[$key]) ? $settings[$key] : 0);
    }

    public static function get_marketing_settings() {
        return get_option('mdf_marketing_settings');
    }

    public static function get_marketing_setting($key) {
        $settings = self::get_marketing_settings();
        return (isset($settings[$key]) ? $settings[$key] : 0);
    }

    public static function get_search_url_page() {
        $s = self::get_settings();
        return $s['search_url'];
    }

    public static function get_post_types() {
        $all_post_types = get_post_types();
        unset($all_post_types['nav_menu_item']);
        unset($all_post_types['revision']);
        unset($all_post_types[self::$slug]);
        unset($all_post_types[self::$slug_woo_sort]);
        unset($all_post_types[self::$slug_shortcodes]);
        unset($all_post_types[self::$slug_links]);

        return $all_post_types;
    }

    public static function get_page_mdf_string() {
        $string = "";

        //we need this to simply redraw search form for another selected filter-category
        if (isset($_REQUEST['mdf_ajax_request']) AND isset($_REQUEST['simple_form_redraw']) AND $_REQUEST['simple_form_redraw'] == 1) {
            return "";
        }

        if (isset($_REQUEST['page_mdf'])) {
            $string = $_REQUEST['page_mdf'];
        }


        if (!isset($_REQUEST['page_mdf'])) {
            if (isset($_SERVER) AND isset($_SERVER['QUERY_STRING']) AND ! empty($_SERVER['QUERY_STRING'])) {
                $query_string = $_SERVER['QUERY_STRING'];
                if (substr_count($query_string, 'page_mdf=')) {
                    $page_mdf = explode('page_mdf', $query_string);
                    if (isset($page_mdf[1]) AND ! empty($page_mdf[1])) {
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
        if (!empty($page_meta_data_filter)) {
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

        if (!empty($data)) {
            return true;
        }

        return false;
    }

    public static function get_html_items_by_cat($cat_id) {
        $args = array(
            'post_type' => self::$slug,
            'orderby' => 'meta_value_num',
            'meta_key' => 'sequence',
            'order' => 'ASC',
            'ignore_sticky_posts' => self::get_setting('ignore_sticky_posts'),
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => self::$slug_cat,
                    'field' => 'term_id',
                    'terms' => $cat_id
                )
            ),
            'post_status' => array('publish'),
            'nopaging' => true
        );
        $res = array();
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
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

    public static function front_script_includer() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-datepicker', array('jquery'));
        wp_enqueue_style('meta_data_filter_front', MetaDataFilterCore::get_application_uri() . 'css/front.css');
        //tooltipster
        wp_enqueue_style('tooltipster', self::get_application_uri() . 'js/tooltipster/css/tooltipster.css');
        $tooltip_theme = self::get_setting('tooltip_theme');
        if ($tooltip_theme != 'default') {
            wp_enqueue_style('tooltipster_theme', self::get_application_uri() . 'js/tooltipster/css/themes/tooltipster-' . $tooltip_theme . '.css');
        }
        wp_enqueue_script('tooltipster', self::get_application_uri() . 'js/tooltipster/js/jquery.tooltipster.min.js', array('jquery'));
        //beauty scrolling malihu-custom-scrollbar
        //http://manos.malihu.gr/jquery-custom-content-scroller/
        if (self::get_setting('use_custom_scroll_bar')) {
            wp_enqueue_script('mousewheel', self::get_application_uri() . 'js/malihu-custom-scrollbar/jquery.mousewheel.min.js', array('jquery'));
            wp_enqueue_script('malihu-custom-scrollbar', self::get_application_uri() . 'js/malihu-custom-scrollbar/jquery.mCustomScrollbar.min.js', array('jquery'));
            wp_enqueue_script('malihu-custom-scrollbar-concat', self::get_application_uri() . 'js/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js', array('jquery'));
            wp_enqueue_style('malihu-custom-scrollbar', self::get_application_uri() . 'js/malihu-custom-scrollbar/jquery.mCustomScrollbar.css');
        }
        //beauty range-sliders ion.range-slider
        //http://ionden.com/a/plugins/ion.rangeSlider/

        wp_enqueue_script('ion.range-slider', self::get_application_uri() . 'js/ion.range-slider/ion.rangeSlider.min.js', array('jquery'));
        wp_enqueue_style('ion.range-slider', self::get_application_uri() . 'js/ion.range-slider/css/ion.rangeSlider.css');
        wp_enqueue_style('ion.range-slider-skin', self::get_application_uri() . 'js/ion.range-slider/css/ion.rangeSlider.skinNice.css');
        if (self::get_setting('use_chosen_js_w') OR self::get_setting('use_chosen_js_s')) {
            //chosen - beautiful drop-downs http://harvesthq.github.io/chosen/
            wp_enqueue_script('chosen-drop-down', self::get_application_uri() . 'js/chosen/chosen.jquery.min.js', array('jquery'));
            wp_enqueue_style('chosen-drop-down', self::get_application_uri() . 'js/chosen/chosen.min.css');
        }

        if (self::get_setting('use_custom_icheck')) {
            //http://fronteed.com/iCheck/
            $skin = self::get_setting('icheck_skin');
            if (!$skin) {
                $skin = 'flat_blue';
            }
            $skin = explode('_', $skin);
            wp_enqueue_script('icheck-jquery', self::get_application_uri() . 'js/icheck/icheck.min.js', array('jquery'));
            //wp_enqueue_style('icheck-jquery', self::get_application_uri() . 'js/icheck/all.css');
            wp_enqueue_style('icheck-jquery-color', self::get_application_uri() . 'js/icheck/skins/' . $skin[0] . '/' . $skin[1] . '.css');
        }
    }

    public static function render_html($pagepath, $data = array()) {
        @extract($data);
        ob_start();
        include($pagepath);
        return ob_get_clean();
    }

    //API
    /** Get meta value of post
     *
     * @param string $key Meta Key
     * @param int $post_id Post ID, if its == 0, ID will try to get from globals
     * @param bool $look_for_reflection Set to true if you now or you think that param is reflected
     *
     * @return mixed Depends of what meta field is keeps
     */
    public static function get_field($key, $post_id = 0, $look_for_reflection = false) {
        $res = "";
        if ($post_id == 0) {
            $post_id = get_the_ID();
            if (!$post_id) {
                return __('something wrong with post ID', 'meta-data-filter');
            }
        }
//***
        if ($look_for_reflection) {
            global $wpdb;
            $filter_post_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '%{$key}%'");
            $html_items = self::get_html_items($filter_post_id);
            if (isset($html_items[$key])) {
                if (isset($html_items[$key]['is_reflected']) AND $html_items[$key]['is_reflected'] == 1) {
                    $reflected_key = $html_items[$key]['reflected_key'];
                    $res = get_post_meta($post_id, $reflected_key, TRUE);
                } else {
                    $res = get_post_meta($post_id, $key, TRUE);
                }
            } else {
                return __('something wrong with meta key', 'meta-data-filter');
            }
        } else {
            $res = get_post_meta($post_id, $key, TRUE);
        }

        return $res;
    }

    //is customer look the site from mobile device
    public static function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    //this function needs for multi range sider
    public static function range_slider_multicontroller($slug, $meta_key, $from, $to) {
        //*** is key is multy values
        $is_multi = false;
        /*
          global $wpdb;
          $filter_id = (int) $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '%s'  AND meta_value LIKE '%s'", 'html_items', '%' . $meta_key . '%'));
          if ($filter_id > 0) {
          $filter_items = self::get_html_items($filter_id);
          if (isset($filter_items[$meta_key]['slider_multi_value']) AND $filter_items[$meta_key]['slider_multi_value'] == 1) {
          $is_multi = true;
          }
          }
         * 
         */
        //***
        if ($is_multi) {
            $hash = md5(self::$session_id);
            $tmp_key = trim($meta_key . '_mdftmp_' . $hash);
            //check for data fresh
            $updated = (int) get_option($meta_key . '_updated_' . $slug, 0);
            $now = time();
            $is_freshed = TRUE;
            if ($now - $updated >= 5) {//5 sec
                $is_freshed = FALSE;
            }
            //***
            if (!$is_freshed) {
                $meta_query_array = array();
                $meta_query_array[] = array(
                    'key' => "meta_data_filter_cat",
                    'value' => (isset($_REQUEST['mdf_cat']) ? $_REQUEST['mdf_cat'] : $_REQUEST['meta_data_filter_cat_form']),
                    'compare' => '='
                );

                $args = array(
                    'post_type' => $slug,
                    'meta_query' => $meta_query_array,
                    'tax_query' => array(),
                    'post_status' => array('publish'),
                    'nopaging' => true,
                    'fields' => 'ids',
                    'ignore_sticky_posts' => self::get_setting('ignore_sticky_posts'),
                    'suppress_filters' => false,
                    'update_post_term_cache' => false,
                    'update_post_meta_cache' => false,
                    'cache_results' => false,
                    'showposts' => false,
                    'comments_popup' => false
                );

                $counter_obj = new WP_QueryMDFCounter($args);
                $posts_ids = $counter_obj->posts; //array
                //***       

                if (!empty($posts_ids)) {
                    foreach ($posts_ids as $post_id) {
                        $string = get_post_meta($post_id, $meta_key, TRUE);
                        $mode = 'single';
                        $vals = array();
                        if (substr_count($string, ',') > 0) {
                            $mode = 'set';
                            $vals = explode(',', $string);
                        }
                        if (substr_count($string, '^') > 0) {
                            $mode = 'range';
                            $vals = explode('^', $string);
                        }
                        if (is_array($vals) AND ! empty($vals)) {
                            $min = min($vals);
                            $max = max($vals);
                        }

                        switch ($mode) {
                            case 'single':
                                update_post_meta($post_id, $tmp_key, floatval($string));
                                break;
                            case 'set':
                                foreach ($vals as $val) {
                                    $val = floatval($val);
                                    if ($val >= $from AND $val <= $to) {
                                        update_post_meta($post_id, $tmp_key, $val);
                                        break;
                                    }
                                }
                                break;
                            case 'set':
                                for ($i = $max; $i >= $min; $i --) {
                                    if ($i >= $from AND $i <= $to) {
                                        update_post_meta($post_id, $tmp_key, $i);
                                        break;
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                    }

                    //fresh time mark
                    update_option($meta_key . '_updated_' . $slug, time());
                }
            }
            //***
            return array(
                'key' => $tmp_key,
                'value' => array($from, $to),
                'type' => 'DECIMAL',
                'compare' => 'BETWEEN'
            );
        }
        //if not multi val range-slider
        return array(
            'key' => $meta_key,
            'value' => array($from, $to),
            'type' => 'DECIMAL',
            'compare' => 'BETWEEN'
        );
    }

}

class WP_QueryMDFCounter extends WP_Query {

    function set_found_posts($q, $limits) {
        return false;
    }

}
