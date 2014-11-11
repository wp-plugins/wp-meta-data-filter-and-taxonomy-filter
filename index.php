<?php
/*
  Plugin Name: WordPress Meta Data & Taxonomies Filter (MDTF) GPL
  Plugin URI: http://wordpress-filter.com/
  Description: Powerful and Flexible Filters Tools. Let your clients find all things they are want with the ease on your site
  Author: realmag777
  Version: 1.0.6
  Author URI: http://www.pluginus.net/
 */
define('MDTF_PLUGIN_NAME', plugin_basename(__FILE__));
//+++
require plugin_dir_path(__FILE__) . 'core.php';
require plugin_dir_path(__FILE__) . 'classes/html.php';
require plugin_dir_path(__FILE__) . 'classes/page.php';
require plugin_dir_path(__FILE__) . 'classes/shortcodes.php';
require plugin_dir_path(__FILE__) . 'classes/sort_panel.php';
//ext
require plugin_dir_path(__FILE__) . 'ext/helper.php';
//require plugin_dir_path(__FILE__) . 'ext/marketing.php';
require plugin_dir_path(__FILE__) . 'ext/mdtf-pagination/tw-pagination.php';

//11-11-2014
class MetaDataFilter extends MetaDataFilterCore {

    const WIDGET_TAXONOMIES_ONLY = -1;

    public static function init() {
        parent::init();
        load_plugin_textdomain('meta-data-filter', false, dirname(plugin_basename(__FILE__)) . '/languages');

        //***
        $args = array(
            'labels' => array(
                'name' => __('MDTF Filters', 'meta-data-filter'),
                'singular_name' => __('Filters sections', 'meta-data-filter'),
                'add_new' => __('Add New Filter Section', 'meta-data-filter'),
                'add_new_item' => __('Add New Filter Section', 'meta-data-filter'),
                'edit_item' => __('Edit Filter Section', 'meta-data-filter'),
                'new_item' => __('New Filter Section', 'meta-data-filter'),
                'view_item' => __('View Filter Section', 'meta-data-filter'),
                'search_items' => __('Search Filter sections', 'meta-data-filter'),
                'not_found' => __('No Filter sections found', 'meta-data-filter'),
                'not_found_in_trash' => __('No Filter sections found in Trash', 'meta-data-filter'),
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
            'supports' => array('title', 'excerpt', 'tags'),
            'rewrite' => array('slug' => self::$slug),
            'show_in_admin_bar' => false,
            'menu_icon' => '',
            'taxonomies' => array(self::$slug_cat) // this is IMPORTANT
        );

        register_taxonomy(self::$slug_cat, array(self::$slug), array(
            "labels" => array(
                'name' => __('MDTF Categories', 'meta-data-filter'),
                'singular_name' => __('MDTF Categories', 'meta-data-filter'),
                'add_new' => __('Add New', 'meta-data-filter'),
                'add_new_item' => __('Add New Category', 'meta-data-filter'),
                'edit_item' => __('Edit Categories', 'meta-data-filter'),
                'new_item' => __('New Category', 'meta-data-filter'),
                'view_item' => __('View Category', 'meta-data-filter'),
                'search_items' => __('Search Categories', 'meta-data-filter'),
                'not_found' => __('No Categories found', 'meta-data-filter'),
                'not_found_in_trash' => __('No Categories found in Trash', 'meta-data-filter'),
                'parent_item_colon' => ''
            ),
            "singular_label" => __("Category", 'meta-data-filter'),
            'show_in_nav_menus' => false,
            'capabilities' => array('manage_terms'),
            'show_ui' => true,
            'term_group' => true,
            'hierarchical' => true,
            'query_var' => true,
            'rewrite' => array('slug' => self::$slug_cat),
            'orderby' => 'name'
        ));


        register_post_type(self::$slug, $args);
//flush_rewrite_rules(true); - do not use, makes woocommerce products 404
//+++
        wp_enqueue_script('jquery');

//***
        add_action("admin_init", array(__CLASS__, 'admin_init'), 1);
        add_action('admin_menu', array(__CLASS__, 'admin_menu'), 1);
        add_action('save_post', array(__CLASS__, 'save_post'), 1);
        if (!is_admin()) {
//!!! important to call front.js here, another way slider js for shortcodes doesn work
            wp_enqueue_script('meta_data_filter_widget', self::get_application_uri() . 'js/front.js', array('jquery', 'jquery-ui-core'/* , 'jquery-ui-slider' */));
        }
        add_action('wp_head', array(__CLASS__, 'wp_head'), 1);
        add_action('wp_footer', array(__CLASS__, 'wp_footer'), 9999);
        add_action('admin_head', array(__CLASS__, 'admin_head'), 1);
//***
        add_action('restrict_manage_posts', array(__CLASS__, "restrict_manage_posts"));
        add_filter('parse_query', array(__CLASS__, "parse_query"), 9999);
        add_action('pre_get_posts', array(__CLASS__, 'pre_get_posts'));
        add_filter("manage_" . self::$slug . "_posts_columns", array(__CLASS__, "show_edit_columns"));
        add_action("manage_" . self::$slug . "_posts_custom_column", array(__CLASS__, "show_edit_columns_content"));
        add_filter("manage_edit-" . self::$slug . "_sortable_columns", array(__CLASS__, "show_edit_sortable_columns"));
        add_action('load-edit.php', array(__CLASS__, "show_edit_sortable_columns_act"));
        add_action('widgets_init', array(__CLASS__, 'widgets_init'));
//***
        add_action('wp_ajax_meta_data_filter_set_sequence', array(__CLASS__, 'ajx_set_sequence'));
        add_action('wp_ajax_mdf_get_tax_options_in_widget', array(__CLASS__, 'get_tax_options_in_widget'));
        add_action('wp_ajax_mdf_change_meta_key', array(__CLASS__, 'change_meta_key'));
        add_action('wp_ajax_mdf_add_filter_item_to_widget', array(__CLASS__, 'add_filter_item_to_widget'));
//front ajax
        add_action('wp_ajax_mdf_draw_term_childs', array(__CLASS__, 'draw_term_childs_ajx'));
        add_action('wp_ajax_nopriv_mdf_draw_term_childs', array(__CLASS__, 'draw_term_childs_ajx'));
//+++
        add_filter('mdf_filter_taxonomies', array(__CLASS__, 'filter_mdf_filter_taxonomies'), 1);
        add_filter('mdf_filter_taxonomies2', array(__CLASS__, 'filter_mdf_filter_taxonomies2'), 1);
        $_REQUEST['meta_data_filter_args'] = array();
        add_filter('meta_data_filter_args', array(__CLASS__, 'filter_meta_data_filter_args'), 1);
        add_filter('plugin_action_links_' . MDTF_PLUGIN_NAME, array(__CLASS__, 'plugin_action_links'));
        add_filter('widget_text', 'do_shortcode');
        //we need it when have filter-item by post_title
        add_filter('posts_where', array('MDTF_HELPER', 'mdf_post_title_filter'));

        if (self::get_setting('marketing_short_links')) {
            //MDF_Marketing::init();
        }

        MetaDataFilterHtml::init();
        MetaDataFilterPage::init();
        MetaDataFilterShortcodes::init();
        MDTF_SORT_PANEL::init();
    }

    public static function draw_sort_by_filter() {
        //for compatibility only
    }

    /**
     * Show action links on the plugin screen
     */
    public static function plugin_action_links($links) {
        return array_merge(array(
            '<a href="' . admin_url('edit.php?post_type=meta_data_filter&page=mdf_settings') . '">' . __('Settings', 'meta-data-filter') . '</a>',
            '<a target="_blank" href="' . esc_url('http://wordpress-filter.com/documentation/') . '">' . __('Documentation', 'meta-data-filter') . '</a>'
                ), $links);

        return $links;
    }

    public static function get_plugin_ver() {
        $data = get_plugin_data(__FILE__);
        return $data['Version'];
    }

    public static function filter_meta_data_filter_args($args) {
        $args = array_merge($args, $_REQUEST['meta_data_filter_args']);
        return $args;
    }

//action to hook taxonomies array in query for shortcode [meta_data_filter_results]
    public static function filter_mdf_filter_taxonomies($tax_array) {
        static $tax_query_array = array();

        if (empty($tax_query_array)) {
            $tax_query_array = $tax_array;
        } else {
            if (!empty($tax_array)) {
                foreach ($tax_array as $taxes) {
                    $tax_query_array[] = $taxes;
                }
            }
        }

        return $tax_query_array;
    }

    //for recount
    public static function filter_mdf_filter_taxonomies2($tax_array) {
        if (isset($_REQUEST['MDF_ADDITIONAL_TAXONOMIES']) AND ! empty($_REQUEST['MDF_ADDITIONAL_TAXONOMIES'])) {
            foreach ($_REQUEST['MDF_ADDITIONAL_TAXONOMIES'] as $tax) {
                $tax_array[] = $tax;
            }
        }


        return $tax_array;
    }

    public static function admin_menu() {
        add_submenu_page('edit.php?post_type=' . self::$slug, __("MDTF Settings", 'meta-data-filter'), __("MDTF Settings", 'meta-data-filter'), 'manage_options', 'mdf_settings', array(__CLASS__, 'draw_settings_page'));
    }

    public static function wp_head() {
        wp_enqueue_script('jquery');
        //wp_enqueue_style('meta_data_filter_tester', self::get_application_uri() . 'css/tester.css');
        include MetaDataFilterCore::get_application_path() . 'js/js_vars.php';
    }

    public static function mdf_shortcode_quick_js_injection() {
        //not need, only for compatibility for old versions (<=1.1.3)
    }

    public static function wp_footer() {
        ?>
        <script type="text/javascript">
            jQuery(function () {
                jQuery.fn.life = function (types, data, fn) {
                    jQuery(this.context).on(types, this.selector, data, fn);
                    return this;
                };
            });
            //+++
        <?php
        $page_meta_data_filter = self::get_page_mdf_data();
        if ($page_meta_data_filter['mdf_widget_options']['search_result_page'] != 'self'):
            ?>
                var mdf_found_totally =<?php echo(isset($_REQUEST['meta_data_filter_count']) ? intval($_REQUEST['meta_data_filter_count']) : 0); ?>;
        <?php else: ?>
                var mdf_found_totally =<?php
            if (!isset($_REQUEST['meta_data_filter_found_posts'])) {
                $query = new WP_QueryMDFCounter($_REQUEST['meta_data_filter_args']);
                //$_REQUEST['meta_data_filter_count']=$query->found_posts;
                echo $query->found_posts;
            } else {
                echo $_REQUEST['meta_data_filter_found_posts'];
            }
            ?>;
        <?php endif; ?>

        </script>
        <?php
    }

    public static function admin_head() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-datepicker', array('jquery'));
        wp_enqueue_style('meta_data_filter_admin_total', self::get_application_uri() . 'css/admin_total.css');

        include MetaDataFilterCore::get_application_path() . 'js/js_vars.php';
        ?>
        <script type="text/javascript">
            jQuery(function () {
                jQuery.fn.life = function (types, data, fn) {
                    jQuery(this.context).on(types, this.selector, data, fn);
                    return this;
                };
            });
        </script>
        <?php
    }

