<?php
/*
  Plugin Name: WordPress Meta Data & Taxonomies Filter (MDTF)
  Plugin URI: http://meta-data-filter.pluginus.net/
  Description: Powerful and Flexible Filters Tools. Let your clients find all things they are want with the ease on your site
  Author: realmag777
  Version: 1.0.3
  Author URI: http://www.pluginus.net/
 */
require plugin_dir_path(__FILE__) . 'core.php';
require plugin_dir_path(__FILE__) . 'classes/html.php';
require plugin_dir_path(__FILE__) . 'classes/page.php';
require plugin_dir_path(__FILE__) . 'classes/shortcodes.php';

//26-05-2014
class MetaDataFilter extends MetaDataFilterCore {

    const WIDGET_TAXONOMIES_ONLY = -1;

    public static function init() {
        load_plugin_textdomain('meta-data-filter', false, dirname(plugin_basename(__FILE__)) . '/languages');
//***
        $args = array(
            'labels'=>array(
                'name'=>__('MDF Filters', 'meta-data-filter'),
                'singular_name'=>__('Filters sections', 'meta-data-filter'),
                'add_new'=>__('Add New Filter', 'meta-data-filter'),
                'add_new_item'=>__('Add New Filter Section', 'meta-data-filter'),
                'edit_item'=>__('Edit Filter Section', 'meta-data-filter'),
                'new_item'=>__('New Filter Section', 'meta-data-filter'),
                'view_item'=>__('View Filter Section', 'meta-data-filter'),
                'search_items'=>__('Search Filters', 'meta-data-filter'),
                'not_found'=>__('No Filters found', 'meta-data-filter'),
                'not_found_in_trash'=>__('No Filters found in Trash', 'meta-data-filter'),
                'parent_item_colon'=>''
            ),
            'public'=>false,
            'archive'=>false,
            'exclude_from_search'=>true,
            'publicly_queryable'=>true,
            'show_ui'=>true,
            'query_var'=>true,
            'capability_type'=>'post',
            'has_archive'=>false,
            'hierarchical'=>true,
            'menu_position'=>null,
            'supports'=>array('title', 'excerpt', 'tags'),
            'rewrite'=>array('slug'=>self::$slug),
            'show_in_admin_bar'=>false,
            'menu_icon'=>'',
            'taxonomies'=>array(self::$slug_cat) // this is IMPORTANT
        );

        register_taxonomy(self::$slug_cat, array(self::$slug), array(
            "labels"=>array(
                'name'=>__('MDF Categories', 'meta-data-filter'),
                'singular_name'=>__('MDF Categories', 'meta-data-filter'),
                'add_new'=>__('Add New', 'meta-data-filter'),
                'add_new_item'=>__('Add New Category', 'meta-data-filter'),
                'edit_item'=>__('Edit Categories', 'meta-data-filter'),
                'new_item'=>__('New Category', 'meta-data-filter'),
                'view_item'=>__('View Category', 'meta-data-filter'),
                'search_items'=>__('Search Categories', 'meta-data-filter'),
                'not_found'=>__('No Categories found', 'meta-data-filter'),
                'not_found_in_trash'=>__('No Categories found in Trash', 'meta-data-filter'),
                'parent_item_colon'=>''
            ),
            "singular_label"=>__("Category", 'meta-data-filter'),
            'show_in_nav_menus'=>false,
            'capabilities'=>array('manage_terms'),
            'show_ui'=>true,
            'term_group'=>true,
            'hierarchical'=>true,
            'query_var'=>true,
            'rewrite'=>array('slug'=>self::$slug_cat),
            'orderby'=>'name'
        ));


        register_post_type(self::$slug, $args);
//flush_rewrite_rules(true); - do not use, makes woocommerce products 404        
//+++
        wp_enqueue_script('jquery');
        wp_enqueue_script('mdf_lib', MetaDataFilterCore::get_application_uri() . 'js/lib.js', array('jquery'));
//***
        add_action("admin_init", array(__CLASS__, 'admin_init'), 1);
        add_action('admin_menu', array(__CLASS__, 'admin_menu'), 1);
        add_action('save_post', array(__CLASS__, 'save_post'), 1);
        if(!is_admin()) {
//!!! important to call front.js here, another way slider js for shortcodes doesn work
            wp_enqueue_script('meta_data_filter_widget', self::get_application_uri() . 'js/front.js', array('jquery', 'jquery-ui-core', 'jquery-ui-slider'));
        }
        add_action('wp_head', array(__CLASS__, 'wp_head'), 1);
        add_action('admin_head', array(__CLASS__, 'admin_head'), 1);
//***        
        add_action('restrict_manage_posts', array(__CLASS__, "restrict_manage_posts"));
        add_filter('parse_query', array(__CLASS__, "parse_query"));
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
        add_action('wp_ajax_mdf_delete_html_item', array(__CLASS__, 'delete_html_item'));
        add_action('wp_ajax_mdf_add_filter_item_to_widget', array(__CLASS__, 'add_filter_item_to_widget'));
//front ajax
        add_action('wp_ajax_mdf_draw_term_childs', array(__CLASS__, 'draw_term_childs_ajx'));
        add_action('wp_ajax_nopriv_mdf_draw_term_childs', array(__CLASS__, 'draw_term_childs_ajx'));
//+++
        add_filter('mdf_filter_taxonomies', array(__CLASS__, 'filter_mdf_filter_taxonomies'), 1);
        add_filter('mdf_filter_taxonomies2', array(__CLASS__, 'filter_mdf_filter_taxonomies2'), 1);
        $_REQUEST['meta_data_filter_args'] = array();
        add_filter('meta_data_filter_args', array(__CLASS__, 'filter_meta_data_filter_args'), 1);


        MetaDataFilterHtml::init();
        MetaDataFilterPage::init();
        MetaDataFilterShortcodes::init();

//Remove sticky posts
//add_filter('pre_get_posts', array(__CLASS__, 'remove_sticky_posts'), 1);
    }

