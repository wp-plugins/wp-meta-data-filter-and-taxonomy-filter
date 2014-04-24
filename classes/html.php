<?php

class MetaDataFilterHtml extends MetaDataFilterCore {

    public static function init() {
        add_action('wp_ajax_meta_data_filter_add_item_to_data_group', array(__CLASS__, 'add_item_to_data_group'));
        self::$items_types = array(
            'checkbox'=>__("checkbox", 'meta-data-filter'),
            'select'=>__("drop-down", 'meta-data-filter'),
            'slider'=>__("range slider", 'meta-data-filter'),
            'taxonomy'=>__("taxonomy", 'meta-data-filter'),
        );

        self::$tax_items_types = array(
            'select'=>__("drop-down", 'meta-data-filter'),
            'checkbox'=>__("checkbox", 'meta-data-filter')
        );
    }

//ajax
    public static function add_item_to_data_group() {
        wp_die(self::render_html(self::get_application_path() . 'views/add_item_to_data_group.php'));
    }

    //UNDER DEV
    public static function get_item_posts_count2( $page_meta_data_filter, $meta_key, $meta_value, $slug, $html_type ) {
        global $wpdb;
        $sql = "";
        $show_items_count_dynam = 0;
        if(self::is_page_mdf_data()) {
            list($meta_query_array, $filter_post_blocks_data, $widget_options) = self::get_meta_query_array($page_meta_data_filter);
            $show_items_count_dynam = isset($widget_options['show_items_count_dynam']) ? $widget_options['show_items_count_dynam'] : 0;
            if($show_items_count_dynam == 1) {


                //create MYSQL QUERY
                $sql = "SELECT COUNT(*) FROM {$wpdb->posts}";




                //TAXES SET QUERY ROWS
                $mdf_tax_bool = isset($_REQUEST['mdf_tax_bool']) ? $_REQUEST['mdf_tax_bool'] : 'AND';
                $tax_query_array = self::get_tax_query_array($page_meta_data_filter['taxonomy']);
                $taxes_ids_strings_array = array();
                if(!empty($tax_query_array)) {
                    if(!empty($tax_query_array) AND is_array($tax_query_array)) {
                        foreach($tax_query_array as $tax) {
                            if(!is_array($tax['terms'])) {
                                $taxes_ids_strings_array[] = $tax['terms'];
                            }
                            if(is_array($tax['terms'])) {
                                $taxes_ids_strings_array[] = implode(',', $tax['terms']);
                            }
                        }
                    }
                }
                if(!empty($taxes_ids_strings_array)) {
                    foreach($taxes_ids_strings_array as $key=> $ids) {
                        if($key == 0) {
                            $sql.= " INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
                            continue;
                        }
                        $sql.= " INNER JOIN {$wpdb->term_relationships} AS tt{$key} ON ({$wpdb->posts}.ID = tt{$key}.object_id)";
                    }
                }





                //META SET QUERY ROWS
                $in_array = false;
                $pos_in_array = 0; //to remove item if it is in $meta_query_array
                if(!empty($meta_query_array)) {
                    foreach($meta_query_array as $k=> $value) {
                        if($value['key'] == $meta_key) {
                            $in_array = true;
                            $pos_in_array = $k;
                            break;
                        }
                    }
                }
                switch($html_type) {
                    case 'checkbox':
                        if(!$in_array) {
                            $meta_query_array[] = array(
                                'key'=>$meta_key,
                                'value'=>1,
                                'compare'=>'='
                            );
                        }
                        break;
                    case 'select':
                        //we have to exclude all siblings keys in current select
                        if($in_array) {
                            unset($meta_query_array[$pos_in_array]);
                        }
                        //and add itself
                        $meta_query_array[] = array(
                            'key'=>$meta_key,
                            'value'=>$meta_value,
                            'compare'=>'='
                        );

                        break;
                    default:
                        break;
                }


                if(!empty($meta_query_array)) {
                    foreach($meta_query_array as $key=> $vals) {
                        if($key == 0) {
                            $sql.= " INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)";
                            continue;
                        }
                        $sql.= " INNER JOIN {$wpdb->postmeta} AS mt{$key} ON ({$wpdb->posts}.ID = mt{$key}.post_id)";
                    }
                }


                //WHERE
                $sql.= " WHERE 1=1";


                //TAXES SET QUERY VALUES
                if(!empty($taxes_ids_strings_array)) {
                    $sql.= " AND( ";
                    foreach($taxes_ids_strings_array as $key=> $ids) {
                        if($key > 0) {
                            $sql.="  {$mdf_tax_bool}";
                        }
                        //***
                        if($key == 0) {
                            $sql.=" {$wpdb->term_relationships}.term_taxonomy_id IN ({$ids})";
                            continue;
                        }
                        $sql.=" tt{$key}.term_taxonomy_id IN ({$ids})";
                    }
                    $sql.= " )";
                }


                //AND SLUG AND STATUS
                $sql.=" AND {$wpdb->posts}.post_type = '{$slug}' AND ({$wpdb->posts}.post_status = 'publish') ";


                //META SET QUERY VALUES
                $meta_data_filter_bool = isset($_REQUEST['meta_data_filter_bool']) ? $_REQUEST['meta_data_filter_bool'] : 'AND';
                if(!empty($meta_query_array)) {
                    $sql.= " AND( ";
                    foreach($meta_query_array as $key=> $m_value) {
                        if($key > 0) {
                            $sql.="  {$meta_data_filter_bool}";
                        }
                        //***
                        $meta_key = $m_value['key'];
                        $h_type = 'checkbox';
                        if(array_key_exists($meta_key, $filter_post_blocks_data)) {
                            $h_type = $filter_post_blocks_data[$meta_key]['type'];
                        } else {
                            foreach($filter_post_blocks_data as $block) {
                                if(isset($block['reflected_key']) AND $meta_key == $block['reflected_key'] AND $block['is_reflected'] == 1) {
                                    $h_type = $block['type'];
                                    break;
                                }
                            }
                        }

                        switch($h_type) {
                            case 'slider':
                                $val = $m_value['value'];
                                if($key == 0) {
                                    $sql.=" ( ({$wpdb->postmeta}.meta_key = '{$meta_key}' AND CAST({$wpdb->postmeta}.meta_value AS SIGNED) BETWEEN '{$val[0]}' AND '{$val[1]}') )";
                                } else {
                                    $sql.=" ( (mt{$key}.meta_key = '{$meta_key}' AND CAST(mt{$key}.meta_value AS SIGNED) BETWEEN '{$val[0]}' AND '{$val[1]}') )";
                                }
                                break;
                            default:
                                $val = $m_value['value'];
                                if(is_array($val)) {
                                    $val = $m_value['value'][0];
                                }
                                //+++
                                if($key == 0) {
                                    $sql.=" ( ({$wpdb->postmeta}.meta_key = '{$meta_key}' AND CAST({$wpdb->postmeta}.meta_value AS CHAR) = '{$val}') )";
                                } else {
                                    $sql.=" ( (mt{$key}.meta_key = '{$meta_key}' AND CAST(mt{$key}.meta_value AS CHAR) = '{$val}') )";
                                }
                                break;
                        }
                    }
                    $sql.= " )";
                }

                //END
            }
        }


        //if no taxes or just widget not in searching process
        if(!self::is_page_mdf_data() OR ( isset($widget_options['show_items_count_dynam']) AND $show_items_count_dynam == 0)) {
            $sql = "SELECT COUNT(*) FROM {$wpdb->posts}
                    INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)
                    WHERE 1=1
                    AND {$wpdb->posts}.post_type = '{$slug}'
                    AND ({$wpdb->posts}.post_status = 'publish')";


            switch($html_type) {
                case 'slider':
                    $sql.=" AND ( ({$wpdb->postmeta}.meta_key = '{$meta_key}' AND CAST({$wpdb->postmeta}.meta_value AS SIGNED) BETWEEN '{$meta_value[0]}' AND '$meta_value[1]') )";
                    break;
                default:
                    $sql.=" AND ( ({$wpdb->postmeta}.meta_key = '{$meta_key}' AND CAST({$wpdb->postmeta}.meta_value AS CHAR) = '{$meta_value}') )";
                    break;
            }
        }
        //echo $sql;
        return (int) $wpdb->get_var($sql);
    }

    //works perfectly but is slow a little
    public static function get_item_posts_count( $page_meta_data_filter, $meta_key, $meta_value, $slug, $html_type ) {
        $post_count = 0;
        global $wpdb;
        //+++
        if(self::is_page_mdf_data()) {
            list($meta_query_array, $filter_post_blocks_data, $widget_options) = self::get_meta_query_array($page_meta_data_filter);
            $show_items_count_dynam = isset($widget_options['show_items_count_dynam']) ? $widget_options['show_items_count_dynam'] : 0;

            //if count tax items we need to ask next param
            if($html_type == 'tax_item') {
                $show_items_count_dynam = isset($widget_options['taxonomies_options_post_recount_dyn']) ? $widget_options['taxonomies_options_post_recount_dyn'] : 0;
            }

            if($show_items_count_dynam == 1) {
                //process ******************************************************
                $in_array = false;
                $pos_in_array = 0; //to remove item if it is in $meta_query_array
                if(!empty($meta_query_array)) {
                    foreach($meta_query_array as $k=> $value) {
                        if($value['key'] == $meta_key) {
                            $in_array = true;
                            $pos_in_array = $k;
                            break;
                        }
                    }
                }
                //+++
                if($html_type != 'tax_item') {
                    switch($html_type) {
                        case 'checkbox':
                            if(!$in_array) {
                                $meta_query_array[] = array(
                                    'key'=>$meta_key,
                                    'value'=>1,
                                    'compare'=>'='
                                );
                            }
                            break;
                        case 'select':
                            //we have to exclude all siblings keys in current select
                            if($in_array) {
                                unset($meta_query_array[$pos_in_array]);
                            }
                            //and add itself
                            $meta_query_array[] = array(
                                'key'=>$meta_key,
                                'value'=>$meta_value,
                                'compare'=>'='
                            );

                            break;
                        case 'slider':
                            break;
                        default:
                            break;
                    }
                }


                //+++


                $tax_query_array = array();
                //+++
                $mdf_tax_bool = isset($_REQUEST['mdf_tax_bool']) ? $_REQUEST['mdf_tax_bool'] : 'AND';
                if(isset($page_meta_data_filter['taxonomy'])) {
                    $tax_query_array = self::get_tax_query_array($page_meta_data_filter['taxonomy']);
                }
                $tax_query_array = apply_filters('mdf_filter_taxonomies2', $tax_query_array);
                //+++
                //AND empty($tax_query_array) - if only taxonomies in search form
                if(isset($_REQUEST['meta_data_filter_cat'])) {
                    $tmp_add = true;

                    if(empty($meta_query_array)) {
                        $tmp_add = true;
                    }

                    $hide_meta_filter_values = isset($widget_options['hide_meta_filter_values']) ? $widget_options['hide_meta_filter_values'] : false;
                    if($hide_meta_filter_values) {
                        $tmp_add = false;
                    }

                    if($tmp_add) {
                        $meta_query_array[] = array(
                            'key'=>"meta_data_filter_cat",
                            'value'=>$_REQUEST['meta_data_filter_cat'],
                            'compare'=>'='
                        );
                    }
                }


                $meta_data_filter_bool = isset($_REQUEST['meta_data_filter_bool']) ? $_REQUEST['meta_data_filter_bool'] : 'AND';
                if(!empty($meta_query_array)) {
                    $meta_query_array = array_merge(array('relation'=>$meta_data_filter_bool), $meta_query_array);
                }



                if(!empty($tax_query_array)) {
                    $tax_query_array = array_merge(array('relation'=>$mdf_tax_bool), $tax_query_array);
                }


                $args = array(
                    'post_type'=>$slug,
                    'meta_query'=>$meta_query_array,
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
                $post_count = $counter_obj->post_count;
            }
        }

        //****************
        if($html_type != 'tax_item') {

            if(!self::is_page_mdf_data() OR ( isset($widget_options['show_items_count_dynam']) AND $widget_options['show_items_count_dynam'] == 0)) {
                //if we are not on search page or NO show_items_count_dynam
                $meta_query_array = array();
                switch($html_type) {
                    case 'slider':
                        $meta_query_array[] = array(
                            'key'=>$meta_key,
                            'value'=>array($meta_value[0], $meta_value[1]),
                            'type'=>'NUMERIC',
                            'compare'=>'BETWEEN'
                        );

                        break;
                    default:
                        $meta_query_array[] = array(
                            'key'=>"$meta_key",
                            'value'=>"$meta_value",
                            'compare'=>'='
                        );

                        break;
                }


                $tax_query_array = apply_filters('mdf_filter_taxonomies', array());
                $tax_query_array = apply_filters('mdf_filter_taxonomies2', $tax_query_array);



                $args = array(
                    'post_type'=>$slug,
                    'meta_query'=>$meta_query_array,
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
                $post_count = $counter_obj->post_count;
            }
        }

        return $post_count;
    }

    public static function get_meta_query_array( $page_meta_data_filter ) {
        if(isset($_GLOBALS['MDF_META_QUERY_ARRAY'])) {
            return $_GLOBALS['MDF_META_QUERY_ARRAY']; //query array is cached already
        }
        //+++
        $meta_query_array = array();
        if(!empty($page_meta_data_filter)) {
            //here we get all data about meta keys in constructor!!!
            $filter_post_blocks = array();
            if(isset($page_meta_data_filter['filter_post_blocks'])) {
                $filter_post_blocks = $page_meta_data_filter['filter_post_blocks'];
                unset($page_meta_data_filter['filter_post_blocks']);
            }
            $filter_post_blocks_data = array();
            if(!empty($filter_post_blocks) AND is_array($filter_post_blocks)) {
                foreach($filter_post_blocks as $p_id) {
                    $html_items = MetaDataFilterPage::get_html_items($p_id);
                    if(is_array($html_items)) {
                        $filter_post_blocks_data = array_merge($filter_post_blocks_data, $html_items);
                    }
                }
            }
            //+++get widget options
            $widget_options = array();
            if(isset($page_meta_data_filter['mdf_widget_options'])) {
                $widget_options = $page_meta_data_filter['mdf_widget_options'];
                unset($page_meta_data_filter['mdf_widget_options']);
            }
            //+++
            if(isset($page_meta_data_filter['taxonomy'])) {
                unset($page_meta_data_filter['taxonomy']);
            }
            $meta_data_filter_bool = isset($_REQUEST['meta_data_filter_bool']) ? $_REQUEST['meta_data_filter_bool'] : 'AND';
            //+++
            if(is_array($page_meta_data_filter)) {
                foreach($page_meta_data_filter as $key=> $value) {
                    if($value AND ! empty($value)) {
                        $html_item_data = array();
                        if(isset($filter_post_blocks_data[$key])) {
                            $html_item_data = $filter_post_blocks_data[$key];
                        }
                        //+++
                        $search_key = $key;
                        if(isset($html_item_data['is_reflected']) AND $html_item_data['is_reflected'] == 1) {
                            $search_key = $html_item_data['reflected_key'];
                        }
                        //is it slider
                        if(substr_count($value, '^')) {
                            //slider
                            if($meta_data_filter_bool != 'OR') {//IMPORTANT! THIS IS FUSE! WITHOUT this: MODE 'OR' BREAK SQL EXEC
                                list($s, $f) = explode('^', $value);
                                $meta_query_array[] = array(
                                    'key'=>$search_key,
                                    'value'=>array((int) $s, (int) $f),
                                    'type'=>'NUMERIC',
                                    'compare'=>'BETWEEN'
                                );
                            }
                        } else {
                            //checkbox or select
                            if($value != '~') {
                                $meta_query_array[] = array(
                                    'key'=>"$search_key",
                                    'value'=>"$value",
                                    'compare'=>'='
                                );
                            }
                            //~ - mean ANY in select and checkbox
                        }
                    }
                }
            }
        }
        //lets cache it
        $_GLOBALS['MDF_META_QUERY_ARRAY'] = array($meta_query_array, $filter_post_blocks_data, $widget_options);
        return $_GLOBALS['MDF_META_QUERY_ARRAY'];
    }

    public static function get_tax_query_array( $taxonomies ) {
        $use_cache = true;
        if(isset($_GLOBALS['MDF_TAX_USE_CACHE_ARRAY'])) {//uses for get_tax_count in index.php
            $use_cache = $_GLOBALS['MDF_TAX_USE_CACHE_ARRAY']; //uses for get_tax_count in index.php
        }
        if(isset($_GLOBALS['MDF_TAX_QUERY_ARRAY']) AND ! empty($_GLOBALS['MDF_TAX_QUERY_ARRAY']) AND $use_cache) {
            return $_GLOBALS['MDF_TAX_QUERY_ARRAY']; //tax query array is cached already
        }
        //+++
        $tax_query_array = array();
        if(!empty($taxonomies)) {
            if(isset($taxonomies['select']) AND ! empty($taxonomies['select'])) {
                foreach($taxonomies['select'] as $tax_name=> $terms_ids) {
                    if(!empty($terms_ids) AND is_array($terms_ids)) {
                        foreach($terms_ids as $k=> $val) {
                            if($val == -1 OR $val == 0) {
                                unset($terms_ids[$k]);
                            }
                        }
                        //+++
                        if(!empty($terms_ids)) {
                            $tax_query_array[] = array(
                                'taxonomy'=>$tax_name,
                                'field'=>'term_id',
                                'terms'=>end($terms_ids),
                                    //'operator' => 'AND',//ATTENTION
                                    //'include_children' => false
                            );
                        }
                    }
                }
            }
            //+++
            if(isset($taxonomies['checkbox']) AND ! empty($taxonomies['checkbox'])) {
                foreach($taxonomies['checkbox'] as $tax_name=> $terms_ids) {
                    if(!empty($terms_ids) AND is_array($terms_ids)) {
                        /*
                          foreach ($terms_ids as $k => $val) {
                          if ($val == -1 OR $val == 0) {
                          unset($terms_ids[$k]);
                          }
                          }
                         *
                         */
                        //+++
                        //lets check for childs and if we them has exlude parents
                        foreach($terms_ids as $k=> $term_id) {
                            $childs_ids = get_term_children($term_id, $tax_name);
                            if(array_intersect($childs_ids, $terms_ids)) {
                                unset($terms_ids[$k]);
                            }
                        }
                        shuffle($terms_ids); //let keys begin from 0
                        //+++
                        if(!empty($terms_ids)) {
                            $tax_query_array[] = array(
                                'taxonomy'=>$tax_name,
                                'field'=>'term_id',
                                'terms'=>$terms_ids,
                                    //'operator' => 'AND',//ATTENTION
                                    //'include_children' => false
                            );
                        }
                    }
                }
            }
        }
        //lets cache it
        if(!empty($tax_query_array)) {
            $tax_query_array = array($tax_query_array[0]);
        }
        if($use_cache) {
            $_GLOBALS['MDF_TAX_QUERY_ARRAY'] = $tax_query_array;
        }
        return $tax_query_array;
    }

    public static function draw_tax_loader() {
        echo '<img src="' . self::get_application_uri() . 'images/tax_loader.gif" alt="loader" />';
    }

    public static function get_term_label_by_name( $tax_name ) {
        $the_tax = get_taxonomy($tax_name);
        return $the_tax->labels->name;
    }

}