    public static function widgets_init() {
        register_widget('MetaDataFilter_Search');
        //register_widget('MetaDataFilter_TaxSearch');
        //register_widget('MetaDataFilter_PostData');
    }

    public static function admin_init() {
        add_meta_box("meta_data_filter_options", __("Meta Data Filter Options", 'meta-data-filter'), array(__CLASS__, 'data_meta_box'), self::$slug, "normal", "high");
        add_meta_box("meta_data_filter_sequence", __("Filter sequence on front", 'meta-data-filter'), array(__CLASS__, 'data_meta_box_sequence'), self::$slug, "side", "high");
        add_meta_box("meta_data_filter_style", __("Meta Data Filter Style", 'meta-data-filter'), array(__CLASS__, 'data_meta_box_style'), self::$slug, "side", "high");
        MetaDataFilterPage::add_meta_box();
//***
        global $pagenow;
        if (is_admin() AND 'plugins.php' == $pagenow AND isset($_GET['activate'])) {
            if ($_GET['activate'] == 'true') {
                if (!get_option('meta_data_filter_was_activated')) {

                    $args = array(
                        'ID' => 0,
                        'post_status' => 'publish',
                        'post_title' => __("MDTF Results Page", 'meta-data-filter'),
                        'post_content' => '[meta_data_filter_results]',
                        'post_type' => 'page'
                    );

                    $post_id = wp_insert_post($args);
                    update_post_meta($post_id, '_wp_page_template', 'template-meta-data-filter.php');

//set settings
                    $data = array(
                        //'search_url'=>get_permalink($post_id),
                        'search_url' => home_url(),
                        'post_types' => array('post'),
                        'output_tpl' => 'search',
                        'tooltip_theme' => 'shadow',
                        'tooltip_icon' => '',
                        'tooltip_icon_w' => '',
                        'tooltip_icon_h' => '',
                        'tooltip_max_width' => 220,
                        'ajax_searching' => 0,
                        'marketing_short_links' => 0,
                        'use_chosen_js_w' => 0,
                        'use_chosen_js_s' => 0,
                        'use_custom_scroll_bar' => 0,
                        'use_custom_icheck' => 0,
                        'results_per_page' => 0,
                        'loading_text' => __("One Moment ...", 'meta-data-filter'),
                        'hide_search_button_shortcode' => 0,
                        'ignore_sticky_posts' => 1,
                        'icheck_skin' => 'flat',
                        'try_make_shop_page_ajaxed' => 0
                    );
                    update_option('meta_data_filter_settings', $data);
//***
                    @copy(self::get_application_path() . 'views/to_theme/template-meta-data-filter.php', get_template_directory() . '/template-meta-data-filter.php');
                    update_option('meta_data_filter_was_activated', 1);
                }
            }
        }
        add_action('admin_notices', array(__CLASS__, 'admin_notices'));
    }

    public static function admin_notices($notices) {
        $notices = "";
        //watch for template file existing
        if (!file_exists(get_template_directory() . '/template-meta-data-filter.php')) {
            if (!copy(self::get_application_path() . 'views/to_theme/template-meta-data-filter.php', get_template_directory() . '/template-meta-data-filter.php')) {
                $notices.='<div class="error fade"><p>' . __("WordPress Meta Data & Taxonomies Filter plugin can't copy the template file to your current theme from wp-content/plugins/meta-data-filter/views/to_theme/template-meta-data-filter.php. Please upload this file using ftp to your current theme.", 'meta-data-filter') . '</p></div>';
            }
        }

        echo $notices;
    }

    public static function data_meta_box() {
        global $post;
        wp_enqueue_script('meta_data_filter_admin', self::get_application_uri() . 'js/admin.js', array('jquery'));
        wp_enqueue_style('meta_data_filter_admin', self::get_application_uri() . 'css/admin.css');
        echo self::render_html(self::get_application_path() . 'views/data_meta_box.php', array('html_items' => self::get_html_items($post->ID)));
    }

    public static function data_meta_box_sequence() {
        global $post;
        ?>
        <input type="text" placeholder="<?php _e("integer number", 'meta-data-filter') ?>" name="sequence" value="<?php echo (int) get_post_meta($post->ID, 'sequence', true) ?>" />
        <?php
    }

