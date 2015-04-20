<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

class MetaDataFilterShortcodes extends MetaDataFilterCore
{

    public static $cached_filter_posts = array(); //for features panel shortcode

    public static function init()
    {
        parent::init();
        $args = array(
            'labels' => array(
                'name' => __('MDTF Shortcodes', 'meta-data-filter'),
                'singular_name' => __('Shortcodes Links', 'meta-data-filter'),
                'add_new' => __('Add New Shortcode', 'meta-data-filter'),
                'add_new_item' => __('Add New Shortcode', 'meta-data-filter'),
                'edit_item' => __('Edit Shortcode', 'meta-data-filter'),
                'new_item' => __('New Shortcode', 'meta-data-filter'),
                'view_item' => __('View Shortcode', 'meta-data-filter'),
                'search_items' => __('Search Shortcodes', 'meta-data-filter'),
                'not_found' => __('No Shortcodes found', 'meta-data-filter'),
                'not_found_in_trash' => __('No Shortcodes found in Trash', 'meta-data-filter'),
                'parent_item_colon' => ''
            ),
            'public' => false,
            'archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => true,
            'menu_position' => null,
            'show_in_menu' => 'edit.php?post_type=' . self::$slug,
            'supports' => array('title', 'excerpt'),
            'rewrite' => array('slug' => self::$slug_shortcodes),
            'show_in_admin_bar' => false,
            'menu_icon' => '',
            'taxonomies' => array()
        );

        register_post_type(self::$slug_shortcodes, $args);

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
        add_shortcode('mdf_results_tax_navigation', array(__CLASS__, 'results_tax_navigation'));
        add_shortcode('mdf_results_by_ajax', array(__CLASS__, 'results_by_ajax'));
        //woocommerce overloaded
        add_shortcode('mdf_products', array(__CLASS__, 'mdf_products'));
        add_shortcode('mdf_custom', array(__CLASS__, 'mdf_custom'));
        add_action('load-post.php', array(__CLASS__, "post_inits"));
        add_action('load-post-new.php', array(__CLASS__, "post_inits"));
        add_action('save_post', array(__CLASS__, 'save_post'), 1);
        add_action("manage_" . self::$slug_shortcodes . "_posts_custom_column", array(__CLASS__, "show_edit_columns_content"));
        add_filter("manage_" . self::$slug_shortcodes . "_posts_columns", array(__CLASS__, "show_edit_columns"));
        add_action('wp_ajax_mdf_draw_shortcode_html_items', array(__CLASS__, 'draw_shortcode_html_items_ajx'));
        //for woocommerce
        add_action('woocommerce_before_shop_loop', array(__CLASS__, 'woocommerce_before_shop_loop'));
        add_action('woocommerce_after_shop_loop', array(__CLASS__, 'woocommerce_after_shop_loop'));
        //***
        add_action('wp_ajax_mdf_search_button_get_content', array(__CLASS__, 'mdf_search_button_get_content'));
        add_action('wp_ajax_nopriv_mdf_search_button_get_content', array(__CLASS__, 'mdf_search_button_get_content'));
    }

    public static function admin_init()
    {
        add_meta_box("mdf_shortcode_options", __("MDTF Shortcode Options", 'meta-data-filter'), array(__CLASS__, 'draw_options_meta_box'), self::$slug_shortcodes, "normal", "high");
        add_meta_box("mdf_shortcode_shortcode", __("Shortcode", 'meta-data-filter'), array(__CLASS__, 'draw_shortcode_meta_box'), self::$slug_shortcodes, "side", "high");
    }

    public static function draw_shortcode_meta_box()
    {
        global $post;
        _e("<b>Search form shortcode</b>", 'meta-data-filter');
        ?>
        <br />
        <i>[mdf_search_form id="<?php echo $post->ID ?>"]</i><br /><br />
        <?php
        _e("<b>Button shortcode</b>", 'meta-data-filter');
        echo '<br /><i>[mdf_search_button id="' . $post->ID . '" title="' . $post->post_title . '" popup_title="' . $post->post_title . '" popup_width=800]</i>';
    }