    public static function filter_meta_data_filter_args( $args ) {
        $args = array_merge($args, $_REQUEST['meta_data_filter_args']);
        return $args;
    }

//action to hook taxonomies array in query for shortcode [meta_data_filter_results]
    public static function filter_mdf_filter_taxonomies( $tax_array ) {
        static $tax_query_array = array();

        if(empty($tax_query_array)) {
            $tax_query_array = $tax_array;
        } else {
            if(!empty($tax_array)) {
                foreach($tax_array as $taxes) {
                    $tax_query_array[] = $taxes;
                }
            }
        }

        return $tax_query_array;
    }

    public static function filter_mdf_filter_taxonomies2( $tax_array ) {
        if(isset($_REQUEST['MDF_ADDITIONAL_TAXONOMIES'])) {
            foreach($_REQUEST['MDF_ADDITIONAL_TAXONOMIES'] as $tax) {
                $tax_array[] = $tax;
            }
        }


        return $tax_array;
    }

    public static function admin_menu() {
        add_submenu_page('edit.php?post_type=' . self::$slug, __("MDF Settings", 'meta-data-filter'), __("MDF Settings", 'meta-data-filter'), 'manage_options', 'mdf_settings', array(__CLASS__, 'draw_settings_page'));
    }

//Remove sticky posts
    public static function remove_sticky_posts( $query ) {
        if(!is_page_template('showcase.php')) {
            $query->set('ignore_sticky_posts', true);
        }
        return $query;
    }

    public static function wp_head() {
        wp_enqueue_script('jquery');
        include MetaDataFilterCore::get_application_path() . 'js/js_vars.php';
    }

    public static function admin_head() {
        wp_enqueue_style('meta_data_filter_admin_total', self::get_application_uri() . 'css/admin_total.css');
        include MetaDataFilterCore::get_application_path() . 'js/js_vars.php';
    }

    public static function widgets_init() {
        register_widget('MetaDataFilter_Search');
        register_widget('MetaDataFilter_PostData');
    }

    public static function admin_init() {
        add_meta_box("meta_data_filter_options", __("Meta Data Filter Options", 'meta-data-filter'), array(__CLASS__, 'data_meta_box'), self::$slug, "normal", "high");
        add_meta_box("meta_data_filter_sequence", __("Meta Data Filter Sequence", 'meta-data-filter'), array(__CLASS__, 'data_meta_box_sequence'), self::$slug, "side", "high");
        //add_meta_box("meta_data_filter_style", __("Meta Data Filter Style", 'meta-data-filter'), array(__CLASS__, 'data_meta_box_style'), self::$slug, "side", "high");
        MetaDataFilterPage::add_meta_box();
//***
        global $pagenow;
        if(is_admin() AND 'plugins.php' == $pagenow AND isset($_GET['activate'])) {
            if($_GET['activate'] == 'true') {
                if(!get_option('meta_data_filter_was_activated')) {

                    $args = array(
                        'ID'=>0,
                        'post_status'=>'publish',
                        'post_title'=>__("MetaData Filter Results Page", 'meta-data-filter'),
                        'post_content'=>'[meta_data_filter_results][/meta_data_filter_results]',
                        'post_type'=>'page'
                    );

                    $post_id = wp_insert_post($args);
                    update_post_meta($post_id, '_wp_page_template', 'template-meta-data-filter.php');

//set settings
                    $data = array(
                        //'search_url'=>get_permalink($post_id),
                        'search_url'=>home_url(),
                        'post_types'=>array('post'),
                        'output_tpl'=>'search',
                        'tooltip_theme'=>'shadow',
                        'tooltip_icon'=>'',
                        'tooltip_max_width'=>220,
                        'post_features_panel_auto'=>1,
                        'under_title_out'=>1,
                        'ajax_searching'=>0,
                        'marketing_short_links'=>0
                    );
                    update_option('meta_data_filter_settings', $data);
//***
                    copy(self::get_application_path() . 'views/to_theme/template-meta-data-filter.php', get_template_directory() . '/template-meta-data-filter.php');
                    update_option('meta_data_filter_was_activated', 1);
                }
            }
        }
//watch for template file existing
        if(!file_exists(get_template_directory() . '/template-meta-data-filter.php')) {
            copy(self::get_application_path() . 'views/to_theme/template-meta-data-filter.php', get_template_directory() . '/template-meta-data-filter.php');
        }
    }