    public static function data_meta_box_style() {
        global $post;
        ?>
        <h4><?php _e("The section max-heigh", 'meta-data-filter') ?></h4>
        <i><?php _e("If you have a lot of elements in the filter section you can set max-height. Set 0 if you do not need it.", 'meta-data-filter') ?></i><br />
        <br />
        <h5 style="margin:0;"><?php _e("On front (widget)", 'meta-data-filter') ?>:</h5>
        <input type="text" name="widget_section_max_height" placeholder="px" value="<?php echo (int) get_post_meta($post->ID, 'widget_section_max_height', true) ?>" /><br />
        <br />
        <h5 style="margin:0;"><?php _e("On backend", 'meta-data-filter') ?>:</h5>
        <input type="text" name="backend_section_max_height" placeholder="px" value="<?php echo (int) get_post_meta($post->ID, 'backend_section_max_height', true) ?>" /><br />
        <br />
        <h4><?php _e("The section toggle", 'meta-data-filter') ?>:</h4>
        <?php
        $toggles = array(
            0 => __("No toogle", 'meta-data-filter'),
            1 => __("Opened", 'meta-data-filter'),
            2 => __("Closed", 'meta-data-filter'),
        );
        $toggle = get_post_meta($post->ID, 'widget_section_toggle', true);
        ?>
        <select name="widget_section_toggle" class="postform">
            <?php foreach ($toggles as $key => $value) : ?>
                <option <?php echo selected($toggle, $key) ?> value="<?php echo $key ?>"><?php echo $value ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public static function save_post() {
        if (!empty($_POST)) {
            global $post;
            if (is_object($post)) {

                if ($post->post_type == self::$slug) {
//saving items of constructor
                    if (isset($_POST['html_item'])) {
                        update_post_meta($post->ID, 'html_items', $_POST['html_item']);
                    }
                    self::set_sequence($post->ID, $_POST['sequence']);
                    update_post_meta($post->ID, 'widget_section_max_height', $_POST['widget_section_max_height']);
                    update_post_meta($post->ID, 'backend_section_max_height', $_POST['backend_section_max_height']);
                    update_post_meta($post->ID, 'widget_section_toggle', $_POST['widget_section_toggle']);
                } else {
//saving for posts/pages or custom post types
                    if (isset($_POST[self::$slug_cat])) {
                        global $wpdb;
                        if ($_POST[self::$slug_cat] == -1 OR $_POST[self::$slug_cat] != get_post_meta($post->ID, self::$slug_cat, true)) {
//clean old values, because otherwise search is bad
                            $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE 'medafi_%' AND post_id=$post->ID");
                        }
//***
                        update_post_meta($post->ID, 'page_meta_data_filter', $_POST['page_meta_data_filter']); //synhro saving
                        if (!empty($_POST['page_meta_data_filter'])) {
                            foreach ($_POST['page_meta_data_filter'] as $key => $value) {
                                update_post_meta($post->ID, $key, $value); //this is for better searching
                            }
                        }
                        update_post_meta($post->ID, self::$slug_cat, $_POST[self::$slug_cat]);
                    }
                }
            }
        }
    }

//***********************************************************

    public static function show_edit_columns_content($column) {
        global $post;

        switch ($column) {
            case "items_count":
                echo(count(self::get_html_items($post->ID)));
                break;
            case self::$slug_cat:
                echo get_the_term_list($post->ID, self::$slug_cat, '', ',');
                break;
            case "sequence":
                wp_enqueue_style('meta_data_filter_admin', self::get_application_uri() . 'css/admin.css');
                wp_enqueue_script('meta_data_filter_admin', self::get_application_uri() . 'js/admin.js', array('jquery'));
                ?>
                <input type="text" pattern="[0-9]*" name="xxx" placeholder="<?php _e("digits only", 'meta-data-filter') ?>" value="<?php echo get_post_meta($post->ID, 'sequence', true) ?>" data-post-id="<?php echo $post->ID ?>" class="meta_data_filter_sequence" />
                <?php
                break;
        }
    }

    public static function show_edit_columns($columns) {
        $columns = array(
            "cb" => '<input type="checkbox" />',
            "title" => __("Title", 'meta-data-filter'),
            "items_count" => __("Html Items Count", 'meta-data-filter'),
            self::$slug_cat => __("Categories", 'meta-data-filter'),
            "sequence" => __("Filter sequence on front", 'meta-data-filter'),
        );

        return $columns;
    }

    public static function show_edit_sortable_columns($columns) {
        $columns['sequence'] = 'sequence';
        $columns[self::$slug_cat] = self::$slug_cat;
        return $columns;
    }

    public static function show_edit_sortable_columns_act() {
        add_filter('request', array(__CLASS__, 'sequence_col_sort_logic'));
        add_filter('request', array(__CLASS__, 'cat_col_sort_logic'));
    }

    public static function sequence_col_sort_logic($vars) {

        if (isset($vars['post_type']) AND self::$slug == $vars['post_type']) {
            if (isset($vars['orderby']) AND 'sequence' == $vars['orderby']) {
                $vars = array_merge($vars, array('meta_key' => 'sequence', 'orderby' => 'meta_value_num'));
            }
        }

        return $vars;
    }

    public static function cat_col_sort_logic($vars) {

        if (isset($vars['post_type']) AND self::$slug == $vars['post_type']) {
            if (isset($vars['orderby']) AND self::$slug_cat == $vars['orderby']) {
//$vars = array_merge($vars, array('taxonomy' => self::$slug_cat, 'orderby' => 'title'));
            }
        }

        return $vars;
    }

    public static function restrict_manage_posts() {
        global $typenow;
        global $wp_query;
        if ($typenow == self::$slug) {
            $mdf_taxonomy = get_taxonomy(self::$slug_cat);
            wp_dropdown_categories(array(
                'show_option_all' => __("Show All", 'meta-data-filter') . " " . $mdf_taxonomy->label,
                'taxonomy' => self::$slug_cat,
                'name' => self::$slug_cat,
                'orderby' => 'name',
                'selected' => @$wp_query->query[self::$slug_cat],
                'hierarchical' => true,
                'depth' => 3,
                'show_count' => true, // Show # listings in parens
                'hide_empty' => true,
            ));
        }
    }

    public static function parse_query($wp_query) {
        if (!is_admin()) {
            //for result output
            if (!isset($_REQUEST['meta_data_filter_works'])) {
                if (self::is_page_mdf_data() AND $wp_query->is_main_query()) {
                    $mdf_data = self::get_page_mdf_data();
                    $output_tpl = "";
                    if (isset($mdf_data['mdf_widget_options']['search_result_tpl']) AND ! empty($mdf_data['mdf_widget_options']['search_result_tpl'])) {
                        $output_tpl = $mdf_data['mdf_widget_options']['search_result_tpl'];
                    } else {
                        $settings = MetaDataFilter::get_settings();
                        if (empty($settings['output_tpl'])) {
                            $output_tpl = 'search';
                        } else {
                            $output_tpl = $settings['output_tpl'];
                        }
                    }

                    if ($output_tpl == 'search') {
                        $wp_query->set('post_type', $_GET['slg']);
                        if ($_GET['slg'] != 'post') {
                            $wp_query->is_post_type_archive = true;
                        }

                        $wp_query->is_tax = false;
                        $wp_query->is_tag = false;
                        $wp_query->is_home = false;
                        $wp_query->is_search = true;
                    }
                }
            }
        }

//***
        //for sorting in admin
        if (is_admin()) {
            global $pagenow;
            $post_type = self::$slug; // change HERE
            $taxonomy = self::$slug_cat; // change HERE
            $q_vars = &$wp_query->query_vars;
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
                $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
                $q_vars[$taxonomy] = $term->slug;
            }
        }
    }

    public static function pre_get_posts() {
        if (!is_admin()) {
            //for result output
            if (!isset($_REQUEST['meta_data_filter_works'])) {
                if (self::is_page_mdf_data()) {
                    $mdf_data = self::get_page_mdf_data();
                    if (isset($mdf_data['mdf_widget_options']['search_result_tpl']) AND ! empty($mdf_data['mdf_widget_options']['search_result_tpl'])) {
                        $output_tpl = $mdf_data['mdf_widget_options']['search_result_tpl'];
                    } else {
                        $settings = MetaDataFilter::get_settings();
                        if (empty($settings['output_tpl'])) {
                            $output_tpl = 'search';
                        } else {
                            $output_tpl = $settings['output_tpl'];
                        }
                    }

                    if ($output_tpl == 'search') {
                        $_REQUEST['mdf_do_not_render_shortcode_tpl'] = true;
                        do_shortcode('[meta_data_filter_results]');
                    }
                }
            }
        }
    }

    //+++

    public static function set_sequence($post_id, $sequence) {
        update_post_meta($post_id, 'sequence', (int) $sequence);
    }

    public static function ajx_set_sequence() {
        self::set_sequence($_REQUEST['post_id'], $_REQUEST['sequence']);
        exit;
    }

    public static function draw_settings_page() {
        wp_enqueue_script('jquery');
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script("jquery-ui-tabs");
        wp_enqueue_style('jquery-ui', self::get_application_uri() . 'css/jquery-ui-1.8.23.custom.css');
        //***
        if (isset($_POST['meta_data_filter_settings_submit']) AND is_admin()) {

            $service1_before = self::get_setting('marketing_short_links');
            update_option('meta_data_filter_settings', $_POST['meta_data_filter_settings']);
            $service1_after = self::get_setting('marketing_short_links');

//is state of mdf marketing were changed
            if ($service1_before != $service1_after) {
                ?>
                <script type="text/javascript">
                    window.location = "<?php echo admin_url("edit.php?post_type=" . self::$slug . "&page=mdf_settings") ?>";
                </script>
                <?php
            }
        }
        //***
        if (isset($_POST['meta_data_filter_assign_filter_id']) AND is_admin() AND ! empty($_POST['mass_filter_id'])) {
            //$posts = get_posts (array( 'post_type'=>$_POST['mass_filter_slug'], 'numberposts'=>-1 ));
            global $wpdb;
            $posts = $wpdb->get_results("SELECT ID from {$wpdb->posts} WHERE post_type='{$_POST['mass_filter_slug']}'");
            if (!empty($posts)) {
                $meta_key = 'meta_data_filter_cat';
                foreach ($posts as $post) {
                    update_post_meta($post->ID, $meta_key, $_POST['mass_filter_id']);
                }
            }
        }
        //***
        $data = self::get_settings();
        echo self::render_html(self::get_application_path() . 'views/settings.php', $data);
    }

//for widget

