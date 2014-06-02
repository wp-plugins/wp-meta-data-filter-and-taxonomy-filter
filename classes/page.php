<?php

class MetaDataFilterPage extends MetaDataFilterCore {

    public static $cached_filter_posts = array(); //for the_title hook

    public static function init() {
        if(MetaDataFilter::get_setting('post_features_panel_auto') == 1) {
            add_action('the_title', array(__CLASS__, 'the_title'));
        }
        add_action('wp_ajax_meta_data_filter_get_data_group_topage_items', array(__CLASS__, 'get_data_group_topage_items'));
        add_action('wp_ajax_mdf_encode_search_get_params', array(__CLASS__, 'encode_search_get_params'));
        add_action('wp_ajax_nopriv_mdf_encode_search_get_params', array(__CLASS__, 'encode_search_get_params'));
        //+++
        add_action('wp_ajax_mdf_get_ajax_auto_recount_data', array(__CLASS__, 'get_ajax_auto_recount_data'));
        add_action('wp_ajax_nopriv_mdf_get_ajax_auto_recount_data', array(__CLASS__, 'get_ajax_auto_recount_data'));
    }

    public static function add_meta_box() {
        $settings = self::get_settings();
        if(isset($settings['post_types'])) {
            if(!empty($settings['post_types'])) {
                foreach($settings['post_types'] as $post_name) {
                    add_meta_box("meta_data_filter_options", __("Meta Data Filter Options", 'meta-data-filter'), array(__CLASS__, 'data_meta_box'), $post_name, "normal", "high");
                }
            }
        }
    }

    //ajax - get new items by cat select
    public static function get_data_group_topage_items() {
        self::draw_back_page_items($_REQUEST['cat_id']);
        exit;
    }

    //admin page or *? panel
    public static function draw_back_page_items( $cat_id = 0, $post_id = 0 ) {
        if($cat_id == 0) {
            $cat_id = get_post_meta($post_id, self::$slug_cat, TRUE);
        }
        $blocks = self::get_fiters_posts_datablocks(self::get_fiters_posts($cat_id));
        //***
        if(!empty($blocks)) {
            $page_meta_data_filter = $cat_id > 0 ? get_post_meta($post_id, 'page_meta_data_filter', true) : array();
            foreach($blocks as $filter_post_id=> $filter) {
                echo self::render_html(self::get_application_path() . 'views/page/draw_back_page_items.php', array(
                    'filter'=>$filter,
                    'post_id'=>$post_id,
                    'page_meta_data_filter'=>$page_meta_data_filter
                ));
            }
        } else {
            ?>
            <input type="hidden" name="page_meta_data_filter" value="" />
            <?php

        }
    }

    public static function the_title( $title = "" ) {
        if(isset($_REQUEST['meta_data_filter_cat']) AND in_the_loop()) {
            global $post;
            static $title_collector = array();
            if(in_array($post->ID, $title_collector)) {
                return "";
            }
            //+++
            $data = array();
            $data['cat_id'] = get_post_meta($post->ID, self::$slug_cat, TRUE);
            $data['post_id'] = $post->ID;
            //caching queries data while loop titles
            if(!isset(self::$cached_filter_posts[$data['cat_id']])) {
                self::$cached_filter_posts[$data['cat_id']] = MetaDataFilterPage::get_fiters_posts($data['cat_id']);
            }
            $data['filter_posts'] = self::$cached_filter_posts[$data['cat_id']];
            $data['page_meta_data_filter'] = $data['cat_id'] > 0 ? get_post_meta($post->ID, 'page_meta_data_filter', true) : array();
            $title_collector[] = $post->ID;
            $title.= self::render_html(self::get_application_path() . 'views/front/the_title.php', $data);
        }

        return $title;
    }