    public static function data_meta_box() {
        global $post;
        wp_enqueue_script('meta_data_filter_admin', self::get_application_uri() . 'js/admin.js', array('jquery'));
        wp_enqueue_style('meta_data_filter_admin', self::get_application_uri() . 'css/admin.css');
        echo self::render_html(self::get_application_path() . 'views/data_meta_box.php', array('html_items'=>self::get_html_items($post->ID)));
    }

    public static function data_meta_box_sequence() {
        global $post;
        ?>
        <input type="text" name="sequence" value="<?php echo (int) get_post_meta($post->ID, 'sequence', true) ?>" />
        <?php
    }

    public static function data_meta_box_style() {
        
    }

    public static function save_post() {
        if(!empty($_POST)) {
            global $post;
            if(is_object($post)) {

                if($post->post_type == self::$slug) {
//saving items of constructor
                    if(isset($_POST['html_item'])) {
                        update_post_meta($post->ID, 'html_items', $_POST['html_item']);
                    }
                    self::set_sequence($post->ID, $_POST['sequence']);
                    update_post_meta($post->ID, 'widget_section_max_height', $_POST['widget_section_max_height']);
                } else {
//saving for posts/pages or custom post types
                    if(isset($_POST[self::$slug_cat])) {
                        global $wpdb;
                        if($_POST[self::$slug_cat] == -1 OR $_POST[self::$slug_cat] != get_post_meta($post->ID, self::$slug_cat, true)) {
//clean old values, because otherwise search is bad
                            $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE 'medafi_%' AND post_id=$post->ID");
                        }
//***
                        update_post_meta($post->ID, 'page_meta_data_filter', $_POST['page_meta_data_filter']); //synhro saving
                        if(!empty($_POST['page_meta_data_filter'])) {
                            foreach($_POST['page_meta_data_filter'] as $key=> $value) {
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

    public static function show_edit_columns_content( $column ) {
        global $post;

        switch($column) {
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

    public static function show_edit_columns( $columns ) {
        $columns = array(
            "cb"=>'<input type="checkbox" />',
            "title"=>__("Title", 'meta-data-filter'),
            "items_count"=>__("Html Items Count", 'meta-data-filter'),
            self::$slug_cat=>__("Categories", 'meta-data-filter'),
            "sequence"=>__("Sequence", 'meta-data-filter'),
        );

        return $columns;
    }

    public static function show_edit_sortable_columns( $columns ) {
        $columns['sequence'] = 'sequence';
        $columns[self::$slug_cat] = self::$slug_cat;
        return $columns;
    }

    public static function show_edit_sortable_columns_act() {
        add_filter('request', array(__CLASS__, 'sequence_col_sort_logic'));
        add_filter('request', array(__CLASS__, 'cat_col_sort_logic'));
    }

    public static function sequence_col_sort_logic( $vars ) {

        if(isset($vars['post_type']) AND self::$slug == $vars['post_type']) {
            if(isset($vars['orderby']) AND 'sequence' == $vars['orderby']) {
                $vars = array_merge($vars, array('meta_key'=>'sequence', 'orderby'=>'meta_value_num'));
            }
        }

        return $vars;
    }

    public static function cat_col_sort_logic( $vars ) {

        if(isset($vars['post_type']) AND self::$slug == $vars['post_type']) {
            if(isset($vars['orderby']) AND self::$slug_cat == $vars['orderby']) {
//$vars = array_merge($vars, array('taxonomy' => self::$slug_cat, 'orderby' => 'title'));
            }
        }

        return $vars;
    }

    public static function restrict_manage_posts() {
        global $typenow;
        global $wp_query;
        if($typenow == self::$slug) {
            $mdf_taxonomy = get_taxonomy(self::$slug_cat);
            wp_dropdown_categories(array(
                'show_option_all'=>__("Show All", 'meta-data-filter') . " " . $mdf_taxonomy->label,
                'taxonomy'=>self::$slug_cat,
                'name'=>self::$slug_cat,
                'orderby'=>'name',
                'selected'=>@$wp_query->query[self::$slug_cat],
                'hierarchical'=>true,
                'depth'=>3,
                'show_count'=>true, // Show # listings in parens
                'hide_empty'=>true,
            ));
        }
    }

    public static function parse_query( $query ) {

        if(!is_admin()) {
//for result output
            if(!isset($_REQUEST['meta_data_filter_works'])) {
                if(self::is_page_mdf_data()) {
                    $mdf_data = self::get_page_mdf_data();
                    $output_tpl = "";
                    if(isset($mdf_data['mdf_widget_options']['search_result_tpl']) AND ! empty($mdf_data['mdf_widget_options']['search_result_tpl'])) {
                        $output_tpl = $mdf_data['mdf_widget_options']['search_result_tpl'];
                    } else {
                        $settings = MetaDataFilter::get_settings();
                        if(empty($settings['output_tpl'])) {
                            $output_tpl = 'search';
                        } else {
                            $output_tpl = $settings['output_tpl'];
                        }
                    }

                    if($output_tpl == 'search') {
                        global $wp_query;
                        $wp_query->set('post_type', $_GET['slg']);
                        if($_GET['slg'] != 'post') {
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
        if(is_admin()) {
            global $pagenow;
            $post_type = self::$slug; // change HERE
            $taxonomy = self::$slug_cat; // change HERE
            $q_vars = &$query->query_vars;
            if($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
                $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
                $q_vars[$taxonomy] = $term->slug;
            }
        }
    }

    public static function pre_get_posts() {
        if(!is_admin()) {
            //for result output
            if(!isset($_REQUEST['meta_data_filter_works'])) {
                if(self::is_page_mdf_data()) {
                    $mdf_data = self::get_page_mdf_data();
                    if(isset($mdf_data['mdf_widget_options']['search_result_tpl']) AND ! empty($mdf_data['mdf_widget_options']['search_result_tpl'])) {
                        $output_tpl = $mdf_data['mdf_widget_options']['search_result_tpl'];
                    } else {
                        $settings = MetaDataFilter::get_settings();
                        if(empty($settings['output_tpl'])) {
                            $output_tpl = 'search';
                        } else {
                            $output_tpl = $settings['output_tpl'];
                        }
                    }

                    if($output_tpl == 'search') {
                        $_REQUEST['mdf_do_not_render_shortcode_tpl'] = true;
                        do_shortcode('[meta_data_filter_results]');
                        add_filter('woo_main_before', array(__CLASS__, 'woo_main_before'), 9999);
                    }
                }
            }
        }
    }

    //woo_main_before();
    public function woo_main_before() {
        echo MetaDataFilterPage::draw_sort_by_filter();
    }

//+++

    public static function set_sequence( $post_id, $sequence ) {
        update_post_meta($post_id, 'sequence', (int) $sequence);
    }

    public static function ajx_set_sequence() {
        self::set_sequence($_REQUEST['post_id'], $_REQUEST['sequence']);
        exit;
    }

    public static function draw_settings_page() {
        if(isset($_POST['meta_data_filter_settings_submit']) AND is_admin()) {
            $service1_before = self::get_setting('marketing_short_links');
            update_option('meta_data_filter_settings', $_POST['meta_data_filter_settings']);
            $service1_after = self::get_setting('marketing_short_links');

//is state of mdf marketing were changed
            if($service1_before != $service1_after) {
                ?>
                <script type="text/javascript">
                    window.location = "<?php echo admin_url("edit.php?post_type=" . self::$slug . "&page=mdf_settings") ?>";
                </script>
                <?php
            }
        }
        $data = self::get_settings();
        echo self::render_html(self::get_application_path() . 'views/settings.php', $data);
    }

//for widget

    public static function add_filter_item_to_widget( $val = '' ) {
        $res = MetaDataFilterPage::get_html_sliders_by_filter_cat($_REQUEST['filter_cat']);
        ?>
        <?php if(!empty($res)): ?>
            <li><select class="widefat" style="width: 90%; margin-bottom: 9px;" name="<?php echo $_REQUEST['field_name'] ?>[]">
                    <?php foreach($res as $key=> $name) : ?>
                        <option value="<?php echo $key ?>" <?php if($key == $val): ?>selected=""<?php endif; ?> class="level-0"><?php _e($name) ?></option>
                    <?php endforeach; ?>
                </select>&nbsp;<a href="#" class="button mdf_delete_filter_item">x</a></li>
        <?php endif; ?>
        <?php
        if(isset($_REQUEST['action']) AND $_REQUEST['action'] == 'mdf_add_filter_item_to_widget') {
            exit;
        }
        return;
    }

//ajax - for option of any taxonomy in the widget
    public static function get_tax_options_in_widget() {
        $terms_list = self::get_terms($_REQUEST['tax_name']);
        if(!empty($terms_list)) {
            $data = array();
            $data['tax_name'] = $_REQUEST['tax_name'];
            $data['show_how'] = $_REQUEST['show_how'];
            $data['select_size'] = isset($_REQUEST['select_size']) ? $_REQUEST['select_size'] : 1;
            $data['hidden_term'] = explode(',', $_REQUEST['hidden_terms']);
            $data['terms_list'] = $terms_list;
            echo self::render_html(self::get_application_path() . 'views/widgets/search_form/popup_terms_list.php', $data);
        } else {
            printf(__('No Taxonomies items created for %s', 'meta-data-filter'), $_REQUEST['tax_name']);
        }
        exit;
    }

    public static function get_terms( $taxonomy, $hide_empty = 0, $get_childs = true, $selected = 0, $category_parent = 0 ) {
        $cats_objects = get_categories(array(
            'orderby'=>'name',
            'order'=>'ASC',
            'style'=>'list',
            'show_count'=>0,
            'hide_empty'=>$hide_empty,
            'use_desc_for_title'=>1,
            'child_of'=>0,
            'hierarchical'=>true,
            'title_li'=>'',
            'show_option_none'=>'',
            'number'=>NULL,
            'echo'=>0,
            'depth'=>0,
            'current_category'=>$selected,
            'pad_counts'=>0,
            'taxonomy'=>$taxonomy,
            'walker'=>'Walker_Category'));

        $cats = array();
        if(!empty($cats_objects)) {
            foreach($cats_objects as $value) {
                if($value->category_parent == $category_parent) {
                    $cats[$value->term_id] = array();
                    $cats[$value->term_id]['term_id'] = $value->term_id;
                    $cats[$value->term_id]['name'] = $value->name;
                    $cats[$value->term_id]['count'] = $value->count;
                    if($get_childs) {
                        $cats[$value->term_id]['childs'] = self::assemble_terms_childs($cats_objects, $value->term_id);
                    }
                }
            }
        }
        return $cats;
    }

//just for get_terms
    private static function assemble_terms_childs( $cats_objects, $parent_id ) {
        $res = array();
        foreach($cats_objects as $value) {
            if($value->category_parent == $parent_id) {
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
            'taxonomies_options_show_count'=>($_REQUEST['is_auto_submit'] == 'true' OR empty($_REQUEST['page_mdf'])) ? 'false' : 'true',
            //if auto submit do not count items count in childs
            'taxonomies_options_post_recount_dyn'=>'false',
            'taxonomies_options_hide_terms_0'=>'true',
            'meta_data_filter_cat'=>$_REQUEST['meta_data_filter_cat'],
            'meta_data_filter_slug'=>$_REQUEST['slug'],
                //'hide_meta_filter_values' => $_REQUEST['slug'],
        );
        $_GLOBALS['MDF_META_DATA_FILTER'] = $_REQUEST['page_mdf'];
        self::draw_term_childs($_REQUEST['type'], $_REQUEST['mdf_parent_id'], 0, $_REQUEST['tax_name'], true, $_REQUEST['hide'], $widget_options);
        exit;
    }

    public static function draw_term_childs( $type = 'select', $mdf_parent_id = 0, $selected_id = 0, $tax_name = 0, $is_ajax = true, $hide_string_ids = '', $widget_options = array() ) {
        $hide = array();
        if(!empty($hide_string_ids)) {
            $hide = explode(',', $hide_string_ids);
        }
//+++
        $terms = self::get_terms($tax_name, true, false, 0, $mdf_parent_id);
        switch($type) {
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

    private static function draw_term_childs_select( $terms, $mdf_parent_id = 0, $selected_id = 0, $hide, $tax_name = 0, $is_ajax = true, $hide_string_ids = '', $widget_options = array() ) {
        if(!empty($terms)) {
            $select_size = isset($widget_options['taxonomies_options_select_size'][$tax_name]) ? (int) $widget_options['taxonomies_options_select_size'][$tax_name] : 1;
            ?>
            <select size="<?php echo $select_size; ?>" name="mdf[taxonomy][select][<?php echo $tax_name ?>][]" class="mdf_taxonomy" data-tax-name="<?php echo $tax_name ?>" data-hide="<?php echo $hide_string_ids ?>">
                <option value="-1">
                    <?php _e(MetaDataFilterHtml::get_term_label_by_name($tax_name)); ?>
                </option>
                <?php foreach($terms as $term_id=> $term) : ?>
                    <?php
                    //do not output hidden terms
                    if(in_array($term_id, $hide)) {
                        continue;
                    }
                    ?>
                    <?php
                    $tax_count = -1;
                    $tax_count_strting = "";
                    $show_option = true;
                    if($widget_options['taxonomies_options_show_count'] == 'true' OR $widget_options['taxonomies_options_show_count'] == 1) {
                        $tax_count = self::get_tax_count($term, $tax_name, $widget_options, 'select');
                        $tax_count_strting = ' (' . $tax_count . ')';
                        if($widget_options['taxonomies_options_post_recount_dyn'] == 'true' OR $widget_options['taxonomies_options_post_recount_dyn'] == 1) {
                            if($widget_options['taxonomies_options_hide_terms_0'] == 'true' OR $widget_options['taxonomies_options_hide_terms_0'] == 1) {
                                if(!$tax_count) {
                                    $show_option = false;
                                }
                            }
                        }
                    }
                    ?>


                    <?php if($show_option): ?>
                        <option <?php if($tax_count == 0 AND ( $widget_options['taxonomies_options_show_count'] == 'true' OR $widget_options['taxonomies_options_show_count'] == 1)): ?>disabled<?php endif; ?> value="<?php echo $term_id ?>" <?php if($selected_id == $term_id): ?>selected<?php endif; ?>>
                            <?php _e($term['name']) ?><?php echo $tax_count_strting; ?>
                        </option>
                    <?php endif; ?>


                <?php endforeach; ?>
            </select>
            <?php if($is_ajax): ?>
                <div class="mdf_taxonomy_child_container"><?php if($tax_name !== 'post_tag') MetaDataFilterHtml::draw_tax_loader(); ?></div>
            <?php endif; ?>
            <?php
        }
        if($is_ajax) {
            exit;
        }
    }

    private static function draw_term_childs_checkbox( $terms, $mdf_parent_id = 0, $selected_ids = array(), $hide, $tax_name = 0, $is_ajax = true, $hide_string_ids = '', $widget_options = array() ) {
        if(!empty($terms)) {
            ?>
            <ul class="mdf_taxonomy_check_list">

                <?php foreach($terms as $term_id=> $term) : ?>
                    <?php
                    //do not output hidden terms
                    if(in_array($term_id, $hide)) {
                        continue;
                    }
                    ?>
                    <li>

                        <?php
                        $tax_count = -1;
                        $tax_count_strting = "";
                        $show_option = true;
                        if($widget_options['taxonomies_options_show_count'] == 'true' OR $widget_options['taxonomies_options_show_count'] == 1) {
                            $tax_count = self::get_tax_count($term, $tax_name, $widget_options, 'checkbox');
                            $tax_count_strting = ' (' . $tax_count . ')';
                            if($widget_options['taxonomies_options_post_recount_dyn'] == 'true' OR $widget_options['taxonomies_options_post_recount_dyn'] == 1) {
                                if($widget_options['taxonomies_options_hide_terms_0'] == 'true' OR $widget_options['taxonomies_options_hide_terms_0'] == 1) {
                                    if(!$tax_count) {
                                        $show_option = false;
                                    }
                                }
                            }
                        }
                        $childs_ids = array();
                        if($show_option) {
                            $childs_ids = get_term_children($term_id, $tax_name);
                        }

                        $unique_id = uniqid();
                        ?>

                        <?php if($show_option): ?>
                            <input <?php if($tax_count == 0 AND ( $widget_options['taxonomies_options_show_count'] == 'true' OR $widget_options['taxonomies_options_show_count'] == 1)): ?>disabled<?php endif; ?> type="checkbox" name="mdf[taxonomy][checkbox][<?php echo $tax_name ?>][]" class="mdf_taxonomy_checkbox <?php if(!empty($childs_ids)): ?>mdf_has_childs<?php endif; ?>" value="<?php echo $term_id ?>" <?php if(in_array($term_id, $selected_ids)): ?>checked<?php endif; ?> data-tax-name="<?php echo $tax_name ?>" data-hide="<?php echo $hide_string_ids ?>" id="tax_check_<?php echo $term_id ?>_<?php echo $unique_id ?>" />&nbsp;<label for="tax_check_<?php echo $term_id ?>_<?php echo $unique_id ?>"><?php _e($term['name']); ?><?php echo $tax_count_strting ?></label>

                            <?php if(array_intersect($childs_ids, $selected_ids) OR ( !empty($childs_ids) AND in_array($term_id, $selected_ids))): ?>
                                <div class="mdf_taxonomy_child_container" style="display: block;">
                                    <?php
                                    $terms = self::get_terms($tax_name, true, false, 0, $term_id);
                                    self::draw_term_childs_checkbox($terms, $term_id, $selected_ids, $hide, $tax_name, $is_ajax, $hide_string_ids, $widget_options);
                                    ?>
                                </div>
                            <?php else: ?>
                                <div class="mdf_taxonomy_child_container">
                                    <?php if($tax_name !== 'post_tag') MetaDataFilterHtml::draw_tax_loader(); ?>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>

                    </li>
                <?php endforeach; ?>
            </ul>

            <?php
        }
        if($is_ajax) {
            exit;
        }
    }

//return count value for taxonomies
    private static function get_tax_count( $term, $tax_name, $widget_options, $html_type ) {
        $count = 0;
        if(!$widget_options['taxonomies_options_show_count']) {
            return 0;
        }

        $slug = $widget_options['meta_data_filter_slug'];

        if($widget_options['taxonomies_options_show_count']) {
            if($widget_options['taxonomies_options_post_recount_dyn'] AND self::is_page_mdf_data()) {
//isset($_REQUEST['page_mdf']) instead of isset($_GET['page_mdf']) for ajax requests
                $_GLOBALS['MDF_TAX_USE_CACHE_ARRAY'] = false;
                $page_meta_data_filter = self::get_page_mdf_data();


                if(isset($page_meta_data_filter['taxonomy'])) {
                    $taxonomies = $page_meta_data_filter['taxonomy'];
                }

                if(!isset($taxonomies[$html_type])) {
                    $taxonomies[$html_type] = array();
                }
                if(!isset($taxonomies[$html_type][$tax_name]) OR ! is_array($taxonomies[$html_type][$tax_name])) {
                    $taxonomies[$html_type][$tax_name] = array();
                }

                if($html_type == 'select') {
                    if(!in_array($term['term_id'], $taxonomies[$html_type][$tax_name])) {
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


                if($html_type == 'checkbox') {
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
                    'taxonomy'=>$tax_name,
                    'field'=>'term_id',
                    'terms'=>array($term['term_id'])
                );
                $tax_query_array = apply_filters('mdf_filter_taxonomies2', $tax_query_array);
//+++
                $args = array(
                    'post_type'=>$slug,
                    'meta_query'=>array(),
                    'tax_query'=>$tax_query_array,
                    'post_status'=>array('publish'),
                    'nopaging'=>true,
                    'fields'=>'ids',
                    'ignore_sticky_posts'=>true,
                    'suppress_filters'=>true,
                    'update_post_term_cache'=>false,
                    'update_post_meta_cache'=>false,
                    'cache_results'=>false,
                    'showposts'=>false,
                    'comments_popup'=>false
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
        $request = array('content'=>'', 'notice'=>'');
//+++
//check for 'medafi_' prefix
        if($new_key == 'medafi_') {
            $request['notice'] = __('Attention! Meta key must be begin from medafi_ but can not such the name!!', 'meta-data-filter');
            wp_die(json_encode($request));
        }

        if(substr($new_key, 0, strlen('medafi_')) != 'medafi_') {
            $request['notice'] = __('Attention! Meta key must be begin from medafi_!!', 'meta-data-filter');
            wp_die(json_encode($request));
        }

//firstly lets check for new key existing in current post of data constructor
        $items = array_keys(self::get_html_items($post_id));
        if(in_array($new_key, $items)) {
            $request['notice'] = __('Such key already exists in current filter or its the same fileld. Try another key!', 'meta-data-filter');
            wp_die(json_encode($request));
        }
//second check the new key in other filters
        $sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='%s'", self::$slug);
        $results = $wpdb->get_results($sql, ARRAY_N);
        $ids = array();
        if(!empty($results)) {
            foreach($results as $value) {
                if($value[0] == $post_id) {
                    continue;
                }
                $ids[] = $value[0];
            }
        }
        if(!empty($ids)) {
            foreach($ids as $p_id) {
                $get_html_items = self::get_html_items($p_id);
                if(is_array($get_html_items)) {
                    $items = array_keys($get_html_items);
                    if(is_array($items) AND in_array($new_key, $items)) {
                        $post_info = get_post($p_id);
                        $term_list = wp_get_post_terms($p_id, self::$slug_cat, array("fields"=>"names"));
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
        if(!empty($items) AND is_array($items)) {
            foreach($items as $key=> $value) {
                if($key == $old_key) {
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
        if(!empty($results) AND is_array($results)) {
            foreach($results as $value) {
                $p_id = $value[0];
                $synhro_data_array = get_post_meta($p_id, 'page_meta_data_filter', true);
                if(!empty($synhro_data_array) AND is_array($synhro_data_array)) {
                    $tmp_new_data = array();
                    foreach($synhro_data_array as $key=> $value) {
                        if($key == $old_key) {
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
        $request['content'] = self::render_html(self::get_application_path() . 'views/add_item_to_data_group.php', array('itemdata'=>@$new_items[$new_key], 'uniqid'=>$new_key));
        wp_die(json_encode($request));
    }

//ajax
    public static function delete_html_item() {
//TODO OR NOT TODO WHAT IS THE QUESTION

        exit;
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
    public static function get_field( $key, $post_id = 0, $look_for_reflection = false ) {
        $res = "";
        if($post_id == 0) {
            $post_id = get_the_ID();
            if(!$post_id) {
                return __('something wrong with post ID', 'meta-data-filter');
            }
        }
//***
        if($look_for_reflection) {
            global $wpdb;
            $filter_post_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '%{$key}%'");
            $html_items = self::get_html_items($filter_post_id);
            if(isset($html_items[$key])) {
                if(isset($html_items[$key]['is_reflected']) AND $html_items[$key]['is_reflected'] == 1) {
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

}

add_action('init', array('MetaDataFilter', 'init'), 1);

//***


class MetaDataFilter_Search extends WP_Widget {

//Widget Setup
    function __construct() {
//Basic settings
        $settings = array('classname'=>__CLASS__, 'description'=>__('META DATA FILTER SEARCH WIDGET', 'meta-data-filter'));

//Creation
        $this->WP_Widget(__CLASS__, __('MDTF by www.pluginus.net', 'meta-data-filter'), $settings);
    }

//Widget view
    function widget( $args, $instance ) {
        $args['instance'] = $instance;
        $args['sidebar_name'] = $args['name'];
        wp_enqueue_style('meta_data_filter_front', MetaDataFilterCore::get_application_uri() . 'css/front.css');

//wp_enqueue_script('meta_data_filter_widget', self::get_application_uri() . 'js/front.js', array('jquery', 'jquery-ui-core', 'jquery-ui-slider'), false, true);
        //beauty range-sliders ion.range-slider
        //http://ionden.com/a/plugins/ion.rangeSlider/
        wp_enqueue_script('ion.range-slider', MetaDataFilter::get_application_uri() . 'js/ion.range-slider/ion.rangeSlider.min.js', array('jquery'));
        wp_enqueue_style('ion.range-slider', MetaDataFilter::get_application_uri() . 'js/ion.range-slider/css/ion.rangeSlider.css');
        wp_enqueue_style('ion.range-slider-skin', MetaDataFilter::get_application_uri() . 'js/ion.range-slider/css/ion.rangeSlider.skinNice.css');
        echo MetaDataFilterHtml::render_html(MetaDataFilterCore::get_application_path() . 'views/widgets/search.php', $args);
    }

//Update widget
    function update( $new_instance, $old_instance ) {
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
        $instance['custom_filter_panel'] = $new_instance['custom_filter_panel'];
        $instance['taxonomies'] = $new_instance['taxonomies'];
        $instance['taxonomies_options_hide'] = $new_instance['taxonomies_options_hide'];
        $instance['taxonomies_options_show_how'] = $new_instance['taxonomies_options_show_how'];
        $instance['taxonomies_options_select_size'] = $new_instance['taxonomies_options_select_size'];
        $instance['taxonomies_options_behaviour'] = $new_instance['taxonomies_options_behaviour'];
        $instance['taxonomies_options_show_count'] = $new_instance['taxonomies_options_show_count'];
        $instance['taxonomies_options_post_recount_dyn'] = $new_instance['taxonomies_options_post_recount_dyn'];
        $instance['taxonomies_options_hide_terms_0'] = $new_instance['taxonomies_options_hide_terms_0'];
        $instance['show_reset_button'] = $new_instance['show_reset_button'];
        $instance['ajax_items_recount'] = $new_instance['ajax_items_recount'];
        $instance['search_result_page'] = $new_instance['search_result_page'];
        $instance['search_result_tpl'] = $new_instance['search_result_tpl'];


        return $instance;
    }

//Widget form
    function form( $instance ) {
        wp_enqueue_script('mdf_popup', MetaDataFilter::get_application_uri() . 'js/pn_popup/pn_advanced_wp_popup.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'));
        wp_enqueue_style('mdf_popup', MetaDataFilter::get_application_uri() . 'js/pn_popup/styles.css');
//Defaults
        $defaults = array(
            'title'=>__('Meta Data Filter', 'meta-data-filter'),
            MetaDataFilterCore::$slug_cat=>0,
            'meta_data_filter_slug'=>'post',
            'hide_meta_filter_values'=>'false',
            'hide_tax_filter_values'=>'false',
            'show_checkbox_items_count'=>'true',
            'show_select_items_count'=>'true',
            'show_slider_items_count'=>'true',
            'show_items_count_dynam'=>'true',
            'hide_items_where_count_0'=>'false',
            'act_without_button'=>'false',
            'and_or'=>'and',
            'title_for_any'=>__('Any', 'meta-data-filter'),
            'title_for_filter_button'=>__('Filter', 'meta-data-filter'),
            'show_filter_button_after_each_block'=>'false',
            'custom_filter_panel'=>array(),
            'taxonomies'=>array(),
            'taxonomies_options_hide'=>array(),
            'taxonomies_options_show_how'=>'select',
            'taxonomies_options_select_size'=>1,
            'taxonomies_options_behaviour'=>'AND',
            'taxonomies_options_show_count'=>'true',
            'taxonomies_options_post_recount_dyn'=>'true',
            'taxonomies_options_hide_terms_0'=>'false',
            'show_reset_button'=>'false',
            'ajax_items_recount'=>'false',
            'search_result_page'=>'',
            'search_result_tpl'=>''
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        $args = array();
        $args['instance'] = $instance;
        $args['widget'] = $this;
        wp_enqueue_script('meta_data_filter_widget', MetaDataFilterCore::get_application_uri() . 'js/widget_back.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
        echo MetaDataFilterHtml::render_html(MetaDataFilterCore::get_application_path() . 'views/widgets/search_form.php', $args);
    }

}

//for single post,page or single custom
class MetaDataFilter_PostData extends WP_Widget {

//Widget Setup
    function __construct() {
//Basic settings
        $settings = array('classname'=>__CLASS__, 'description'=>__('META DATA FILTER SINGLE POST WIDGET', 'meta-data-filter'));

//Creation
        $this->WP_Widget(__CLASS__, __('MDTF single post', 'meta-data-filter'), $settings);
    }

//Widget view
    function widget( $args, $instance ) {
        $args['instance'] = $instance;
        wp_enqueue_script('jquery');

        echo MetaDataFilterHtml::render_html(MetaDataFilterCore::get_application_path() . 'views/widgets/post_data.php', $args);
    }

//Update widget
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance[MetaDataFilterCore::$slug_cat] = $new_instance[MetaDataFilterCore::$slug_cat];
        $instance['meta_data_filter_slug'] = $new_instance['meta_data_filter_slug'];
        $instance['show_absent_items'] = $new_instance['show_absent_items'];

        return $instance;
    }

//Widget form
    function form( $instance ) {
//Defaults
        $defaults = array(
            'title'=>__('Single Post Meta Data', 'meta-data-filter'),
            'show_absent_items'=>'false',
            'meta_data_filter_slug'=>'post'
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        $args = array();
        $args['instance'] = $instance;
        $args['widget'] = $this;
        wp_enqueue_script('meta_data_filter_widget', MetaDataFilterCore::get_application_uri() . 'js/widget_back.js', array('jquery'));
        echo MetaDataFilterHtml::render_html(MetaDataFilterCore::get_application_path() . 'views/widgets/post_data_form.php', $args);
    }

}
