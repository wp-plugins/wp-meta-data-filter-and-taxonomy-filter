<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

class MDTF_SORT_PANEL extends MetaDataFilterCore {

    public static function init() {
        parent::init();
        $args = array(
            'labels' => array(
                'name' => __('MDTF Sort panels', 'meta-data-filter'),
                'singular_name' => __('Sort panels', 'meta-data-filter'),
                'add_new' => __('Add New Sort panel', 'meta-data-filter'),
                'add_new_item' => __('Add New Sort panel', 'meta-data-filter'),
                'edit_item' => __('Edit Sort panel', 'meta-data-filter'),
                'new_item' => __('New Sort panel', 'meta-data-filter'),
                'view_item' => __('View Sort panel', 'meta-data-filter'),
                'search_items' => __('Search Sort panels', 'meta-data-filter'),
                'not_found' => __('No Sort panels found', 'meta-data-filter'),
                'not_found_in_trash' => __('No Sort panels found in Trash', 'meta-data-filter'),
                'parent_item_colon' => ''
            ),
            'public' => false,
            'archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => true,
            'menu_position' => null,
            'supports' => array('title'),
            'show_in_menu' => 'edit.php?post_type=' . self::$slug,
            'rewrite' => array('slug' => self::$slug_woo_sort),
            'show_in_admin_bar' => false,
            'menu_icon' => '',
            'taxonomies' => array() // this is IMPORTANT
        );

        register_post_type(self::$slug_woo_sort, $args);

        add_action('admin_init', array(__CLASS__, 'admin_init'), 1);
        add_action('save_post', array(__CLASS__, 'save_post'), 1);

        add_action('woocommerce_before_shop_loop', array(__CLASS__, 'mdtf_catalog_ordering'));
        //add_action('woocommerce_before_main_content', array(__CLASS__, 'woocommerce_before_main_content'), 1);
    }

    public static function admin_init() {
        add_meta_box("mdf_woo_sort", __("Sort panel options", 'meta-data-filter'), array(__CLASS__, 'draw_options_meta_box'), self::$slug_woo_sort, "normal", "high");
    }

    public static function draw_options_meta_box($post) {
        $data = array();
        $data['post_id'] = $post->ID;
        $data['settings'] = self::get_woo_search_values($post->ID);
        $data['show_results_tax_navigation'] = get_post_meta($post->ID, 'show_results_tax_navigation', TRUE);
        echo self::render_html(self::get_application_path() . 'views/woosort/metabox.php', $data);
    }

    public static function save_post() {
        if (!empty($_POST)) {
            global $post;
            if (is_object($post)) {
                if ($post->post_type == self::$slug_woo_sort) {
                    if (isset($_POST['mdf_woo_search_values'])) {
                        $mdf_woo_search_values=  array_slice($_POST['mdf_woo_search_values'], 0, 3);
                        update_post_meta($post->ID, 'mdf_woo_search_values', $mdf_woo_search_values);
                        update_post_meta($post->ID, 'show_results_tax_navigation', 0);
                    }
                }
            }
        }
    }

    public static function get_woo_search_values($post_id) {
        $settings = get_post_meta($post_id, 'mdf_woo_search_values', TRUE);
        return $settings;
    }

    public static function draw_options_select($selected = 0, $name = "", $id = "") {
        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'category' => '',
            'orderby' => 'ID',
            'order' => 'ASC',
            'include' => '',
            'exclude' => '',
            'meta_key' => '',
            'meta_value' => '',
            'post_type' => self::$slug_woo_sort,
            'post_mime_type' => '',
            'post_parent' => '',
            'post_status' => 'publish',
            'suppress_filters' => true);
        $posts = get_posts($args);
        if (!empty($posts) AND is_array($posts)) {
            ?>
            <select class="widefat" name="<?php echo $name ?>" id="<?php echo $id ?>">
                <option value="0"><?php _e("Not using", 'meta-data-filter'); ?></option>
                <?php
                foreach ($posts as $post) {
                    ?>
                    <option <?php selected($selected, $post->ID) ?> value="<?php echo $post->ID ?>"><?php echo $post->post_title ?></option>
                    <?php
                }
            }
            ?>
        </select>
        <?php
    }

    public static function mdtf_catalog_ordering() {        
        if (self::is_page_mdf_data()) {
            $page_meta_data_filter = self::get_page_mdf_data();
            list($meta_query_array, $filter_post_blocks_data, $widget_options) = MetaDataFilterHtml::get_meta_query_array($page_meta_data_filter);
            if (isset($widget_options['woo_search_panel_id']) AND intval($widget_options['woo_search_panel_id']) > 0) {
                remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
                remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
                $post_id = intval($widget_options['woo_search_panel_id']);
                $settings = self::get_woo_search_values($post_id);
                if (!empty($settings) AND is_array($settings)) {
                    $order_by = isset($_REQUEST['order_by']) ? $_REQUEST['order_by'] : 'date';
                    ?>
                    <span class="mdf_woo_before_shop_loop">
                        <select class="mdf_woo_catalog_order_by">
                            <option value="0"><?php _e("Default", 'meta-data-filter'); ?></option>
                            <?php foreach ($settings as $value) : ?>
                                <?php $value = explode('^', $value); ?>
                                <option <?php selected($order_by, $value[0]) ?> value="<?php echo $value[0] ?>"><?php echo $value[1] ?></option>
                            <?php endforeach; ?>
                        </select>&nbsp;
                        <?php $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'DESC' ?>
                        <select class="mdf_woo_catalog_ordering">
                            <option value="DESC" <?php selected($order, 'DESC') ?>><?php _e("descending", 'meta-data-filter'); ?></option>
                            <option value="ASC" <?php selected($order, 'ASC') ?>><?php _e("ascending", 'meta-data-filter'); ?></option>
                        </select>&nbsp;
                    </span>
                    <?php
                    
                }
            }
        }
    }

}
