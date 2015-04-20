<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

//constant links
class MDTF_CONST_LINKS extends MetaDataFilterCore {

    public static function init() {
        parent::init();
        $args = array(
            'labels' => array(
                'name' => __('MDTF Constant links', 'meta-data-filter'),
                'singular_name' => __('Constant link', 'meta-data-filter'),
                'add_new' => __('Add New Constant link', 'meta-data-filter'),
                'add_new_item' => __('Add New Constant link', 'meta-data-filter'),
                'edit_item' => __('Edit Constant link', 'meta-data-filter'),
                'new_item' => __('New Constant link', 'meta-data-filter'),
                'view_item' => __('View Constant link', 'meta-data-filter'),
                'search_items' => __('Search Constant links', 'meta-data-filter'),
                'not_found' => __('No Constant links found', 'meta-data-filter'),
                'not_found_in_trash' => __('No Constant links found in Trash', 'meta-data-filter'),
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
            'rewrite' => array('slug' => self::$slug_const_links),
            'show_in_admin_bar' => false,
            'menu_icon' => '',
            'taxonomies' => array() // this is IMPORTANT
        );

        register_post_type(self::$slug_const_links, $args);

        add_action('admin_init', array(__CLASS__, 'admin_init'), 1);
        add_action('save_post', array(__CLASS__, 'save_post'), 1);
    }

    public static function admin_init() {
        add_meta_box("mdf_const_links", __("Links options", 'meta-data-filter'), array(__CLASS__, 'draw_options_meta_box'), self::$slug_const_links, "normal", "high");
    }

    public static function draw_options_meta_box($post) {
        $data = array();
        $data['post_id'] = $post->ID;
        $data['page_mdf_link'] = get_post_meta($post->ID, 'page_mdf_link', TRUE);
        $data['page_mdf_string'] = get_post_meta($post->ID, 'page_mdf_string', TRUE);
        echo self::render_html(self::get_application_path() . 'views/const_links/metabox.php', $data);
    }

    public static function save_post() {
        if (!empty($_POST)) {
            global $post;
            if (is_object($post)) {
                if ($post->post_type == self::$slug_const_links) {
                    if (isset($_POST['mdf_const_links_values'])) {
                        if (!empty($_POST['mdf_new_link']) AND filter_var($_POST['mdf_new_link'], FILTER_VALIDATE_URL) !== FALSE) {
                            $full_link = trim($_POST['mdf_new_link']);
                            $link = explode('page_mdf=', $full_link);
                            if (isset($link[1])) {
                                $key_string = $link[1];
                                $full_link = str_replace($key_string, $post->ID, $full_link);
                                update_post_meta($post->ID, 'page_mdf_link', $full_link);
                                $string = self::get_page_mdf_session($key_string);
                                update_post_meta($post->ID, 'page_mdf_string', $string);
                            }
                        }
                    }
                }
            }
        }
    }

}