    public static function add_filter_item_to_widget($val = '') {
        $res = MetaDataFilterPage::get_html_sliders_by_filter_cat($_REQUEST['filter_cat']);
        ?>
        <?php if (!empty($res)): ?>
            <li><select class="widefat" style="width: 90%; margin-bottom: 9px;" name="<?php echo $_REQUEST['field_name'] ?>[]">
                    <?php foreach ($res as $key => $name) : ?>
                        <option value="<?php echo $key ?>" <?php if ($key == $val): ?>selected=""<?php endif; ?> class="level-0"><?php _e($name) ?></option>
                    <?php endforeach; ?>
                </select>&nbsp;<a href="#" class="button mdf_delete_filter_item">x</a></li>
        <?php endif; ?>
        <?php
        if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'mdf_add_filter_item_to_widget') {
            exit;
        }
        return;
    }

//ajax - for option of any taxonomy in the widget
    public static function get_tax_options_in_widget() {
        $terms_list = self::get_terms($_REQUEST['tax_name']);
        if (!empty($terms_list)) {
            $data = array();
            $data['tax_name'] = $_REQUEST['tax_name'];
            $data['show_how'] = $_REQUEST['show_how'];
            $data['select_size'] = isset($_REQUEST['select_size']) ? $_REQUEST['select_size'] : 1;
            $data['show_child_terms'] = isset($_REQUEST['show_child_terms']) ? $_REQUEST['show_child_terms'] : 0;
            $data['terms_section_toggle'] = isset($_REQUEST['terms_section_toggle']) ? $_REQUEST['terms_section_toggle'] : 0;
            $data['tax_title'] = isset($_REQUEST['tax_title']) ? $_REQUEST['tax_title'] : 1;
            $data['checkbox_max_height'] = isset($_REQUEST['checkbox_max_height']) ? $_REQUEST['checkbox_max_height'] : 0;
            $data['hidden_term'] = explode(',', $_REQUEST['hidden_terms']);
            $data['terms_list'] = $terms_list;
            echo self::render_html(self::get_application_path() . 'views/widgets/search_form/popup_terms_list.php', $data);
        } else {
            printf(__('No term are created for %s', 'meta-data-filter'), $_REQUEST['tax_name']);
        }
        exit;
    }

    public static function get_terms($taxonomy, $hide_empty = false, $get_childs = true, $selected = 0, $category_parent = 0) {
        $cats_objects = get_categories(array(
            'orderby' => 'name',
            'order' => 'ASC',
            'style' => 'list',
            'show_count' => 0,
            'hide_empty' => $hide_empty,
            'use_desc_for_title' => 1,
            'child_of' => 0,
            'hierarchical' => true,
            'title_li' => '',
            'show_option_none' => '',
            'number' => NULL,
            'echo' => 0,
            'depth' => 0,
            'current_category' => $selected,
            'pad_counts' => 0,
            'taxonomy' => $taxonomy,
            'walker' => 'Walker_Category'));

        $cats = array();
        if (!empty($cats_objects)) {
            foreach ($cats_objects as $value) {
                if (is_object($value) AND $value->category_parent == $category_parent) {
                    $cats[$value->term_id] = array();
                    $cats[$value->term_id]['term_id'] = $value->term_id;
                    $cats[$value->term_id]['name'] = $value->name;
                    $cats[$value->term_id]['count'] = $value->count;
                    if ($get_childs) {
                        $cats[$value->term_id]['childs'] = self::assemble_terms_childs($cats_objects, $value->term_id);
                    }
                }
            }
        }
        return $cats;
    }

//just for get_terms
    private static function assemble_terms_childs($cats_objects, $parent_id) {
        $res = array();
        foreach ($cats_objects as $value) {
            if ($value->category_parent == $parent_id) {
                $res[$value->term_id]['term_id'] = $value->term_id;
                $res[$value->term_id]['name'] = $value->name;
                $res[$value->term_id]['count'] = $value->count;
                $res[$value->term_id]['childs'] = self::assemble_terms_childs($cats_objects, $value->term_id);
            }
        }

        return $res;
    }

