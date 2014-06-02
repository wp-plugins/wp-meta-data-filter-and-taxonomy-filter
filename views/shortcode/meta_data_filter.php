<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
if(!isset($_REQUEST['meta_data_filter_works'])) {//anti endless loop
    $_REQUEST['meta_data_filter_works'] = 1; //anti endless loop
    wp_reset_query();
    wp_reset_postdata();
    global $wp_query;

    //+++
    $meta_query_array = array();
    $filter_post_blocks_data = array();
    //+++
    if(self::is_page_mdf_data()) {
        $page_meta_data_filter = self::get_page_mdf_data();
        //+++
        if(!empty($page_meta_data_filter)) {
            $taxonomies = array();
            if(isset($page_meta_data_filter['taxonomy'])) {
                $taxonomies = $page_meta_data_filter['taxonomy'];
                unset($page_meta_data_filter['taxonomy']);
            }
            //+++
            list($meta_query_array, $filter_post_blocks_data, $widget_options) = MetaDataFilterHtml::get_meta_query_array($page_meta_data_filter);
            $meta_data_filter_bool = isset($_GET['meta_data_filter_bool']) ? $_GET['meta_data_filter_bool'] : 'AND';
        }
    } else {
        return;
    }

    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //Taxonomies in process ...
    $tax_query_array = MetaDataFilterHtml::get_tax_query_array($taxonomies);
    $mdf_tax_bool = 'AND';
    if(!empty($tax_query_array)) {
        $mdf_tax_bool = isset($_GET['mdf_tax_bool']) ? $_GET['mdf_tax_bool'] : 'AND';
    }
    $tax_query_array = apply_filters('mdf_filter_taxonomies', $tax_query_array); //for 3 part scripts
    $tax_query_array = apply_filters('mdf_filter_taxonomies2', $tax_query_array); //for 3 part scripts
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    if(isset($_REQUEST['meta_data_filter_cat']) AND $_REQUEST['meta_data_filter_cat'] != -1) {
        $tmp_add = true;

        if(empty($meta_query_array)) {
            $tmp_add = true;
        }

        if($widget_options['hide_meta_filter_values']) {
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

    if(!empty($meta_query_array)) {
        $meta_query_array = array_merge(array('relation'=>$meta_data_filter_bool), $meta_query_array);
    }

    if(!empty($tax_query_array)) {
        $tax_query_array = array($tax_query_array[0]);
        $tax_query_array = array_merge(array('relation'=>$mdf_tax_bool), $tax_query_array);
    }
    //***
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    if(isset($_GET['mdf_page_num'])) {
        $paged = $_GET['mdf_page_num'];
    }


    $args = array(
        'post_type'=>$_GET['slg'],
        'meta_query'=>$meta_query_array,
        'tax_query'=>$tax_query_array,
        'post_status'=>array('publish'),
        'paged'=>$paged
    );


    //print_r($args);
//***
    if(isset($_GET['order_by']) AND $_GET['order_by'] != 'date') {
        $args['meta_key'] = $_GET['order_by'];
        $args['orderby'] = 'meta_value_num';
    } else {
        $args['orderby'] = 'date'; //default ordering
    }

    $args['order'] = 'DESC';
    if(isset($_GET['order'])) {
        $args['order'] = $_GET['order'];
    }
//***    
    $args = apply_filters('meta_data_filter_args', $args);
    if(!isset($_REQUEST['mdf_get_query_args_only'])) {
        $wp_query = new WP_Query($args);
    }
    //echo $wp_query->request;
    //print_r($wp_query);exit;
    //***
    $_REQUEST['meta_data_filter_count'] = $wp_query->found_posts;
    $_REQUEST['meta_data_filter_args'] = $args;
    if(!isset($_REQUEST['mdf_do_not_render_shortcode_tpl'])) {
        $settings = MetaDataFilter::get_settings();
        $output_tpl = "";
        if(empty($settings['output_tpl'])) {
            $output_tpl = 'search';
        } else {
            $output_tpl = $settings['output_tpl'];
        }

        if(isset($widget_options['search_result_tpl']) AND ! empty($widget_options['search_result_tpl'])) {
            $output_tpl = $widget_options['search_result_tpl'];
        }

        $tpl = explode('-', $output_tpl);
        if(!isset($tpl[1])) {
            $tpl[1] = '';
        }
        //means ajax recounting, so data need only for redrawed search form
        ?>
        <script type="text/javascript">
            var mdf_found_totally =<?php echo $wp_query->found_posts ?>;
        </script>
        <?php
        get_template_part($tpl[0], $tpl[1]);
    }
    //***
    wp_reset_postdata();
    //wp_reset_query();    
}