    public static function show_edit_columns($columns)
    {
        $columns = array(
            "cb" => '<input type="checkbox" />',
            "title" => __("Title", 'meta-data-filter'),
            "shortcode" => __("Shortcode", 'meta-data-filter'),
            "button_shortcode" => __("Button Shortcode", 'meta-data-filter'),
            "date" => __("Date", 'meta-data-filter'),
        );

        return $columns;
    }

    public static function show_edit_columns_content($column)
    {
        global $post;

        switch ($column)
        {
            case "shortcode":
                echo '[mdf_search_form id="' . $post->ID . '"]';
                break;
            case "button_shortcode":
                echo '[mdf_search_button id="' . $post->ID . '" title="' . $post->post_title . '" popup_title="' . $post->post_title . '"]';
                break;
        }
    }

    public static function get_shortcode_options($post_id)
    {
        $shortcode_options = (array) get_post_meta($post_id, 'shortcode_options', true);

        if (!isset($shortcode_options['shortcode_cat']) OR empty($shortcode_options['shortcode_cat']))
        {
            $shortcode_options['shortcode_cat'] = -1;
        }

        if (!isset($shortcode_options['options']['post_type']) OR empty($shortcode_options['options']['post_type']))
        {
            $shortcode_options['options']['post_type'] = 'post';
        }

        return $shortcode_options;
    }

    public static function draw_options_meta_box($post)
    {
        echo self::render_html(self::get_application_path() . 'views/shortcode/options_meta_box.php', array());
    }

    public static function post_inits()
    {
        wp_enqueue_script('mdf_shortcode', self::get_application_uri() . 'js/shortcode.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'));
        wp_enqueue_script('mdf_popup', self::get_application_uri() . 'js/pn_popup/pn_advanced_wp_popup.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'));
        wp_enqueue_style('mdf_popup', self::get_application_uri() . 'js/pn_popup/styles.css');
    }

    public static function mce_buttons($buttons)
    {
        //$buttons[] = 'meta-data-filter';
        return $buttons;
    }

    public static function mce_add_rich_plugins($plugin_array)
    {
        //$plugin_array['mdf_tiny_shortcodes'] = self::get_application_uri() . '/js/shortcode.js';
        return $plugin_array;
    }

//basic shortcode to show filtered results
    public static function do_meta_data_filter_shortcode()
    {
        $data = array();
        echo self::render_html(self::get_application_path() . 'views/shortcode/meta_data_filter.php', $data);
    }

    //[mdf_search_form id="742" slideout=1 location="right" action="click" onloadslideout=1]
    public static function do_mdf_search_form($args)
    {
        ?>
        <p>

            <?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

            <a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" target="_blank" class="button">In the premium version only</a><br />

            <a href="http://cars.wordpress-filter.com/essential-grid/" target="_blank" class="button">Shortcodes gives you ability to create something like this</a>                    
        </p>
        <?php
    }