    public static function draw_post_features_panel( $post_id ) {
        if(isset($_REQUEST['meta_data_filter_cat'])) {
            $data = array();
            $data['cat_id'] = get_post_meta($post_id, self::$slug_cat, TRUE);
            $data['post_id'] = $post_id;
            //caching queries data while loop titles
            if(!isset(self::$cached_filter_posts[$data['cat_id']])) {
                self::$cached_filter_posts[$data['cat_id']] = MetaDataFilterPage::get_fiters_posts($data['cat_id']);
            }
            $data['filter_posts'] = self::$cached_filter_posts[$data['cat_id']];
            $data['page_meta_data_filter'] = $data['cat_id'] > 0 ? get_post_meta($post_id, 'page_meta_data_filter', true) : array();
            echo self::render_html(self::get_application_path() . 'views/front/the_title.php', $data);
        }
    }

    //for single page widget
    public static function draw_single_page_items( $post_id = 0, $show_absent_items = true ) {
        wp_enqueue_script('meta_data_filter_widget', self::get_application_uri() . 'js/front.js', array('jquery'));
        $cat_id = get_post_meta($post_id, self::$slug_cat, TRUE);
        $blocks = self::get_fiters_posts_datablocks(self::get_fiters_posts($cat_id));
        //***
        $page_meta_data_filter = $cat_id > 0 ? get_post_meta($post_id, 'page_meta_data_filter', true) : array();
        foreach($blocks as $filter_post_id=> $filter_post) {
            echo self::render_html(self::get_application_path() . 'views/page/draw_single_page_items.php', array(
                'filter_post'=>$filter_post,
                'post_id'=>$post_id,
                'page_meta_data_filter'=>$page_meta_data_filter,
                'show_absent_items'=>$show_absent_items
            ));
        }
    }

    public static function draw_front_page_items( $widget_options, $form_uniqid ) {
        if($widget_options[MetaDataFilterCore::$slug_cat] == MetaDataFilter::WIDGET_TAXONOMIES_ONLY) {
            $blocks = array(
                array(
                    'name'=>'~',
                    'items'=>array(
                        array(
                            'name'=>'~',
                            'type'=>'taxonomy',
                            'description'=>'',
                            'meta_key'=>'medafi_tax_only'
                        )
                    )
                )
            );
        } else {
            $blocks = self::get_fiters_posts_datablocks(self::get_fiters_posts($widget_options[self::$slug_cat]));
        }


        $page_meta_data_filter = self::get_page_mdf_data();
        //***
        if(!empty($blocks)) {
            wp_enqueue_style('meta_data_filter_front', self::get_application_uri() . 'css/front.css');
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core', array('jquery'));
            wp_enqueue_script('jquery-ui-slider', array('jquery', 'jquery-ui-core'));
            //***
            foreach($blocks as $filter_post_id=> $filter_block) {
                echo self::render_html(self::get_application_path() . 'views/page/draw_front_page_items.php', array(
                    'form_uniqid'=>$form_uniqid,
                    'filter_block'=>$filter_block,
                    'filter_post_id'=>$filter_post_id,
                    'page_meta_data_filter'=>$page_meta_data_filter,
                    'slug'=>$widget_options['meta_data_filter_slug'],
                    'widget_options'=>$widget_options
                ));
            }
        }
    }