//ajax - draw html items for selected term in the widget
    public static function draw_term_childs_ajx() {
        $widget_options = array(
            'taxonomies_options_show_count' => ($_REQUEST['is_auto_submit'] == 'true' OR empty($_REQUEST['page_mdf'])) ? 'false' : 'true',
            //if auto submit do not count items count in childs
            'taxonomies_options_post_recount_dyn' => 'true',
            'taxonomies_options_hide_terms_0' => 'true',
            'meta_data_filter_cat' => $_REQUEST['mdf_cat'],
            'meta_data_filter_slug' => $_REQUEST['slug'],
                //'hide_meta_filter_values' => $_REQUEST['slug'],
        );
        $_GLOBALS['MDF_META_DATA_FILTER'] = $_REQUEST['page_mdf'];
        self::draw_term_childs($_REQUEST['type'], $_REQUEST['mdf_parent_id'], 0, $_REQUEST['tax_name'], true, $_REQUEST['hide'], $widget_options);
        exit;
    }

    public static function draw_term_childs($type = 'select', $mdf_parent_id = 0, $selected_id = 0, $tax_name = 0, $is_ajax = true, $hide_string_ids = '', $widget_options = array()) {
        $hide = array();
        if (!empty($hide_string_ids)) {
            $hide = explode(',', $hide_string_ids);
        }
//+++
        $hide_empty = false;
        //taxonomies_options_hide_terms_0
        if (isset($widget_options['taxonomies_options_hide_terms_0'])) {
            $hide_empty = (bool) ($widget_options['taxonomies_options_hide_terms_0']);
        }
        //print_r($widget_options);
        $terms = self::get_terms($tax_name, $hide_empty, false, 0, $mdf_parent_id);
        switch ($type) {
            case 'select':
                self::draw_term_childs_select($terms, $mdf_parent_id, $selected_id, $hide, $tax_name, $is_ajax, $hide_string_ids, $widget_options);
                break;
            case 'checkbox':
                self::draw_term_childs_checkbox($terms, $mdf_parent_id, (array) $selected_id, $hide, $tax_name, $is_ajax, $hide_string_ids, $widget_options);
                break;
            default:
                break;
        }
    }

    private static function draw_term_childs_select($terms, $mdf_parent_id = 0, $selected_id = 0, $hide, $tax_name = 0, $is_ajax = true, $hide_string_ids = '', $widget_options = array()) {
        if (!empty($terms)) {
            $select_size = isset($widget_options['taxonomies_options_select_size'][$tax_name]) ? (int) $widget_options['taxonomies_options_select_size'][$tax_name] : 1;
            ?>
            <select size="<?php echo $select_size; ?>" name="mdf[taxonomy][select][<?php echo $tax_name ?>][]" class="mdf_taxonomy" data-tax-name="<?php echo $tax_name ?>" data-hide="<?php echo $hide_string_ids ?>">
                <option value="-1">
                    <?php
                    $tax_title = "";
                    $tax_title_busy_key = 0;
                    $tax_title_array = "";
                    if (isset($widget_options['taxonomies_options_tax_title'][$tax_name]) AND ! empty($widget_options['taxonomies_options_tax_title'][$tax_name])) {
                        $tax_title = $widget_options['taxonomies_options_tax_title'][$tax_name];
                        if (substr_count($tax_title, '^')) {
                            $tax_title_array = explode('^', $tax_title);
                            $tax_title = explode('^', $tax_title);
                            if ($mdf_parent_id == 0) {
                                $tax_title = $tax_title[0];
                            } else {
                                $parent = get_term_by('id', $mdf_parent_id, $tax_name);
                                $index = 1;
                                while ($parent->parent != 0) {
                                    $parent = get_term_by('id', $parent->parent, $tax_name);
                                    $index ++;
                                }
                                if (isset($tax_title[$index])) {
                                    $tax_title = $tax_title[$index];
                                    $tax_title_busy_key = $index;
                                } else {
                                    $tax_title = __(MetaDataFilterHtml::get_term_label_by_name($tax_name));
                                }
                            }
                        }
                    } else if (isset($widget_options['shortcode_taxonomies_title'][$tax_name]) AND ! empty($widget_options['shortcode_taxonomies_title'][$tax_name])) {
                        //for shortcodes
                        $tax_title = $widget_options['shortcode_taxonomies_title'][$tax_name];
                        if (substr_count($tax_title, '^')) {
                            $tax_title_array = explode('^', $tax_title);
                            $tax_title = explode('^', $tax_title);
                            if ($mdf_parent_id == 0) {
                                $tax_title = $tax_title[0];
                            } else {
                                $parent = get_term_by('id', $mdf_parent_id, $tax_name);
                                $index = 1;
                                while ($parent->parent != 0) {
                                    $parent = get_term_by('id', $parent->parent, $tax_name);
                                    $index ++;
                                }
                                if (isset($tax_title[$index])) {
                                    $tax_title = $tax_title[$index];
                                    $tax_title_busy_key = $index;
                                } else {
                                    $tax_title = __(MetaDataFilterHtml::get_term_label_by_name($tax_name));
                                }
                            }
                        }
                    } else {
                        $tax_title = __(MetaDataFilterHtml::get_term_label_by_name($tax_name));
                    }
                    echo $tax_title;
                    ?>
                </option>
                <?php foreach ($terms as $term_id => $term) : ?>
                    <?php
                    //do not output hidden terms
                    if (in_array($term_id, $hide)) {
                        continue;
                    }
                    ?>
                    <?php
                    $tax_count = -1;
                    $tax_count_strting = "";
                    $show_option = true;
                    if ($widget_options['taxonomies_options_show_count'] == 'true' OR $widget_options['taxonomies_options_show_count'] == 1) {
                        $tax_count = self::get_tax_count($term, $tax_name, $widget_options, 'select');
                        $tax_count_strting = ' (' . $tax_count . ')';
                        if ($widget_options['taxonomies_options_post_recount_dyn'] == 'true' OR $widget_options['taxonomies_options_post_recount_dyn'] == 1) {
                            if ($widget_options['taxonomies_options_hide_terms_0'] == 'true' OR $widget_options['taxonomies_options_hide_terms_0'] == 1) {
                                if (!$tax_count) {
                                    $show_option = false;
                                }
                            }
                        }
                    }
                    ?>


                    <?php if ($show_option): ?>
                        <option <?php if ($tax_count == 0 AND ( $widget_options['taxonomies_options_show_count'] == 'true' OR $widget_options['taxonomies_options_show_count'] == 1)): ?>disabled<?php endif; ?> value="<?php echo $term_id ?>" <?php if ($selected_id == $term_id): ?>selected<?php endif; ?>>
                            <?php _e($term['name']) ?><?php echo $tax_count_strting; ?>
                        </option>
                    <?php endif; ?>


                <?php endforeach; ?>
            </select>


            <?php
            $show_child_terms1 = (isset($widget_options['taxonomies_options_show_child_terms']) AND $widget_options['taxonomies_options_show_child_terms'][$tax_name] == 1) ? true : false;
            $show_child_terms2 = (isset($widget_options['taxonomies_show_child_terms'][$tax_name]) AND $widget_options['taxonomies_show_child_terms'][$tax_name] == 1) ? true : false;
            $show_child_terms = ($show_child_terms1 OR $show_child_terms2);
            ?>

            <?php if ($show_child_terms): ?>

                <?php //if($is_ajax):       ?>
                <div class="mdf_taxonomy_child_container"><?php MetaDataFilterHtml::draw_tax_loader(); ?></div>
                <?php //endif;       ?>


                <?php
                $show_butaforia = false;

                if (!empty($tax_title_array) AND is_array($tax_title_array)) {
                    foreach ($tax_title_array as $key => $value) {
                        if ($key <= $tax_title_busy_key) {
                            unset($tax_title_array[$key]);
                        } else {
                            break;
                        }
                    }


                    if ($selected_id == -1 OR $selected_id == 0) {
                        $show_butaforia = true;
                    }

                    if (count($tax_title_array) == 0) {
                        $show_butaforia = true;
                    }
                }
                ?>


                <?php if ($show_butaforia): ?>
                    <?php foreach ($tax_title_array as $k => $title) : ?>
                        <div class="mdf_taxonomy_child_container2">
                            <select data-level-id="<?php echo($k + 1) ?>" disabled="disabled">
                                <option value="-1"><?php echo $title ?></option>
                            </select>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>


            <?php else: ?>
                <?php if ($is_ajax): ?>
                    <div class="mdf_taxonomy_child_container"><?php MetaDataFilterHtml::draw_tax_loader(); ?></div>
                <?php endif; ?>
            <?php endif; ?>




            <?php
        }
        if ($is_ajax) {
            exit;
        }
    }

    private static function draw_term_childs_checkbox($terms, $mdf_parent_id = 0, $selected_ids = array(), $hide, $tax_name = 0, $is_ajax = true, $hide_string_ids = '', $widget_options = array()) {
        if (!empty($terms)) {
            ?>
            <ul class="mdf_taxonomy_check_list">

                <?php foreach ($terms as $term_id => $term) : ?>
                    <?php
                    //do not output hidden terms
                    if (in_array($term_id, $hide)) {
                        continue;
                    }
                    ?>
                    <li>

                        <?php
                        $tax_count = -1;
                        $tax_count_strting = "";
                        $show_option = true;
                        if ($widget_options['taxonomies_options_show_count'] == 'true' OR $widget_options['taxonomies_options_show_count'] == 1) {
                            $tax_count = self::get_tax_count($term, $tax_name, $widget_options, 'checkbox');
                            $tax_count_strting = ' (' . $tax_count . ')';
                            if ($widget_options['taxonomies_options_post_recount_dyn'] == 'true' OR $widget_options['taxonomies_options_post_recount_dyn'] == 1) {
                                if ($widget_options['taxonomies_options_hide_terms_0'] == 'true' OR $widget_options['taxonomies_options_hide_terms_0'] == 1) {
                                    if (!$tax_count) {
                                        $show_option = false;
                                    }
                                }
                            }
                        }
                        $childs_ids = array();
                        if ($show_option) {
                            $childs_ids = get_term_children($term_id, $tax_name);
                        }

                        $unique_id = uniqid();
                        ?>

                        <?php if ($show_option): ?>
                            <input <?php if ($tax_count == 0 AND ( $widget_options['taxonomies_options_show_count'] == 'true' OR $widget_options['taxonomies_options_show_count'] == 1)): ?>disabled<?php endif; ?> type="checkbox" name="mdf[taxonomy][checkbox][<?php echo $tax_name ?>][]" class="mdf_taxonomy_checkbox <?php if (!empty($childs_ids)): ?>mdf_has_childs<?php endif; ?>" value="<?php echo $term_id ?>" <?php if (in_array($term_id, $selected_ids)): ?>checked<?php endif; ?> data-tax-name="<?php echo $tax_name ?>" data-hide="<?php echo $hide_string_ids ?>" id="tax_check_<?php echo $term_id ?>_<?php echo $unique_id ?>" />&nbsp;<label <?php if (in_array($term_id, $selected_ids)): ?>class="mdf_taxonomy_checked"<?php endif; ?> for="tax_check_<?php echo $term_id ?>_<?php echo $unique_id ?>"><?php _e($term['name']); ?><?php echo $tax_count_strting ?></label>

                            <?php if (array_intersect($childs_ids, $selected_ids) OR ( !empty($childs_ids) AND in_array($term_id, $selected_ids))): ?>
                                <div class="mdf_taxonomy_child_container" style="display: block;">
                                    <?php
                                    $terms = self::get_terms($tax_name, true, false, 0, $term_id);
                                    self::draw_term_childs_checkbox($terms, $term_id, $selected_ids, $hide, $tax_name, $is_ajax, $hide_string_ids, $widget_options);
                                    ?>
                                </div>
                            <?php else: ?>
                                <div class="mdf_taxonomy_child_container">
                                    <?php MetaDataFilterHtml::draw_tax_loader(); ?>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>

                    </li>
                <?php endforeach; ?>
            </ul>

            <?php
        }
        if ($is_ajax) {
            exit;
        }
    }