//ajax
    public static function draw_shortcode_html_items_ajx()
    {
        wp_die(self::draw_shortcode_html_items(array('shortcode_cat' => (int) $_REQUEST['term_id'])));
    }

    //admin
    public static function draw_shortcode_html_items($shortcode_options/* if new */, $html_items = array())
    {
        $data = array();
        if (!isset($shortcode_options['options']))
        {//if new
            $shortcode_options['options'] = array();
        }
        if (!isset($shortcode_options['options']['post_type']))
        {//if new
            $shortcode_options['options']['post_type'] = 'post';
        }
        $data['shortcode_options'] = $shortcode_options;
        $html_items_by_cat = self::get_html_items_by_cat($shortcode_options['shortcode_cat']);

        //lets sort html_items as in shortcode panel
        $data['data'] = self::sort_html_items($html_items, $html_items_by_cat);

        return self::render_html(self::get_application_path() . 'views/shortcode/options_meta_box_html_items.php', $data);
    }

    //lets sort html_items as in shortcode panel
    private static function sort_html_items($html_items, $html_items_by_cat)
    {
        $html_items_by_cat_sorted = array();
        if (!empty($html_items))
        {
            //sorting blocks
            foreach ($html_items as $filter_id => $meta_keys)
            {
                if (key_exists($filter_id, $html_items_by_cat))
                {
                    $html_items_by_cat_sorted[$filter_id] = $html_items_by_cat[$filter_id];
                }
            }
            //sorting blocks items
            foreach ($html_items as $filter_id => $meta_keys)
            {
                if (!empty($meta_keys) AND is_array($meta_keys))
                {
                    $tmp = array();
                    foreach ($meta_keys as $meta_key => $empty_val)
                    {
                        if (isset($html_items_by_cat_sorted[$filter_id]['html_items'][$meta_key]))
                        {
                            $tmp[$meta_key] = $html_items_by_cat_sorted[$filter_id]['html_items'][$meta_key];
                        }
                    }
                    $html_items_by_cat_sorted[$filter_id]['html_items'] = $tmp;
                }
            }
        } else
        {
            $html_items_by_cat_sorted = $html_items_by_cat;
        }

        return $html_items_by_cat_sorted;
    }

    public static function save_post()
    {
        if (!empty($_POST))
        {
            global $post;
            if (is_object($post))
            {
                if ($post->post_type == self::$slug_shortcodes)
                {
                    if (isset($_POST['shortcode_options']))
                    {
                        update_post_meta($post->ID, 'shortcode_options', $_POST['shortcode_options']);
                        update_post_meta($post->ID, 'html_items', $_POST['html_items']); //for sorting in shortcode
                    }
                }
            }
        }
    }

    public static function get_sh_skins()
    {
        $skins = array();
        $src = self::get_application_path() . 'views/shortcode/skins/';
        $dir = opendir($src);
        while (false !== ( $file = readdir($dir)))
        {
            if (( $file != '.' ) AND ( $file != '..' ))
            {
                if (is_dir($src . $file))
                {
                    $skins[] = $file;
                }
            }
        }
        closedir($dir);
        return $skins;
    }

    //**********************************
    public static function do_mdf_search_button($args)
    {
        if (self::isMobile() AND self::get_setting('hide_search_button_shortcode'))
        {
            return "";
        }
        if (isset($args['id']) AND is_numeric($args['id']))
        {
            $rel_post = get_post($args['id']);
            if (!$rel_post)
            {
                return sprintf(__('Shortcode with ID %s doesn exists', 'meta-data-filter'), $args['id']);
            }
            //+++
            $shortcode_options = self::get_shortcode_options($args['id']);
            wp_enqueue_script('mdf_search_button', self::get_application_uri() . 'js/shortcodes/mdf_search_button.js', array('jquery'));
            wp_enqueue_script('mdf_popup', self::get_application_uri() . 'js/pn_popup/pn_advanced_wp_popup.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'));
            wp_enqueue_style('mdf_popup', self::get_application_uri() . 'js/pn_popup/styles.css');
            wp_enqueue_style('mdf_skin_' . $shortcode_options['options']['skin'], self::get_application_uri() . 'views/shortcode/skins/' . $shortcode_options['options']['skin'] . '/styles.css');
            //wp_enqueue_style('mdf_skin_' . $shortcode_options['options']['skin'] . '_slider', self::get_application_uri() . 'views/shortcode/skins/' . $shortcode_options['options']['skin'] . '/slider.css');
            self::front_script_includer();
            return '<a href="#" class="mdf_search_button button" data-popup-width="' . ((isset($args['popup_width']) AND $args['popup_width'] > 0) ? $args['popup_width'] : 800) . '" data-popup-title="' . (!empty($args['popup_title']) ? $args['popup_title'] : 'Wordpress Meta Data & Taxonomies Filter') . '" data-id="' . $args['id'] . '">' . __($args['title']) . '</a>';
        }
    }

    //ajax
    public static function mdf_search_button_get_content()
    {
        echo '<div class="widget widget-meta-data-filter">';
        echo(do_shortcode('[mdf_search_form id="' . (int) $_REQUEST['shortcode_id'] . '"]'));
        echo '</div>';
        exit;
    }

    //shortcode
    public static function force_searching()
    {
        if (self::is_page_mdf_data())
        {
            $tpl = self::get_setting('output_tpl');
            if ($tpl == 'search')
            {
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
    public static function post_features_panel()
    {
        $panel = "";
        global $post;
        $post_id = 0;
        if (is_object($post))
        {
            $post_id = $post->ID;
        }

        if (isset($_REQUEST['current_post_id']))
        {
            $post_id = $_REQUEST['current_post_id'];
        }


        if ($post_id > 0)
        {
            static $title_collector = array();
            if (in_array($post_id, $title_collector))
            {
                return "";
            }

            //+++
            $data = array();
            $data['cat_id'] = get_post_meta($post_id, self::$slug_cat, TRUE);
            $data['post_id'] = $post_id;
            //caching queries data while loop titles
            if (!isset(self::$cached_filter_posts[$data['cat_id']]))
            {
                self::$cached_filter_posts[$data['cat_id']] = MetaDataFilterPage::get_fiters_posts($data['cat_id']);
            }
            $data['filter_posts'] = self::$cached_filter_posts[$data['cat_id']];
            $data['page_meta_data_filter'] = $data['cat_id'] > 0 ? get_post_meta($post_id, 'page_meta_data_filter', true) : array();
            $title_collector[] = $post_id;
            $panel = self::render_html(self::get_application_path() . 'views/shortcode/post_features_panel.php', $data);
        }


        return $panel;
    }

    //shortcode
    public static function results_tax_navigation()
    {
        if (self::is_page_mdf_data() AND isset($_REQUEST['meta_data_filter_args']) AND isset($_REQUEST['meta_data_filter_args']['tax_query']))
        {
            $taxes = $_REQUEST['meta_data_filter_args']['tax_query'];
            unset($taxes['relation']);

            if (!empty($taxes))
            {
                $tmp = array();
                foreach ($taxes as $tax)
                {
                    $tmp[$tax['taxonomy']] = $tax['terms'];
                }
                //+++
                $output_string = ""; // and prepare our output buffer
                foreach ($tmp as $tax_name => $terms)
                {
                    $output = MetaDataFilterHtml::get_term_label_by_name($tax_name) . ": "; // display the name followed by ":"
                    if (is_array($terms))
                    {
                        foreach ($terms as $term_id)
                        { // now loop through all the slugs
                            $term = get_term_by('id', $term_id, $tax_name); // and based on that slug, get the term object
                            $output .= '<a target="_blank" href="' . get_term_link($term, $tax_name) . '">' . $term->name . '</a>&nbsp;&nbsp;'; // and add the term's name to our output buffer followed by a comma (,)
                        }
                    } else
                    {
                        $term = get_term_by('id', $terms, $tax_name); // and based on that slug, get the term object
                        //$output .= $term->name . ', '; // and add the term's name to our output buffer followed by a comma (,)
                        $parents = self::get_taxonomy_parents_all($term->term_id, $tax_name);
                        $buffer = array();
                        foreach ($parents as $term)
                        {
                            $buffer[] = '<a target="_blank" href="' . get_term_link($term, $tax_name) . '">' . $term->name . '</a>&nbsp;&nbsp;';
                        }
                        $buffer = array_reverse($buffer);
                        $buffer = implode(" -> ", $buffer);
                        $output.=$buffer;
                    }
                    $output_string.= rtrim($output, ", ") . " + ";  // when we're finished with terms, remove the last comma
                }

                return rtrim($output_string, " + "); // when we're finished with taxonomies, remove the last '+'
            }
        }
    }

    //+++
    //shortcode
    public static function results_by_ajax($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'shortcode' => 'products',
            'columns' => '4',
            'animate' => 0,
            'animate_target' => '#mdf_results_by_ajax'//body
                        ), $atts));
        /*
          $shortcode_txt = '[';
          if (!empty($atts)) {
          foreach ($atts as $key => $value) {
          if ($key == 'shortcode') {
          $shortcode_txt.=$value . ' ';
          continue;
          }
          //+++
          $shortcode_txt.=$key . '="' . $value . '" ';
          }
          }
          //+++
          $shortcode_txt = trim($shortcode_txt);
          $shortcode_txt.= ']';
          if ($content) {
          $shortcode_txt.= $content . '[' . '/' . $atts['shortcode'] . ']';
          }
         */

        return "<div id='mdf_results_by_ajax' data-animate='{$animate}' data-animate-target='{$animate_target}' data-shortcode='{$shortcode}'>" . do_shortcode("[$shortcode]") . "</div>";
    }

    public static function woocommerce_before_shop_loop()
    {
        //for ajax output
        if (self::get_setting('try_make_shop_page_ajaxed'))
        {
            echo '<div id="mdf_results_by_ajax" data-shortcode="mdf_products">';
        }
    }

    public static function woocommerce_after_shop_loop()
    {
        //for ajax output
        if (self::get_setting('try_make_shop_page_ajaxed'))
        {
            echo '</div>';
        }
    }

    //woo overloaded
    //plugins\woocommerce\includes\class-wc-shortcodes.php#295
    //[mdf_results_by_ajax shortcode="mdf_products columns=2 per_page=6" animate=1 animate_target=body]
    public static function mdf_products($atts)
    {
        ?>
        <p>

            <?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

            <a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" target="_blank" class="button">In the premium version only</a><br />

            <a href="http://cars.wordpress-filter.com/essential-grid/" target="_blank" class="button">Shortcodes gives you ability to create something like this</a>                    
        </p>
        <?php
    }

    //[mdf_results_by_ajax shortcode="mdf_custom template=woocommerce/layout1 post_type=product per_page=6 pagination=tb" animate=1 animate_target=body]
    public static function mdf_custom($atts)
    {
        ?>
        <p>

            <?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

            <a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" target="_blank" class="button">In the premium version only</a><br />

            <a href="http://cars.wordpress-filter.com/essential-grid/" target="_blank" class="button">Shortcodes gives you ability to create something like this</a>                    
        </p>
        <?php
    }

    //for shortcode mdf_products
    public static function woo_post_class($classes)
    {
        global $post;
        $classes[] = 'product';
        $classes[] = 'type-product';
        $classes[] = 'status-publish';
        $classes[] = 'has-post-thumbnail';
        $classes[] = 'post-' . $post->ID;
        return $classes;
    }

    //+++

    private static function get_taxonomy_parents_all($term_id, $taxonomy)
    {
        $parent = get_term_by('id', $term_id, $taxonomy);
        $res = array();
        $res[] = $parent;
        while ($parent->parent != 0)
        {
            $parent = get_term_by('id', $parent->parent, $taxonomy);
            $res[] = $parent;
        }
        return $res;
    }

    //shortcode
    //[mdf_value post_id=5 key='medafi_price' reflection=1]
    public static function mdf_value($atts)
    {
        if (isset($atts['key']))
        {
            global $post;
            $post_id = 0;
            if (is_single())
            {
                $post_id = $post->ID;
            }

            if (isset($atts['post_id']) AND $atts['post_id'] > 0)
            {
                $post_id = $atts['post_id'];
            }

            $look_for_reflection = 0;

            if (isset($atts['reflection']) AND $atts['reflection'] > 0)
            {
                $look_for_reflection = 1;
            }

            if ($post_id > 0)
            {
                return self::get_field($atts['key'], $post_id, $look_for_reflection);
            }
        }

        return "";
    }

}