    public static function encode_search_get_params() {
        $data = array();
        parse_str($_REQUEST['vars'], $data);
        if(!empty($data) AND is_array($data)) {
            if(isset($data['mdf'])) {
                if(!empty($data['mdf'])) {
                    foreach($data['mdf'] as $key=> $value) {
                        if($value == '~') {
                            unset($data['mdf'][$key]); //clearing not need data
                        }
                    }
                }
            }
        }
        $responce = 'meta_data_filter_bool=' . $data['meta_data_filter_bool'] . '&mdf_tax_bool=' . $data['mdf_tax_bool'] . '&' . self::$slug_cat . '=' . $data[self::$slug_cat];
        if(isset($data['custom_filter_panel'])) {
            $responce.='&custom_filter_panel=' . $data['custom_filter_panel'];
        }

        //reset
        if($_REQUEST['mode'] == 'reset') {
            //shortcodes always reset by ajax
            if($_REQUEST['is_ajaxed_reset']) {
                if($_REQUEST['type'] == 'shortcode') {
                    echo do_shortcode('[mdf_search_form id="' . $_REQUEST['shortcode_id'] . '"]');
                    exit;
                }
            }

            //***

            $tmp = array();
            $tmp['filter_post_blocks'] = $data['mdf']['filter_post_blocks'];
            $tmp['mdf_widget_options'] = $data['mdf']['mdf_widget_options'];
            if(isset($data['mdf']['taxonomy'])) {
                $tmp['taxonomy'] = $data['mdf']['taxonomy'];
            } else {
                $tmp['taxonomy'] = array();
            }

            if(!empty($tmp['taxonomy']) AND is_array($tmp['taxonomy'])) {
                foreach($tmp['taxonomy'] as $h_type=> $taxes) {
                    if(!empty($taxes) OR is_array($taxes)) {
                        foreach($taxes as $tax_name=> $value) {
                            if($h_type == 'select') {
                                $tmp['taxonomy'][$h_type][$tax_name] = array(-1);
                            } else {
                                $tmp['taxonomy'][$h_type][$tax_name] = ("");
                            }
                        }
                    }
                }
            }
            $data['mdf'] = $tmp;
        }




        $responce.='&page_mdf=' . base64_encode(serialize($data['mdf']));
        if(isset($_REQUEST['no_wp_die'])) {
            return $responce;
        } else {
            wp_die($responce);
        }
    }

    //ajax auto submit mode
    public static function get_ajax_auto_recount_data() {
        //for qtranslate
        if(!empty($_REQUEST['mdf_front_qtrans_lang'])) {
            if(function_exists('qtrans_enableLanguage')) {
                $GLOBALS['q_config']['language'] = $_REQUEST['mdf_front_qtrans_lang'];
            }
        }
        //+++
        $_REQUEST['no_wp_die'] = TRUE;
        $link_data = explode('&', self::encode_search_get_params());
        $data = array();
        if(!empty($link_data)) {
            foreach($link_data as $pair) {
                $tmp = explode('=', $pair);
                $data[$tmp[0]] = $tmp[1];
            }
        }

        //+++
        $_REQUEST['meta_data_filter_cat'] = $data['meta_data_filter_cat'];
        $_REQUEST['page_mdf'] = $data['page_mdf'];
        $slug = $_REQUEST['slug'];
        $mdf_data = self::get_page_mdf_data();
        //+++
        set_query_var('paged', 0);
        $_GET['slg'] = $slug;
        $_REQUEST['mdf_do_not_render_shortcode_tpl'] = TRUE;

        //***
        do_shortcode('[meta_data_filter_results]');
        if($_REQUEST['type'] == 'shortcode') {
            echo do_shortcode('[mdf_search_form id="' . $_REQUEST['shortcode_id'] . '"]');
        } else {
            self::dynamic_sidebar($_REQUEST['widget_id'], $_REQUEST['sidebar_name']);
        }
        exit;
    }

