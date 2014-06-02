<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
//do not show form if such params
if($instance['hide_meta_filter_values'] == 'true' AND $instance['hide_tax_filter_values'] == 'true') {
    return false;
}

//shows widget only with $_REQUEST['meta_data_filter_cat'] ID
$show_widget = (isset($_GET[MetaDataFilterCore::$slug_cat]) ? ($_GET[MetaDataFilterCore::$slug_cat] == $instance[MetaDataFilterCore::$slug_cat] ? true : false) : true);
//+++
if($show_widget):
    $uniqid = uniqid();


//+++
    if(isset($instance['search_result_page']) AND ! empty($instance['search_result_page'])) {
        $search_url = $instance['search_result_page'];
    } else {
        $search_url = MetaDataFilterCore::get_search_url_page();
    }
//+++
    if(substr_count($search_url, '?')) {
        $search_url = $search_url . '&';
    } else {
        $search_url = $search_url . '?';
    }
    
    ?>


    <div class="widget widget-meta-data-filter">
        <?php if(!empty($instance['title'])): ?>
            <h3><?php _e($instance['title']) ?></h3>
        <?php endif; ?>

        <?php if(isset($_REQUEST['meta_data_filter_count'])): ?>
            <i class="mdf_widget_found_count"><?php echo sprintf(__('Found %s items', 'meta-data-filter'), $_REQUEST['meta_data_filter_count']) ?></i><br />
        <?php endif; ?>

        <form method="get" action="" id="meta_data_filter_<?php echo $uniqid ?>" data-search-url="<?php echo $search_url ?>" data-unique-id="<?php echo $uniqid ?>" data-slug="<?php echo $instance['meta_data_filter_slug'] ?>" data-sidebar-name="<?php echo $sidebar_name ?>" data-widget-id="<?php echo $widget_id ?>" class="mdf_widget_form">
            <div class="mdf_one_moment_txt">
                <span><?php MetaDataFilterHtml::draw_tax_loader(); ?></span>
            </div>
            <h6>Wordpress Meta Data and Taxonomies Filter</h6>
            <?php 
            echo MetaDataFilterPage::draw_front_page_items($instance, $uniqid);
            $meta_data_filter_bool = 'AND';
            if(isset($_GET['meta_data_filter_bool'])) {
                $meta_data_filter_bool = $_GET['meta_data_filter_bool'];
            }
            ?>

            <?php if($instance['and_or'] == 'BOTH'): ?>
                <input type="radio" name="meta_data_filter_bool" value="OR" <?php if($meta_data_filter_bool == 'OR'): ?>checked<?php endif; ?> />&nbsp;<?php _e("OR", 'meta-data-filter') ?>
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="meta_data_filter_bool" value="AND" <?php if($meta_data_filter_bool == 'AND'): ?>checked<?php endif; ?> />&nbsp;<?php _e("AND", 'meta-data-filter') ?>
                <br /><br />
            <?php else: ?>
                <?php $meta_data_filter_bool = $instance['and_or']; ?>
                <input type="hidden" name="meta_data_filter_bool" value="<?php echo $meta_data_filter_bool ?>" />
            <?php endif; ?>


            <?php
            $mdf_tax_bool = 'AND';
            if(isset($_GET['mdf_tax_bool'])) {
                $mdf_tax_bool = $_GET['mdf_tax_bool'];
            } else {
                if(isset($instance['taxonomies_options_behaviour'])) {
                    $mdf_tax_bool = $instance['taxonomies_options_behaviour'];
                }
            }
            ?>

            <input type="hidden" class="hidden_page_mdf_for_ajax" value="<?php echo(self::is_page_mdf_data() ? self::get_page_mdf_string() : '') ?>" />
            <input type="hidden" name="mdf_tax_bool" value="<?php echo $mdf_tax_bool ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][slug]" value="<?php echo $instance['meta_data_filter_slug'] ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][meta_data_filter_cat]" value="<?php echo $instance['meta_data_filter_cat'] ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][show_items_count_dynam]" value="<?php echo($instance['show_items_count_dynam'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][taxonomies_options_post_recount_dyn]" value="<?php echo($instance['taxonomies_options_post_recount_dyn'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][taxonomies_options_hide_terms_0]" value="<?php echo($instance['taxonomies_options_hide_terms_0'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][hide_meta_filter_values]" value="<?php echo($instance['hide_meta_filter_values'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][hide_tax_filter_values]" value="<?php echo($instance['hide_tax_filter_values'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][search_result_page]" value="<?php echo((isset($instance['search_result_page']) AND ! empty($instance['search_result_page'])) ? $instance['search_result_page'] : '') ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][search_result_tpl]" value="<?php echo((isset($instance['search_result_tpl']) AND ! empty($instance['search_result_tpl'])) ? $instance['search_result_tpl'] : '') ?>" />


            <?php
            if(!empty($instance['custom_filter_panel'])):
                if(is_array($instance['custom_filter_panel'])):
                    ?>
                    <input type="hidden" value="<?php echo implode(',', $instance['custom_filter_panel']); ?>" name="custom_filter_panel" />
                    <?php
                endif;
            endif;
            ?>
            <input type="hidden" value="<?php echo $instance[MetaDataFilterCore::$slug_cat] ?>" name="<?php echo MetaDataFilterCore::$slug_cat ?>" />

            <?php if(!$instance['show_filter_button_after_each_block']): ?>
                <div style="float: left;">
                    <input type="submit" class="button mdf_button" name="" value="<?php _e($instance['title_for_filter_button']); ?>" />
                </div>
            <?php endif; ?>

            <?php if($instance['show_reset_button'] AND self::is_page_mdf_data()): ?>
                <div style="float: right;">
                    <input type="button" class="button mdf_button mdf_reset_button" name="" value="<?php _e('Reset', 'meta-data-filter') ?>" />
                </div>
            <?php endif; ?>

            <div style="clear: both;"></div>

        </form>
        <br />

        <script type="text/javascript">
            jQuery(function() {
                mdf_init_search_form("<?php echo $uniqid ?>", "<?php echo $instance['meta_data_filter_slug'] ?>", "<?php echo $search_url ?>", <?php echo($instance['act_without_button'] === 'true' ? 1 : 0) ?>, 0);
            });
        </script>

    </div>
<?php endif; ?>
