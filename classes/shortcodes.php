<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

class MetaDataFilterShortcodes extends MetaDataFilterCore {

    public static $cached_filter_posts = array(); //for features panel shortcode

    public static function init() {
        parent::init();

        add_action('admin_menu', array(__CLASS__, 'admin_menu'), 1);
        //+++
        add_action('admin_init', array(__CLASS__, 'admin_init'), 1);

        //+++
        //add_filter('mce_buttons', array(__CLASS__, 'mce_buttons'));
        //add_filter('mce_external_plugins', array(__CLASS__, 'mce_add_rich_plugins'));
        add_shortcode('meta_data_filter_results', array(__CLASS__, 'do_meta_data_filter_shortcode'));
        add_shortcode('mdf_search_form', array(__CLASS__, 'do_mdf_search_form'));
        add_shortcode('mdf_search_button', array(__CLASS__, 'do_mdf_search_button'));
        add_shortcode('mdf_force_searching', array(__CLASS__, 'force_searching'));
        add_shortcode('mdf_value', array(__CLASS__, 'mdf_value'));
        add_shortcode('mdf_post_features_panel', array(__CLASS__, 'post_features_panel'));
        add_shortcode('mdf_draw_sort_by_filter', array(__CLASS__, 'draw_sort_by_filter'));
        //add_shortcode('mdf_results_tax_navigation', array(__CLASS__, 'results_tax_navigation'));
        //add_shortcode('mdf_results_by_ajax', array(__CLASS__, 'results_by_ajax'));
        //woocommerce overloaded
        //add_shortcode('mdf_products', array(__CLASS__, 'mdf_products'));
        //add_shortcode('mdf_custom', array(__CLASS__, 'mdf_custom'));
        add_action('load-post.php', array(__CLASS__, "post_inits"));
        add_action('load-post-new.php', array(__CLASS__, "post_inits"));
        add_action('save_post', array(__CLASS__, 'save_post'), 1);
        add_action("manage_" . self::$slug_shortcodes . "_posts_custom_column", array(__CLASS__, "show_edit_columns_content"));
        add_filter("manage_" . self::$slug_shortcodes . "_posts_columns", array(__CLASS__, "show_edit_columns"));
        add_action('wp_ajax_mdf_draw_shortcode_html_items', array(__CLASS__, 'draw_shortcode_html_items_ajx'));
        //***
        add_action('wp_ajax_mdf_search_button_get_content', array(__CLASS__, 'mdf_search_button_get_content'));
        add_action('wp_ajax_nopriv_mdf_search_button_get_content', array(__CLASS__, 'mdf_search_button_get_content'));
    }

    public static function admin_init() {
        add_meta_box("mdf_shortcode_options", __("MDTF Shortcode Options", 'meta-data-filter'), array(__CLASS__, 'draw_options_meta_box'), self::$slug_shortcodes, "normal", "high");
        add_meta_box("mdf_shortcode_shortcode", __("Shortcode", 'meta-data-filter'), array(__CLASS__, 'draw_shortcode_meta_box'), self::$slug_shortcodes, "side", "high");
    }

    public static function admin_menu() {
        add_submenu_page('edit.php?post_type=' . self::$slug, __("MDTF Shortcodes", 'meta-data-filter'), __("MDTF Shortcodes", 'meta-data-filter'), 'manage_options', 'mdf_shortcodes', array(__CLASS__, 'draw_settings_page'));
    }

    public static function draw_settings_page() {
        ?>
        <div class="wrap">
            <b>You can use shortcodes in the premium version only!</b><br />
            <br />
            <a class="button" href="http://cars.wordpress-filter.com/" target="_blank">Look the demo please</a><br />
            <br />
            <a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" alt="Get the plugin" target="_blank"><img src="<?php echo self::get_application_uri() ?>images/mdtf_banner.jpg" /></a>
        </div>
        <?php
    }

    public static function draw_shortcode_meta_box() {
        
    }

    public static function show_edit_columns($columns) {
       
    }

    public static function show_edit_columns_content($column) {
       
    }

    public static function get_shortcode_options($post_id) {
        
    }

    public static function draw_options_meta_box($post) {
        
    }

    public static function post_inits() {
        
    }

    public static function mce_buttons($buttons) {
        //$buttons[] = 'meta-data-filter';
        return $buttons;
    }

    public static function mce_add_rich_plugins($plugin_array) {
        //$plugin_array['mdf_tiny_shortcodes'] = self::get_application_uri() . '/js/shortcode.js';
        return $plugin_array;
    }

//basic shortcode to show filtered results
    public static function do_meta_data_filter_shortcode() {
        
    }

    //[mdf_search_form id="742" slideout=1 location="right" action="click" onloadslideout=1]
    public static function do_mdf_search_form($args) {
        
    }

//ajax
    public static function draw_shortcode_html_items_ajx() {
        
    }