//return count value for taxonomies
    private static function get_tax_count($term, $tax_name, $widget_options, $html_type) {
        $count = 0;
        if (!$widget_options['taxonomies_options_show_count']) {
            return 0;
        }

        $slug = $widget_options['meta_data_filter_slug'];

        if ($widget_options['taxonomies_options_show_count']) {
            if ($widget_options['taxonomies_options_post_recount_dyn'] AND self::is_page_mdf_data()) {
//isset($_REQUEST['page_mdf']) instead of isset($_GET['page_mdf']) for ajax requests
                $_GLOBALS['MDF_TAX_USE_CACHE_ARRAY'] = false;
                $page_meta_data_filter = self::get_page_mdf_data();


                if (isset($page_meta_data_filter['taxonomy'])) {
                    $taxonomies = $page_meta_data_filter['taxonomy'];
                }

                if (!isset($taxonomies[$html_type])) {
                    $taxonomies[$html_type] = array();
                }
                if (!isset($taxonomies[$html_type][$tax_name]) OR ! is_array($taxonomies[$html_type][$tax_name])) {
                    $taxonomies[$html_type][$tax_name] = array();
                }

                if ($html_type == 'select') {
                    if (!in_array($term['term_id'], $taxonomies[$html_type][$tax_name])) {
                        $taxonomies[$html_type][$tax_name][] = $term['term_id'];
                    }
                    /*
                      foreach ($taxonomies[$html_type][$tax_name] as $k => $v) {
                      if($v == -1){
                      unset($taxonomies[$html_type][$tax_name][$k]);
                      }
                      }
                     */
                }


                if ($html_type == 'checkbox') {
//if (!in_array($term['term_id'], $taxonomies[$html_type][$tax_name])) {
                    $taxonomies[$html_type][$tax_name] = array();
                    $taxonomies[$html_type][$tax_name][] = $term['term_id'];
//}
//ATTENTION
//look all taxes in selects and leave only last one for each tax name. Reason: http://clip2net.com/s/73w4a2
                    /*
                      if (isset($taxonomies['select'])) {
                      if (!empty($taxonomies['select']) AND is_array($taxonomies['select'])) {
                      foreach ($taxonomies['select'] as $t_name => $tax_ids) {
                      if (is_array($tax_ids) AND count($tax_ids) > 1) {
                      foreach ($taxonomies['select'][$t_name] as $k => $id) {
                      if (!$id) {
                      unset($taxonomies['select'][$t_name][$k]);
                      }
                      }
                      if (count($taxonomies['select'][$t_name]) > 1) {
                      $last_tax_id = end($taxonomies['select'][$t_name]);
                      $taxonomies['select'][$t_name] = array($last_tax_id);
                      }
                      }
                      }
                      }
                      }
                     *
                     */
//************************************************
                }


                $page_meta_data_filter['taxonomy'] = $taxonomies;


                $count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, 0, 0, $slug, 'tax_item');


                $_GLOBALS['MDF_TAX_USE_CACHE_ARRAY'] = true; //!!!important, another way logic error
                $_GLOBALS['MDF_TAX_QUERY_ARRAY'] = array(); //!!!important, another way logic error
            } else {
                $tax_query_array = array();
                $tax_query_array[] = array(
                    'taxonomy' => $tax_name,
                    'field' => 'term_id',
                    'terms' => array($term['term_id'])
                );
                $tax_query_array = apply_filters('mdf_filter_taxonomies2', $tax_query_array);
//+++
                $meta_query_array = array();
                //for WOOCOMMERCE hidden products ***
                if ($slug == 'product' AND class_exists('WooCommerce')) {
                    $meta_query_array[] = array(
                        'key' => '_visibility',
                        'value' => array('catalog', 'visible'),
                        'compare' => 'IN'
                    );
                    //for out stock products
                    if (self::get_setting('exclude_out_stock_products')) {
                        $meta_query_array[] = array(
                            'key' => '_stock_status',
                            'value' => array('instock'),
                            'compare' => 'IN'
                        );
                    }
                    //***
                }
                //+++
                //Trick - how to hide post from search
                $meta_query_array[] = array(
                    'key' => 'mdf_hide_post',
                    'value' => 'out',
                    'compare' => 'NOT EXISTS'
                );
                //*** fixed 20-10-2014
                //print_r($widget_options);exit;
                //if (isset($widget_options['hide_meta_filter_values']) AND $widget_options['hide_meta_filter_values'] != 'true') {
                if ($widget_options['meta_data_filter_cat'] > 0/* OR @$widget_options['tax_only_in_filter'] == 0 for shortcodes */) {
                    $meta_query_array[] = array(
                        'key' => 'meta_data_filter_cat',
                        'value' => (int) $widget_options['meta_data_filter_cat'],
                        'compare' => '='
                    );
                }
                //***
                $args = array(
                    'post_type' => $slug,
                    'meta_query' => $meta_query_array,
                    'tax_query' => $tax_query_array,
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
                $count = $counter_obj->post_count;
            }
        }

        return $count;
    }

//ajax
    public static function change_meta_key() {
        $post_id = intval($_REQUEST['post_id']);
        $old_key = $_REQUEST['old_key'];
        $new_key = $_REQUEST['new_key'];
        global $wpdb;
        $request = array('content' => '', 'notice' => '');
//+++
//check for 'medafi_' prefix
        if ($new_key == 'medafi_') {
            $request['notice'] = __('Attention! Meta key must be begin from medafi_ but can not such the name!!', 'meta-data-filter');
            wp_die(json_encode($request));
        }

        if (substr($new_key, 0, strlen('medafi_')) != 'medafi_') {
            $request['notice'] = __('Attention! Meta key must be begin from medafi_!!', 'meta-data-filter');
            wp_die(json_encode($request));
        }

//firstly lets check for new key existing in current post of data constructor
        $items = array_keys(self::get_html_items($post_id));
        if (in_array($new_key, $items)) {
            $request['notice'] = __('Such key already exists in current filter or its the same fileld. Try another key!', 'meta-data-filter');
            wp_die(json_encode($request));
        }
//second check the new key in other filters
        $sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='%s'", self::$slug);
        $results = $wpdb->get_results($sql, ARRAY_N);
        $ids = array();
        if (!empty($results)) {
            foreach ($results as $value) {
                if ($value[0] == $post_id) {
                    continue;
                }
                $ids[] = $value[0];
            }
        }
        if (!empty($ids)) {
            foreach ($ids as $p_id) {
                $get_html_items = self::get_html_items($p_id);
                if (is_array($get_html_items)) {
                    $items = array_keys($get_html_items);
                    if (is_array($items) AND in_array($new_key, $items)) {
                        $post_info = get_post($p_id);
                        $term_list = wp_get_post_terms($p_id, self::$slug_cat, array("fields" => "names"));
                        $term_list = implode(',', $term_list);
//***
                        $request['notice'] = sprintf(__('Filter `%s` (%s) already has such key. Filter status: `%s`!', 'meta-data-filter'), $post_info->post_title, $term_list, $post_info->post_status);
                        wp_die(json_encode($request));
                    }
                }
            }
        }
//third - all OK, lets change it
        $items = self::get_html_items($post_id);
        $new_items = array();
        if (!empty($items) AND is_array($items)) {
            foreach ($items as $key => $value) {
                if ($key == $old_key) {
                    $value['meta_key'] = $new_key;
                    $new_items[$new_key] = $value;
                    continue;
                }
                $new_items[$key] = $value;
            }
            update_post_meta($post_id, 'html_items', $new_items);
        }

//replace old key by new one in posts which are already uses old key
//get all posts whish are uses old key and replace keys in synhro_data_array
        $sql = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='%s'", $old_key);
        $results = $wpdb->get_results($sql, ARRAY_N);
        if (!empty($results) AND is_array($results)) {
            foreach ($results as $value) {
                $p_id = $value[0];
                $synhro_data_array = get_post_meta($p_id, 'page_meta_data_filter', true);
                if (!empty($synhro_data_array) AND is_array($synhro_data_array)) {
                    $tmp_new_data = array();
                    foreach ($synhro_data_array as $key => $value) {
                        if ($key == $old_key) {
                            $tmp_new_data[$new_key] = $value;
                            continue;
                        }
                        $tmp_new_data[$key] = $value;
                    }
                    update_post_meta($p_id, 'page_meta_data_filter', $tmp_new_data);
                }
            }
        }
//update old keys in posts which are using it
        $wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key = %s", $new_key, $old_key));
        $request['notice'] = __('Updated', 'meta-data-filter');
        $request['content'] = self::render_html(self::get_application_path() . 'views/add_item_to_data_group.php', array('itemdata' => @$new_items[$new_key], 'uniqid' => $new_key));
        wp_die(json_encode($request));
    }

    public static function add_additional_taxonomies($additional_taxonomies, $register_request = TRUE, $eq = '=') {
        $additional_tax_query_array = array();

        if (!empty($additional_taxonomies)) {
            $tmp = explode('^', $additional_taxonomies);
            if (!empty($tmp) AND is_array($tmp)) {
                foreach ($tmp as $tmp2) {
                    $tmp2 = explode($eq, $tmp2);
                    $taxonomy = $tmp2[0];
                    if (!empty($tmp2) AND is_array($tmp2)) {
                        $terms = explode(',', $tmp2[1]);
                        $terms = self::service_additional_tax_query_array($taxonomy, $terms);
                        if (!empty($terms)) {
                            $additional_tax_query_array[] = array(
                                'taxonomy' => $taxonomy,
                                'field' => 'term_id',
                                'terms' => $terms
                            );
                        }
                    }
                }
                if ($register_request) {
                    $_REQUEST['MDF_ADDITIONAL_TAXONOMIES'] = $additional_tax_query_array;
                    //print_r($additional_tax_query_array);
                }
            }
        }

        return $additional_tax_query_array;
    }

    //utilit func: for additional syntax in widgets and shortcodes
    private static function service_additional_tax_query_array($taxonomy, $terms) {
        global $wp_query;

        foreach ($terms as $kk => $term_id) {
            if ($term_id == 'current' AND is_tax($taxonomy)) {
                $term = $wp_query->queried_object;
                if ($term->taxonomy == $taxonomy) {
                    $terms[$kk] = $term->term_id;
                }
            }
        }
        //clean of 'current'
        foreach ($terms as $kk => $term_id) {
            if ($term_id == 'current') {
                unset($terms[$kk]);
            }
        }

        return $terms;
    }

    //util func
    public function service_additional_tax_query_string($additional_taxonomies_string) {
        $result_string = "";
        $tax_array = self::add_additional_taxonomies($additional_taxonomies_string, false);
        if (!empty($tax_array)) {
            foreach ($tax_array as $value) {
                $result_string.=$value['taxonomy'] . '=' . implode(',', $value['terms']);
            }
        }

        return $result_string;
    }

    //***

    public static function draw_front_toggle($section_toggle = 0) {
        if ($section_toggle > 0) {
            if ($section_toggle == 1) {
                //opened
                ?>
                <a href="#" class="mdf_front_toggle mdf_front_toggle_opened" data-condition="opened"><?php _e(MetaDataFilterCore::get_setting('toggle_close_sign') ? MetaDataFilterCore::get_setting('toggle_close_sign') : '-') ?></a>
                <?php
            } else {
                //closed
                ?>
                <a href="#" class="mdf_front_toggle mdf_front_toggle_closed" data-condition="closed"><?php _e(MetaDataFilterCore::get_setting('toggle_open_sign') ? MetaDataFilterCore::get_setting('toggle_open_sign') : '+') ?></a>
                <?php
            }
        }
    }

    //need to hack wp query of any 3th party shortcodes
    public static function rewrite_search_query_args($args = array()) {
        if (self::is_page_mdf_data()) {
            $_REQUEST['mdf_do_not_render_shortcode_tpl'] = true;
            $_REQUEST['mdf_get_query_args_only'] = true;
            do_shortcode('[meta_data_filter_results]');
            $args = $_REQUEST['meta_data_filter_args'];
        }

        //$_REQUEST['meta_data_filter_found_posts'] - do not forget to = $wp_query->found_posts;

        return $args;
    }

}

