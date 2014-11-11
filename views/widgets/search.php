<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
//do not show form if such params
if ($instance['hide_meta_filter_values'] == 'true' AND $instance['hide_tax_filter_values'] == 'true') {
    return false;
}

//shows widget only with $_REQUEST['mdf_cat'] ID
//$show_widget = (isset($_GET[MetaDataFilterCore::$slug_cat]) ? ($_GET[MetaDataFilterCore::$slug_cat] == $instance[MetaDataFilterCore::$slug_cat] ? true : false) : true);
$show_widget = true;
//+++
if ($show_widget):
    $uniqid = uniqid();


//+++
    if (isset($instance['search_result_page']) AND ! empty($instance['search_result_page'])) {
        $search_url = $instance['search_result_page'];
    } else {
        $search_url = MetaDataFilterCore::get_search_url_page();
    }

    //output content by ajax
    $ajax_results = (isset($instance['ajax_results']) AND $instance['ajax_results'] == 'true') ? 1 : 0;

    //WPML compatibility
    if (class_exists('SitePress')) {
        $search_url = str_replace(site_url(), site_url() . '/' . ICL_LANGUAGE_CODE, $search_url);
    }
//+++
    if (substr_count($search_url, '?')) {
        $search_url = $search_url . '&';
    } else {
        $search_url = $search_url . '?';
    }
    ?>


    <div class="widget widget-meta-data-filter">
        <?php if (!empty($instance['title'])): ?>
            <h3><?php _e($instance['title']) ?></h3>
        <?php endif; ?>


        <?php
        //+++ using filter categories selector
        $meta_data_filter_cat = $instance['meta_data_filter_cat'];
        $_REQUEST['meta_data_filter_cat_form'] = $meta_data_filter_cat;
        if (isset($_REQUEST['mdf_cat'])) {
            $instance['meta_data_filter_cat'] = $meta_data_filter_cat = $_REQUEST['mdf_cat'];
        }
        $filter_categories = get_terms(MetaDataFilterCore::$slug_cat);
        $filter_categories_saved = (isset($instance['filter_categories_addit']) ? (array) $instance['filter_categories_addit'] : array());
        $current_fc_data = null;
        if (!empty($filter_categories)) {
            //unset selected filter category
            foreach ($filter_categories as $k => $fc) {
                if ($fc->term_id == $meta_data_filter_cat) {
                    $current_fc_data = $filter_categories[$k];
                }

                if (!in_array($fc->term_id, array_keys($filter_categories_saved)) OR $fc->term_id == $meta_data_filter_cat) {
                    unset($filter_categories[$k]);
                }
            }
        }

        if (!empty($filter_categories) AND is_object($current_fc_data)) {
            ?>
            <select class="mdf_filter_categories" data-slug="<?php echo $instance['meta_data_filter_slug'] ?>">
                <option value="<?php echo $current_fc_data->term_id ?>"><?php echo $current_fc_data->name ?></option>
                <?php foreach ($filter_categories as $k => $fc) : ?>
                    <option value="<?php echo $fc->term_id ?>"><?php echo $fc->name ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }
        ?>

        <?php if (isset($_REQUEST['meta_data_filter_count']) AND $instance['show_found_totally'] == 'true'): ?>
            <i class="mdf_widget_found_count">
                <?php
                $show_items_count_text = sprintf(__('Found <span>%s</span> items', 'meta-data-filter'), $_REQUEST['meta_data_filter_count']);
                if (isset($instance['show_items_count_text']) AND ! empty($instance['show_items_count_text'])) {
                    $show_items_count_text = sprintf(__($instance['show_items_count_text']), $_REQUEST['meta_data_filter_count']);
                }

                echo $show_items_count_text;
                ?>
            </i><br />
        <?php endif; ?>


        <form method="get" action="" id="meta_data_filter_<?php echo $uniqid ?>" data-search-url="<?php echo $search_url ?>" data-unique-id="<?php echo $uniqid ?>" data-slug="<?php echo $instance['meta_data_filter_slug'] ?>" data-sidebar-name="<?php echo $sidebar_name ?>" data-sidebar-id="<?php echo $sidebar_id ?>" data-widget-id="<?php echo $widget_id ?>" class="mdf_widget_form <?php if ($instance['ajax_items_recount'] AND ! $instance['act_without_button']): ?>mdf_ajax_auto_recount<?php endif; ?> <?php if ($instance['ajax_items_recount'] AND $instance['act_without_button'] != 'true' AND $ajax_results): ?>mdf_ajax_content_redraw<?php endif; ?>">
            <div class="mdf_one_moment_txt">
                <span><?php MetaDataFilterHtml::draw_tax_loader(); ?></span>
            </div>
            <h6>Wordpress Meta Data and Taxonomies Filter</h6>

            <?php
            //+++
            echo MetaDataFilterPage::draw_front_page_items($instance, $uniqid);
            $meta_data_filter_bool = 'AND';
            if (isset($_GET['meta_data_filter_bool'])) {
                $meta_data_filter_bool = $_GET['meta_data_filter_bool'];
            }
            ?>

            <?php if ($instance['and_or'] == 'BOTH'): ?>
                <input type="radio" name="meta_data_filter_bool" value="OR" <?php if ($meta_data_filter_bool == 'OR'): ?>checked<?php endif; ?> />&nbsp;<?php _e("OR", 'meta-data-filter') ?>
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="meta_data_filter_bool" value="AND" <?php if ($meta_data_filter_bool == 'AND'): ?>checked<?php endif; ?> />&nbsp;<?php _e("AND", 'meta-data-filter') ?>
                <br /><br />
            <?php else: ?>
                <?php $meta_data_filter_bool = $instance['and_or']; ?>
                <input type="hidden" name="meta_data_filter_bool" value="<?php echo $meta_data_filter_bool ?>" />
            <?php endif; ?>


            <?php
            $mdf_tax_bool = 'AND';
            if (isset($_GET['mdf_tax_bool'])) {
                $mdf_tax_bool = $_GET['mdf_tax_bool'];
            } else {
                if (isset($instance['taxonomies_options_behaviour'])) {
                    $mdf_tax_bool = $instance['taxonomies_options_behaviour'];
                }
            }
            ?>


            <?php
            $reset_link = self::get_setting('reset_link');

            if (!empty($instance['reset_link'])) {
                $reset_link = $instance['reset_link'];
            }
            ?>

            <input type="hidden" class="hidden_page_mdf_for_ajax" value="<?php echo(self::is_page_mdf_data() ? self::get_page_mdf_string() : '') ?>" />
            <input type="hidden" name="mdf_tax_bool" value="<?php echo $mdf_tax_bool ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][slug]" value="<?php echo $instance['meta_data_filter_slug'] ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][meta_data_filter_cat]" value="<?php echo $meta_data_filter_cat ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][show_items_count_dynam]" value="<?php echo($instance['show_items_count_dynam'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][taxonomies_options_post_recount_dyn]" value="<?php echo($instance['taxonomies_options_post_recount_dyn'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][taxonomies_options_hide_terms_0]" value="<?php echo($instance['taxonomies_options_hide_terms_0'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][hide_meta_filter_values]" value="<?php echo($instance['hide_meta_filter_values'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][hide_tax_filter_values]" value="<?php echo($instance['hide_tax_filter_values'] == 'true' ? 1 : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][search_result_page]" value="<?php echo((isset($instance['search_result_page']) AND ! empty($instance['search_result_page'])) ? $instance['search_result_page'] : '') ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][search_result_tpl]" value="<?php echo((isset($instance['search_result_tpl']) AND ! empty($instance['search_result_tpl'])) ? $instance['search_result_tpl'] : '') ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][woo_search_panel_id]" value="<?php echo((isset($instance['woo_search_panel_id']) AND ! empty($instance['woo_search_panel_id'])) ? $instance['woo_search_panel_id'] : 0) ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][additional_taxonomies]" value="<?php echo((isset($instance['additional_taxonomies']) AND ! empty($instance['additional_taxonomies'])) ? MetaDataFilter::service_additional_tax_query_string($instance['additional_taxonomies']) : '') ?>" />
            <input type="hidden" name="mdf[mdf_widget_options][reset_link]" value="<?php echo $reset_link ?>" />


            <input type="hidden" value="<?php echo $meta_data_filter_cat ?>" name="<?php echo MetaDataFilterCore::$slug_cat ?>" />



            <div style="clear: both;"></div>

            <?php if (!$instance['show_filter_button_after_each_block'] AND ! $instance['act_without_button'] AND ! $ajax_results): ?>
                <div class="mdf_submit_button_container">
                    <input type="submit" class="button mdf_button" name="" value="<?php _e($instance['title_for_filter_button']); ?>" />
                </div>
            <?php endif; ?>

            <?php if ($instance['show_reset_button'] AND self::is_page_mdf_data()): ?>                
                <div class="mdf_reset_button_container">
                    <?php
                    if ($reset_link == 'self') {
                        if (is_page() OR is_single()) {
                            $reset_link = esc_url(apply_filters('the_permalink', get_permalink()));
                        } else {
                            if (isset($_SERVER['SCRIPT_URI'])) {
                                $reset_link = $_SERVER['SCRIPT_URI'];
                            }
                        }
                    }
                    ?>
                    <input type="button" href="<?php echo(empty($reset_link) ? '#' : $reset_link) ?>" class="button mdf_button mdf_reset_button" value="<?php _e('Reset', 'meta-data-filter') ?>" /><br />
                </div>
            <?php endif; ?>

            <div style="clear: both;"></div>

        </form>
        <br />



        <script type="text/javascript">
            jQuery(function () {
                mdf_init_search_form("<?php echo $uniqid ?>", "<?php echo $instance['meta_data_filter_slug'] ?>", "<?php echo $search_url ?>", <?php echo($instance['act_without_button'] === 'true' ? 1 : 0) ?>, <?php echo($instance['ajax_items_recount'] === 'true' ? 1 : 0) ?>, 0);
            });
        </script>

    </div>
<?php endif; ?>