    //need only for AJAX mode recount
    private static function dynamic_sidebar( $widget_id, $index = 1 ) {

        global $wp_registered_sidebars, $wp_registered_widgets;

        if(is_int($index)) {
            $index = "sidebar-$index";
        } else {
            $index = sanitize_title($index);
            foreach((array) $wp_registered_sidebars as $key=> $value) {
                if(sanitize_title($value['name']) == $index) {
                    $index = $key;
                    break;
                }
            }
        }


        //+++ CODE EDITION
        //$sidebars_widgets = wp_get_sidebars_widgets();
        $sidebars_widgets = array();
        $sidebars_widgets[$index] = array();
        $sidebars_widgets[$index][] = $widget_id;
        //+++

        if(empty($wp_registered_sidebars[$index]) || empty($sidebars_widgets[$index]) || !is_array($sidebars_widgets[$index])) {
            return false;
        }

        $sidebar = $wp_registered_sidebars[$index];

        $did_one = false;
        foreach((array) $sidebars_widgets[$index] as $id) {

            if(!isset($wp_registered_widgets[$id]))
                continue;

            $params = array_merge(
                    array(array_merge($sidebar, array('widget_id'=>$id, 'widget_name'=>$wp_registered_widgets[$id]['name']))), (array) $wp_registered_widgets[$id]['params']
            );

            // Substitute HTML id and class attributes into before_widget
            $classname_ = '';
            foreach((array) $wp_registered_widgets[$id]['classname'] as $cn) {
                if(is_string($cn))
                    $classname_ .= '_' . $cn;
                elseif(is_object($cn))
                    $classname_ .= '_' . get_class($cn);
            }
            $classname_ = ltrim($classname_, '_');
            $params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

            $params = apply_filters('dynamic_sidebar_params', $params);

            $callback = $wp_registered_widgets[$id]['callback'];

            do_action('dynamic_sidebar', $wp_registered_widgets[$id]);

            if(is_callable($callback)) {
                call_user_func_array($callback, $params);
                $did_one = true;
            }
        }

        return $did_one;
    }

    public static function get_fiters_posts( $cat_id ) {
        return get_posts(array(
            'post_type'=>self::$slug,
            'orderby'=>self::$slug_cat,
            'numberposts'=>-1,
            'tax_query'=>array(
                array(
                    'taxonomy'=>self::$slug_cat,
                    'field'=>'id',
                    'terms'=>$cat_id,
                ),
            ),
            'meta_query'=>array(
                array(
                    'key'=>'sequence',
                    'value'=>null,
                    'compare'=>'!=',
                    'type'=>'BOOLEAN'
                )
            ),
            'meta_key'=>'sequence',
            'orderby'=>'meta_value',
            'order'=>'ASC',
            'fields'=>'ids'
        ));
    }

    public static function get_fiters_posts_datablocks( $post_ids ) {
        $blocks = array();
        if(!empty($post_ids)) {
            foreach($post_ids as $id) {
                $res = array();
                $res['name'] = get_the_title($id);
                $res['items'] = self::get_html_items($id);
                $blocks[$id] = $res;
            }
        }

        return $blocks;
    }

    public static function data_meta_box() {
        global $post;
        wp_enqueue_script('meta_data_filter_admin', self::get_application_uri() . 'js/admin.js', array('jquery'));
        echo self::render_html(self::get_application_path() . 'views/page/data_meta_box.php', array(
            'html_items'=>self::get_html_items($post->ID),
            'cat_id'=>get_post_meta($post->ID, self::$slug_cat, TRUE),
            'post_id'=>$post->ID
        ));
    }

    public static function draw_sort_by_filter() {
        if(isset($_GET['custom_filter_panel'])) {
            $custom_filter_panel = explode(',', $_GET['custom_filter_panel']);
            if(!empty($custom_filter_panel) AND is_array($custom_filter_panel)) {
                return self::render_html(self::get_application_path() . 'views/front/custom_filter_panel.php', array(
                            'custom_filter_panel'=>$custom_filter_panel
                ));
            }
        }
    }

    //widget, sort by functionality
    public static function get_html_sliders_by_filter_cat( $filter_cat ) {
        $posts = self::get_fiters_posts($filter_cat);
        $res = array();
        //+++
        if(!empty($posts)) {
            foreach($posts as $post_id) {
                $fields = self::get_html_items($post_id);
                if(!empty($fields)) {
                    if(is_array($fields)) {
                        foreach($fields as $key=> $value) {
                            if($value['type'] == 'slider') {
                                $res[$key . '~' . $post_id] = $value['name'] . ' (' . $value['slider'] . ')';
                            }
                        }
                    }
                }
            }
        }

        return $res;
    }

}