add_action('init', array('MetaDataFilter', 'init'), 1);

//***


class MetaDataFilter_Search extends WP_Widget {

//Widget Setup
    function __construct() {
//Basic settings
        $settings = array('classname' => __CLASS__, 'description' => __('Meta Data Filter - search and filter data by taxonomies and meta fileds.', 'meta-data-filter'));

//Creation
        $this->WP_Widget(__CLASS__, __('MDTF', 'meta-data-filter'), $settings);
    }

//Widget view
    function widget($args, $instance) {
        $args['instance'] = $instance;
        $args['sidebar_id'] = $args['id'];
        $args['sidebar_name'] = $args['name'];
        MetaDataFilter::front_script_includer();
        echo MetaDataFilterHtml::render_html(MetaDataFilterCore::get_application_path() . 'views/widgets/search.php', $args);
    }

//Update widget
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance[MetaDataFilterCore::$slug_cat] = $new_instance[MetaDataFilterCore::$slug_cat];
        $instance['meta_data_filter_slug'] = $new_instance['meta_data_filter_slug'];
        $instance['hide_meta_filter_values'] = $new_instance['hide_meta_filter_values'];
        $instance['hide_tax_filter_values'] = $new_instance['hide_tax_filter_values'];
        $instance['show_checkbox_items_count'] = $new_instance['show_checkbox_items_count'];
        $instance['show_select_items_count'] = $new_instance['show_select_items_count'];
        $instance['show_slider_items_count'] = $new_instance['show_slider_items_count'];
        $instance['show_items_count_dynam'] = $new_instance['show_items_count_dynam'];
        $instance['hide_items_where_count_0'] = $new_instance['hide_items_where_count_0'];
        $instance['act_without_button'] = $new_instance['act_without_button'];
        $instance['and_or'] = $new_instance['and_or'];
        $instance['title_for_any'] = $new_instance['title_for_any'];
        $instance['title_for_filter_button'] = $new_instance['title_for_filter_button'];
        $instance['show_filter_button_after_each_block'] = $new_instance['show_filter_button_after_each_block'];
        $instance['taxonomies'] = $new_instance['taxonomies'];
        $instance['taxonomies_options_hide'] = $new_instance['taxonomies_options_hide'];
        $instance['taxonomies_options_show_how'] = $new_instance['taxonomies_options_show_how'];
        $instance['taxonomies_options_select_size'] = $new_instance['taxonomies_options_select_size'];
        $instance['taxonomies_options_tax_title'] = $new_instance['taxonomies_options_tax_title'];
        $instance['taxonomies_options_checkbox_max_height'] = $new_instance['taxonomies_options_checkbox_max_height'];
        $instance['taxonomies_options_behaviour'] = $new_instance['taxonomies_options_behaviour'];
        $instance['taxonomies_options_show_count'] = $new_instance['taxonomies_options_show_count'];
        $instance['taxonomies_options_post_recount_dyn'] = $new_instance['taxonomies_options_post_recount_dyn'];
        $instance['taxonomies_options_hide_terms_0'] = $new_instance['taxonomies_options_hide_terms_0'];
        $instance['taxonomies_options_show_child_terms'] = $new_instance['taxonomies_options_show_child_terms'];
        $instance['taxonomies_options_terms_section_toggle'] = $new_instance['taxonomies_options_terms_section_toggle'];
        $instance['show_reset_button'] = $new_instance['show_reset_button'];
        $instance['show_found_totally'] = $new_instance['show_found_totally'];
        $instance['ajax_items_recount'] = $new_instance['ajax_items_recount'];
        $instance['search_result_page'] = $new_instance['search_result_page'];
        $instance['search_result_tpl'] = $new_instance['search_result_tpl'];
        $instance['show_items_count_text'] = $new_instance['show_items_count_text'];
        $instance['reset_link'] = $new_instance['reset_link'];
        $instance['ajax_output'] = $new_instance['ajax_output'];
        $instance['woo_search_panel_id'] = $new_instance['woo_search_panel_id'];
        $instance['additional_taxonomies'] = $new_instance['additional_taxonomies'];
        $instance['filter_categories_addit'] = $new_instance['filter_categories_addit'];
        $instance['ajax_results'] = $new_instance['ajax_results'];


        return $instance;
    }

//Widget form
    function form($instance) {
        wp_enqueue_script('mdf_popup', MetaDataFilter::get_application_uri() . 'js/pn_popup/pn_advanced_wp_popup.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'));
        wp_enqueue_style('mdf_popup', MetaDataFilter::get_application_uri() . 'js/pn_popup/styles.css');