    //admin
    public static function draw_shortcode_html_items($shortcode_options/* if new */, $html_items = array()) {
        
    }

    //lets sort html_items as in shortcode panel
    private static function sort_html_items($html_items, $html_items_by_cat) {
        $html_items_by_cat_sorted = array();
        if (!empty($html_items)) {
            //sorting blocks
            foreach ($html_items as $filter_id => $meta_keys) {
                if (key_exists($filter_id, $html_items_by_cat)) {
                    $html_items_by_cat_sorted[$filter_id] = $html_items_by_cat[$filter_id];
                }
            }
            //sorting blocks items
            foreach ($html_items as $filter_id => $meta_keys) {
                if (!empty($meta_keys) AND is_array($meta_keys)) {
                    $tmp = array();
                    foreach ($meta_keys as $meta_key => $empty_val) {
                        if (isset($html_items_by_cat_sorted[$filter_id]['html_items'][$meta_key])) {
                            $tmp[$meta_key] = $html_items_by_cat_sorted[$filter_id]['html_items'][$meta_key];
                        }
                    }
                    $html_items_by_cat_sorted[$filter_id]['html_items'] = $tmp;
                }
            }
        } else {
            $html_items_by_cat_sorted = $html_items_by_cat;
        }

        return $html_items_by_cat_sorted;
    }

    public static function save_post() {
        if (!empty($_POST)) {
            global $post;
            if (is_object($post)) {
                if ($post->post_type == self::$slug_shortcodes) {
                    if (isset($_POST['shortcode_options'])) {
                        update_post_meta($post->ID, 'shortcode_options', $_POST['shortcode_options']);
                        update_post_meta($post->ID, 'html_items', $_POST['html_items']); //for sorting in shortcode
                    }
                }
            }
        }
    }

    public static function get_sh_skins() {
        
    }

    //**********************************
    public static function do_mdf_search_button($args) {
        
    }

    //ajax
    public static function mdf_search_button_get_content() {
        
    }

    //shortcode
    public static function force_searching() {
        if (self::is_page_mdf_data()) {
            $tpl = self::get_setting('output_tpl');
            if ($tpl == 'search') {
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

    //shortcode
    public static function post_features_panel() {
        $panel = "";
        global $post;
        if (is_object($post)) {
            static $title_collector = array();
            if (in_array($post->ID, $title_collector)) {
                return "";
            }

            //+++
            $data = array();
            $data['cat_id'] = get_post_meta($post->ID, self::$slug_cat, TRUE);
            $data['post_id'] = $post->ID;
            //caching queries data while loop titles
            if (!isset(self::$cached_filter_posts[$data['cat_id']])) {
                self::$cached_filter_posts[$data['cat_id']] = MetaDataFilterPage::get_fiters_posts($data['cat_id']);
            }
            $data['filter_posts'] = self::$cached_filter_posts[$data['cat_id']];
            $data['page_meta_data_filter'] = $data['cat_id'] > 0 ? get_post_meta($post->ID, 'page_meta_data_filter', true) : array();
            $title_collector[] = $post->ID;
            $panel = self::render_html(self::get_application_path() . 'views/shortcode/post_features_panel.php', $data);
        }


        return $panel;
    }

    //shortcode
    public static function draw_sort_by_filter() {
        if (isset($_GET['custom_filter_panel'])) {
            $custom_filter_panel = explode(',', $_GET['custom_filter_panel']);
            if (!empty($custom_filter_panel) AND is_array($custom_filter_panel)) {
                return self::render_html(self::get_application_path() . 'views/shortcode/custom_filter_panel.php', array('custom_filter_panel' => $custom_filter_panel));
            }
        }
    }

    //+++

    private static function get_taxonomy_parents_all($term_id, $taxonomy) {
        $parent = get_term_by('id', $term_id, $taxonomy);
        $res = array();
        $res[] = $parent;
        while ($parent->parent != 0) {
            $parent = get_term_by('id', $parent->parent, $taxonomy);
            $res[] = $parent;
        }
        return $res;
    }

    //shortcode
    //[mdf_value post_id=5 key='medafi_price' reflection=1]
    public static function mdf_value($atts) {
        if (isset($atts['key'])) {
            global $post;
            $post_id = 0;
            if (is_single()) {
                $post_id = $post->ID;
            }

            if (isset($atts['post_id']) AND $atts['post_id'] > 0) {
                $post_id = $atts['post_id'];
            }

            $look_for_reflection = 0;

            if (isset($atts['reflection']) AND $atts['reflection'] > 0) {
                $look_for_reflection = 1;
            }

            if ($post_id > 0) {
                return self::get_field($atts['key'], $post_id, $look_for_reflection);
            }
        }

        return "";
    }

}
