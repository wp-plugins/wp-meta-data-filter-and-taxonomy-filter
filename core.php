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

    function get_posts() {
        global $wpdb;

        $this->parse_query();

        do_action_ref_array('pre_get_posts', array(&$this));

        // Shorthand.
        $q = &$this->query_vars;

        // Fill again in case pre_get_posts unset some vars.
        $q = $this->fill_query_vars($q);

        // Parse meta query
        $this->meta_query = new WP_Meta_Query();
        $this->meta_query->parse_query_vars($q);

        // Set a flag if a pre_get_posts hook changed the query vars.
        $hash = md5(serialize($this->query_vars));
        if($hash != $this->query_vars_hash) {
            $this->query_vars_changed = true;
            $this->query_vars_hash = $hash;
        }
        unset($hash);

        // First let's clear some variables
        $distinct = '';
        $whichauthor = '';
        $whichmimetype = '';
        $where = '';
        $limits = '';
        $join = '';
        $search = '';
        $groupby = '';
        $fields = '';
        $post_status_join = false;
        $page = 1;



        if(!isset($q['ignore_sticky_posts']))
            $q['ignore_sticky_posts'] = false;

        if(!isset($q['suppress_filters']))
            $q['suppress_filters'] = false;

        $q['cache_results'] = false;

        if(!isset($q['update_post_term_cache']))
            $q['update_post_term_cache'] = true;

        if(!isset($q['update_post_meta_cache']))
            $q['update_post_meta_cache'] = true;


        $post_type = $q['post_type'];
        if(!isset($q['posts_per_page']) || $q['posts_per_page'] == 0)
            $q['posts_per_page'] = get_option('posts_per_page');
        if(isset($q['showposts']) && $q['showposts']) {
            $q['showposts'] = (int) $q['showposts'];
            $q['posts_per_page'] = $q['showposts'];
        }



        $q['nopaging'] = true;




        // If true, forcibly turns off SQL_CALC_FOUND_ROWS even when limits are present.
        if(isset($q['no_found_rows']))
            $q['no_found_rows'] = (bool) $q['no_found_rows'];
        else
            $q['no_found_rows'] = false;

        switch($q['fields']) {
            case 'ids':
                $fields = "$wpdb->posts.ID";
                break;
            case 'id=>parent':
                $fields = "$wpdb->posts.ID, $wpdb->posts.post_parent";
                break;
            default:
                $fields = "$wpdb->posts.*";
        }



        // If we've got a post_type AND it's not "any" post_type.
        if(!empty($q['post_type']) && 'any' != $q['post_type']) {
            foreach((array) $q['post_type'] as $_post_type) {
                $ptype_obj = get_post_type_object($_post_type);
                if(!$ptype_obj || !$ptype_obj->query_var || empty($q[$ptype_obj->query_var]))
                    continue;

                if(!$ptype_obj->hierarchical || strpos($q[$ptype_obj->query_var], '/') === false) {
                    // Non-hierarchical post_types & parent-level-hierarchical post_types can directly use 'name'
                    $q['name'] = $q[$ptype_obj->query_var];
                } else {
                    // Hierarchical post_types will operate through the
                    $q['pagename'] = $q[$ptype_obj->query_var];
                    $q['name'] = '';
                }

                // Only one request for a slug is possible, this is why name & pagename are overwritten above.
                break;
            } //end foreach
            unset($ptype_obj);
        }


        // Taxonomies
        if(!$this->is_singular) {
            $this->parse_tax_query($q);

            $clauses = $this->tax_query->get_sql($wpdb->posts, 'ID');

            $join .= $clauses['join'];
            $where .= $clauses['where'];
        }

        if($this->is_tax) {
            if(empty($post_type)) {
                // Do a fully inclusive search for currently registered post types of queried taxonomies
                $post_type = array();
                $taxonomies = wp_list_pluck($this->tax_query->queries, 'taxonomy');
                foreach(get_post_types(array('exclude_from_search'=>false)) as $pt) {
                    $object_taxonomies = $pt === 'attachment' ? get_taxonomies_for_attachments() : get_object_taxonomies($pt);
                    if(array_intersect($taxonomies, $object_taxonomies))
                        $post_type[] = $pt;
                }
                if(!$post_type)
                    $post_type = 'any';
                elseif(count($post_type) == 1)
                    $post_type = $post_type[0];

                $post_status_join = true;
            } elseif(in_array('attachment', (array) $post_type)) {
                $post_status_join = true;
            }
        }

        // Back-compat
        if(!empty($this->tax_query->queries)) {
            $tax_query_in_and = wp_list_filter($this->tax_query->queries, array('operator'=>'NOT IN'), 'NOT');
            if(!empty($tax_query_in_and)) {
                if(!isset($q['taxonomy'])) {
                    foreach($tax_query_in_and as $a_tax_query) {
                        if(!in_array($a_tax_query['taxonomy'], array('category', 'post_tag'))) {
                            $q['taxonomy'] = $a_tax_query['taxonomy'];
                            if('slug' == $a_tax_query['field'])
                                $q['term'] = $a_tax_query['terms'][0];
                            else
                                $q['term_id'] = $a_tax_query['terms'][0];

                            break;
                        }
                    }
                }

                $cat_query = wp_list_filter($tax_query_in_and, array('taxonomy'=>'category'));
                if(!empty($cat_query)) {
                    $cat_query = reset($cat_query);

                    if(!empty($cat_query['terms'][0])) {
                        $the_cat = get_term_by($cat_query['field'], $cat_query['terms'][0], 'category');
                        if($the_cat) {
                            $this->set('cat', $the_cat->term_id);
                            $this->set('category_name', $the_cat->slug);
                        }
                        unset($the_cat);
                    }
                }
                unset($cat_query);

                $tag_query = wp_list_filter($tax_query_in_and, array('taxonomy'=>'post_tag'));
                if(!empty($tag_query)) {
                    $tag_query = reset($tag_query);

                    if(!empty($tag_query['terms'][0])) {
                        $the_tag = get_term_by($tag_query['field'], $tag_query['terms'][0], 'post_tag');
                        if($the_tag)
                            $this->set('tag_id', $the_tag->term_id);
                        unset($the_tag);
                    }
                }
                unset($tag_query);
            }
        }

        if(!empty($this->tax_query->queries) || !empty($this->meta_query->queries)) {
            $groupby = "{$wpdb->posts}.ID";
        }

        $where .= $search . $whichauthor . $whichmimetype;

        $orderby = "$wpdb->posts.ID";

        if(is_array($post_type) && count($post_type) > 1) {
            $post_type_cap = 'multiple_post_type';
        } else {
            if(is_array($post_type))
                $post_type = reset($post_type);
            $post_type_object = get_post_type_object($post_type);
            if(empty($post_type_object))
                $post_type_cap = $post_type;
        }

        if('any' == $post_type) {
            $in_search_post_types = get_post_types(array('exclude_from_search'=>false));
            if(empty($in_search_post_types))
                $where .= ' AND 1=0 ';
            else
                $where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $in_search_post_types) . "')";
        } elseif(!empty($post_type) && is_array($post_type)) {
            $where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $post_type) . "')";
        } elseif(!empty($post_type)) {
            $where .= " AND $wpdb->posts.post_type = '$post_type'";
            $post_type_object = get_post_type_object($post_type);
        } elseif($this->is_attachment) {
            $where .= " AND $wpdb->posts.post_type = 'attachment'";
            $post_type_object = get_post_type_object('attachment');
        } elseif($this->is_page) {
            $where .= " AND $wpdb->posts.post_type = 'page'";
            $post_type_object = get_post_type_object('page');
        } else {
            $where .= " AND $wpdb->posts.post_type = 'post'";
            $post_type_object = get_post_type_object('post');
        }

        $edit_cap = 'edit_post';
        $read_cap = 'read_post';

        if(!empty($post_type_object)) {
            $edit_others_cap = $post_type_object->cap->edit_others_posts;
            $read_private_cap = $post_type_object->cap->read_private_posts;
        } else {
            $edit_others_cap = 'edit_others_' . $post_type_cap . 's';
            $read_private_cap = 'read_private_' . $post_type_cap . 's';
        }


        if(!empty($this->meta_query->queries)) {
            $clauses = $this->meta_query->get_sql('post', $wpdb->posts, 'ID', $this);
            $join .= $clauses['join'];
            $where .= $clauses['where'];
        }

        $pieces = array('where', 'groupby', 'join', 'orderby', 'distinct', 'fields', 'limits');


        // Announce current selection parameters. For use by caching plugins.
        do_action('posts_selection', $where . $groupby . $orderby . $limits . $join);



        if(!empty($groupby))
            $groupby = 'GROUP BY ' . $groupby;
        if(!empty($orderby))
            $orderby = 'ORDER BY ' . $orderby;

        $found_rows = '';
        if(!$q['no_found_rows'] && !empty($limits))
            $found_rows = 'SQL_CALC_FOUND_ROWS';

        $this->request = $old_request = "SELECT $found_rows $distinct $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits";


        if('ids' == $q['fields']) {
            $this->posts = $wpdb->get_col($this->request);
            $this->post_count = count($this->posts);
            $this->set_found_posts($q, $limits);

            return $this->posts;
        }


        $this->posts = $wpdb->get_results($this->request);



        // Ensure that any posts added/modified via one of the filters above are
        // of the type WP_Post and are filtered.
        if($this->posts) {
            $this->post_count = count($this->posts);
        } else {
            $this->post_count = 0;
            $this->posts = array();
        }

        return $this->posts;
    }

}