//Defaults
        $defaults = array(
            'title' => __('Meta Data Filter', 'meta-data-filter'),
            MetaDataFilterCore::$slug_cat => 0,
            'meta_data_filter_slug' => '',
            'hide_meta_filter_values' => 'false',
            'hide_tax_filter_values' => 'false',
            'show_checkbox_items_count' => 'true',
            'show_select_items_count' => 'true',
            'show_slider_items_count' => 'true',
            'show_items_count_dynam' => 'true',
            'hide_items_where_count_0' => 'false',
            'act_without_button' => 'true',
            'and_or' => 'and',
            'title_for_any' => __('Any', 'meta-data-filter'),
            'title_for_filter_button' => __('Filter', 'meta-data-filter'),
            'show_filter_button_after_each_block' => 'false',
            'taxonomies' => array(),
            'taxonomies_options_hide' => array(),
            'taxonomies_options_show_how' => 'select',
            'taxonomies_options_select_size' => 1,
            'taxonomies_options_tax_title' => '',
            'taxonomies_options_checkbox_max_height' => 0,
            'taxonomies_options_behaviour' => 'AND',
            'taxonomies_options_show_count' => 'true',
            'taxonomies_options_post_recount_dyn' => 'true',
            'taxonomies_options_hide_terms_0' => 'false',
            'taxonomies_options_show_child_terms' => 0,
            'taxonomies_options_terms_section_toggle' => 0,
            'show_reset_button' => 'true',
            'show_found_totally' => 'true',
            'ajax_items_recount' => 'false',
            'search_result_page' => '',
            'search_result_tpl' => '',
            'reset_link' => '',
            'show_items_count_text' => __('Found &lt;span&gt;%s&lt;/span&gt; items', 'meta-data-filter'),
            'ajax_output' => 'false',
            'woo_search_panel_id' => 0,
            'additional_taxonomies' => '',
            'filter_categories_addit' => '',
            'ajax_results' => 'false'
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        $args = array();
        $args['instance'] = $instance;
        $args['widget'] = $this;
        wp_enqueue_script('meta_data_filter_widget', MetaDataFilterCore::get_application_uri() . 'js/widget_back.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
        echo MetaDataFilterHtml::render_html(MetaDataFilterCore::get_application_path() . 'views/widgets/search_form.php', $args);
    }

}

class MetaDataFilter_TaxSearch extends WP_Widget {

//Widget Setup
    function __construct() {
//Basic settings
        $settings = array('classname' => __CLASS__, 'description' => __('MDTF Taxonomies - search and filter data by taxonomies only.', 'meta-data-filter'));

//Creation
        $this->WP_Widget(__CLASS__, __('MDTF Taxonomies only', 'meta-data-filter'), $settings);
    }

//Widget view
    function widget($args, $instance) {
        $args['instance'] = $instance;
        $args['sidebar_id'] = $args['id'];
        $args['sidebar_name'] = $args['name'];
        MetaDataFilter::front_script_includer();
        echo MetaDataFilterHtml::render_html(MetaDataFilterCore::get_application_path() . 'views/widgets/search.php', $args);
    }

//Update widget
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance[MetaDataFilterCore::$slug_cat] = $new_instance[MetaDataFilterCore::$slug_cat];
        $instance['meta_data_filter_slug'] = $new_instance['meta_data_filter_slug'];
        $instance['hide_meta_filter_values'] = $new_instance['hide_meta_filter_values'];
        $instance['hide_tax_filter_values'] = $new_instance['hide_tax_filter_values'];
        $instance['show_checkbox_items_count'] = $new_instance['show_checkbox_items_count'];
        $instance['show_select_items_count'] = $new_instance['show_select_items_count'];
        $instance['show_slider_items_count'] = $new_instance['show_slider_items_count'];
        $instance['show_items_count_dynam'] = $new_instance['show_items_count_dynam'];
        $instance['hide_items_where_count_0'] = $new_instance['hide_items_where_count_0'];
        $instance['act_without_button'] = 0;
        $instance['and_or'] = $new_instance['and_or'];
        $instance['title_for_any'] = $new_instance['title_for_any'];
        $instance['title_for_filter_button'] = $new_instance['title_for_filter_button'];
        $instance['show_filter_button_after_each_block'] = $new_instance['show_filter_button_after_each_block'];
        $instance['taxonomies'] = $new_instance['taxonomies'];
        $instance['taxonomies_options_hide'] = $new_instance['taxonomies_options_hide'];
        $instance['taxonomies_options_show_how'] = $new_instance['taxonomies_options_show_how'];
        $instance['taxonomies_options_select_size'] = $new_instance['taxonomies_options_select_size'];
        $instance['taxonomies_options_tax_title'] = $new_instance['taxonomies_options_tax_title'];
        $instance['taxonomies_options_checkbox_max_height'] = $new_instance['taxonomies_options_checkbox_max_height'];
        $instance['taxonomies_options_behaviour'] = $new_instance['taxonomies_options_behaviour'];
        $instance['taxonomies_options_show_count'] = $new_instance['taxonomies_options_show_count'];
        $instance['taxonomies_options_post_recount_dyn'] = $new_instance['taxonomies_options_post_recount_dyn'];
        $instance['taxonomies_options_hide_terms_0'] = $new_instance['taxonomies_options_hide_terms_0'];
        $instance['taxonomies_options_show_child_terms'] = $new_instance['taxonomies_options_show_child_terms'];
        $instance['taxonomies_options_terms_section_toggle'] = $new_instance['taxonomies_options_terms_section_toggle'];
        $instance['show_reset_button'] = $new_instance['show_reset_button'];
        $instance['show_found_totally'] = $new_instance['show_found_totally'];
        $instance['ajax_items_recount'] = 0;
        $instance['search_result_page'] = $new_instance['search_result_page'];
        $instance['search_result_tpl'] = $new_instance['search_result_tpl'];
        $instance['show_items_count_text'] = $new_instance['show_items_count_text'];
        $instance['reset_link'] = $new_instance['reset_link'];
        $instance['ajax_output'] = 0;
        $instance['woo_search_panel_id'] = $new_instance['woo_search_panel_id'];
        $instance['additional_taxonomies'] = $new_instance['additional_taxonomies'];
        $instance['filter_categories_addit'] = $new_instance['filter_categories_addit'];
        $instance['ajax_results'] = 0;


        return $instance;
    }

//Widget form
    function form($instance) {
        wp_enqueue_script('mdf_popup', MetaDataFilter::get_application_uri() . 'js/pn_popup/pn_advanced_wp_popup.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'));
        wp_enqueue_style('mdf_popup', MetaDataFilter::get_application_uri() . 'js/pn_popup/styles.css');
//Defaults
        $defaults = array(
            'title' => __('MDTF Taxonomies', 'meta-data-filter'),
            MetaDataFilterCore::$slug_cat => MetaDataFilter::WIDGET_TAXONOMIES_ONLY,
            'meta_data_filter_slug' => '',
            'hide_meta_filter_values' => 'true',
            'hide_tax_filter_values' => 'false',
            'show_checkbox_items_count' => 'false',
            'show_select_items_count' => 'false',
            'show_slider_items_count' => 'false',
            'show_items_count_dynam' => 'false',
            'hide_items_where_count_0' => 'false',
            'act_without_button' => 0,
            'and_or' => 'and',
            'title_for_any' => __('Any', 'meta-data-filter'),
            'title_for_filter_button' => __('Filter', 'meta-data-filter'),
            'show_filter_button_after_each_block' => 'false',
            'taxonomies' => array(),
            'taxonomies_options_hide' => array(),
            'taxonomies_options_show_how' => 'select',
            'taxonomies_options_select_size' => 1,
            'taxonomies_options_tax_title' => '',
            'taxonomies_options_checkbox_max_height' => 0,
            'taxonomies_options_behaviour' => 'AND',
            'taxonomies_options_show_count' => 'true',
            'taxonomies_options_post_recount_dyn' => 'true',
            'taxonomies_options_hide_terms_0' => 'false',
            'taxonomies_options_show_child_terms' => 0,
            'taxonomies_options_terms_section_toggle' => 0,
            'show_reset_button' => 'true',
            'show_found_totally' => 'true',
            'ajax_items_recount' => 0,
            'search_result_page' => '',
            'search_result_tpl' => '',
            'reset_link' => '',
            'show_items_count_text' => __('Found &lt;span&gt;%s&lt;/span&gt; items', 'meta-data-filter'),
            'ajax_output' => 0,
            'woo_search_panel_id' => 0,
            'additional_taxonomies' => '',
            'filter_categories_addit' => '',
            'ajax_results' => 0
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        $args = array();
        $args['instance'] = $instance;
        $args['widget'] = $this;
        wp_enqueue_script('meta_data_filter_widget', MetaDataFilterCore::get_application_uri() . 'js/widget_back.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
        echo MetaDataFilterHtml::render_html(MetaDataFilterCore::get_application_path() . 'views/widgets/search_form_tax.php', $args);
    }

}
