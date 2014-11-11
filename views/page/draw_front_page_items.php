<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php if (!empty($filter_block['items'])): ?>

    <input type="hidden" name="mdf[filter_post_blocks][]" value="<?php echo $filter_post_id ?>" />
    <?php
    $section_toggle = (int) get_post_meta($filter_post_id, 'widget_section_toggle', true);
    if (isset($page_meta_data_filter['filter_post_blocks_toggles'][$counter_block])) {
        $section_toggle = $page_meta_data_filter['filter_post_blocks_toggles'][$counter_block];
    }
    ?>
    <input type="hidden" class="mdf_filter_post_blocks_toggles" name="mdf[filter_post_blocks_toggles][]" value="<?php echo $section_toggle ?>" />
    <?php if ($filter_block['name']{0} !== '~'): ?>
        <h4 class="data-filter-section-title"><?php _e($filter_block['name']) ?><?php MetaDataFilter::draw_front_toggle($section_toggle); ?></h4>
    <?php endif; ?>

    <?php
    $counter = 0;
    $icon = MetaDataFilter::get_application_uri() . 'images/tooltip-info.png';
    $settings = MetaDataFilter::get_settings();
    if (!empty($settings['tooltip_icon'])) {
        $icon = $settings['tooltip_icon'];
    }
    $section_height = (int) get_post_meta($filter_post_id, 'widget_section_max_height', true);



    //*** additional taxonomies for Pre-sale question:
    //I have woocommerce with brands plugin installed, each brand page shows the products of this brand,
    //can this plugin filter those products based on category?
    //$_REQUEST['MDF_ADDITIONAL_TAXONOMIES'] = array();
    if (isset($widget_options['additional_taxonomies']) AND ! empty($widget_options['additional_taxonomies'])) {
        MetaDataFilter::add_additional_taxonomies($widget_options['additional_taxonomies']);
    }
    //***
    ?>
    <div class="mdf_filter_section mdf_filter_section_<?php echo $counter_block ?> <?php if ($section_height > 0): ?>mdf_filter_section_scrolled<?php endif; ?> <?php if ($section_toggle == 2): ?>mdf_front_toggle_closed_section<?php endif; ?>" style="<?php if ($section_height): ?>max-height:<?php echo $section_height; ?>px;<?php endif; ?>">
        <table style="width: 98%;">
            <?php foreach ($filter_block['items'] as $key => $item) : $uid = uniqid(); ?>

                <?php
                $search_key = $key;
                if (isset($item['is_reflected']) AND $item['is_reflected'] == 1) {
                    $search_key = $item['reflected_key'];
                }

                //+++
                if ($widget_options['hide_meta_filter_values'] == 'true') {
                    if ($item['type'] != 'taxonomy') {
                        continue;
                    }
                }

                if ($widget_options['hide_tax_filter_values'] == 'true') {
                    if ($item['type'] == 'taxonomy') {
                        continue;
                    }
                }
                ?>
                <tr>

                    <?php if ($item['type'] == 'taxonomy'): ?>
                        <?php if (!empty($widget_options['taxonomies']) AND is_array($widget_options['taxonomies'])): ?>
                            <td>
                                <?php
                                $counter = 0;
                                foreach ($widget_options['taxonomies'] as $tax_name => $v) :
                                    if ($counter > 0) {
                                        break;
                                    }
                                    $counter++;
                                    ?>
                                    <div class="mdf_input_container <?php if ($widget_options['act_without_button']): ?>mdf_tax_auto_submit<?php endif; ?> <?php if ($widget_options['ajax_items_recount']): ?>mdf_tax_ajax_autorecount<?php endif; ?> mdf_section_tax mdf_section_tax_<?php echo $tax_name ?>">
                                        <?php $show_how = $widget_options['taxonomies_options_show_how'][$tax_name]; ?>

                                        <?php if (@$item['name']{0} !== '~' AND $show_how != 'checkbox' AND FALSE): ?>
                                            <h5 class="data-filter-section-title"><?php _e($item['name']) ?>:</h5>
                                        <?php endif; ?>


                                        <input type="hidden" name="mdf[taxonomy][<?php echo $show_how ?>][<?php echo $tax_name ?>]" value="" />
                                        <?php
                                        $hide = '';
                                        if (isset($widget_options['taxonomies_options_hide'][$tax_name])) {
                                            $hide = $widget_options['taxonomies_options_hide'][$tax_name];
                                        }
                                        $section_tax_toggle = 0;
                                        if (isset($widget_options['taxonomies_options_terms_section_toggle'][$tax_name])) {
                                            $section_tax_toggle = (int) $widget_options['taxonomies_options_terms_section_toggle'][$tax_name];
                                        }
                                        if (isset($page_meta_data_filter['filter_post_blocks_toggles_tax'][$tax_name])) {
                                            $section_tax_toggle = $page_meta_data_filter['filter_post_blocks_toggles_tax'][$tax_name];
                                        }
                                        //***
                                        if (self::is_page_mdf_data()) {
                                            $taxonomies = array();
                                            if (isset($page_meta_data_filter['taxonomy'])) {
                                                $taxonomies = $page_meta_data_filter['taxonomy'];
                                                //unset($page_meta_data_filter['taxonomy']);
                                            }
                                            //+++
                                            $terms_ids = array();
                                            if (isset($taxonomies[$show_how])) {
                                                if (array_key_exists($tax_name, $taxonomies[$show_how])) {
                                                    $terms_ids = $taxonomies[$show_how][$tax_name];
                                                }
                                            }
                                            //check situation when data transfered by shortcode and data is in another html item
                                            if (empty($terms_ids)) {
                                                $tmp = $taxonomies;
                                                unset($tmp[$show_how]);
                                                if (!empty($tmp) AND is_array($tmp)) {
                                                    foreach ($tmp as $s_h => $txs) {
                                                        if (array_key_exists($tax_name, $txs)) {
                                                            $terms_ids = $tmp[$s_h][$tax_name];
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            //print_r($terms_ids);
                                            //+++
                                            //draw selects
                                            if ($show_how == 'select') {

                                                if (!empty($terms_ids) AND is_array($terms_ids)) {
                                                    foreach ($terms_ids as $k => $term_id) {
                                                        if ($term_id == -1 AND $term_id == 0) {
                                                            unset($terms_ids[$k]);
                                                        }
                                                    }
                                                }
                                                ?>


                                                <?php
                                                //+++
                                                $cc = 0;
                                                if (!empty($terms_ids) AND is_array($terms_ids)) {
                                                    foreach ($terms_ids as $term_id) {
                                                        $parent_id = 0;
                                                        if ($cc > 0) {
                                                            $parent_id = $terms_ids[$cc - 1];
                                                        }

                                                        MetaDataFilter::draw_term_childs('select', $parent_id, $term_id, $tax_name, false, $hide, $widget_options);
                                                        echo '<div class="mdf_taxonomy_child_container" style="display:block;">';
                                                        $cc++;
                                                    }
                                                } else {
                                                    MetaDataFilter::draw_term_childs('select', 0, 0, $tax_name, false, $hide, $widget_options);
                                                    echo '<div class="mdf_taxonomy_child_container" style="display:block;"></div>';
                                                }
                                                //+++

                                                for ($i = 0; $i < $cc; $i++) {
                                                    echo '</div>';
                                                }
                                            }
                                            //+++
                                            //*** draw checkboxes
                                            if ($show_how == 'checkbox') {
                                                $section_max_height = isset($widget_options['taxonomies_options_checkbox_max_height'][$tax_name]) ? $widget_options['taxonomies_options_checkbox_max_height'][$tax_name] : 0;
                                                ?>
                                                <input type="hidden" class="mdf_filter_post_blocks_toggles" name="mdf[filter_post_blocks_toggles_tax][<?php echo $tax_name ?>]" value="<?php echo $section_tax_toggle ?>" />

                                                <h4 class="data-filter-section-title"><?php
                                                    $tax_title = "";
                                                    if (isset($widget_options['taxonomies_options_tax_title'][$tax_name]) AND ! empty($widget_options['taxonomies_options_tax_title'][$tax_name])) {
                                                        $tax_title = $widget_options['taxonomies_options_tax_title'][$tax_name];
                                                    } else {
                                                        $tax_title = __(MetaDataFilterHtml::get_term_label_by_name($tax_name));
                                                    }
                                                    echo $tax_title;
                                                    ?><?php MetaDataFilter::draw_front_toggle($section_tax_toggle); ?></h4>
                                                <div class="mdf_tax_filter_section mdf_tax_filter_section_<?php echo $tax_name ?> <?php if ($section_max_height): ?>mdf_filter_section_scrolled<?php endif; ?> <?php if ($section_tax_toggle == 2): ?>mdf_front_toggle_closed_section<?php endif; ?>" <?php if ($section_max_height): ?>style="max-height:<?php echo $section_max_height; ?>px;"<?php endif; ?>>
                                                    <?php
                                                    $parent_id = 0;
                                                    MetaDataFilter::draw_term_childs('checkbox', $parent_id, $terms_ids, $tax_name, false, $hide, $widget_options);
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                        } else {
                                            if ($show_how == 'checkbox') {
                                                $section_max_height = isset($widget_options['taxonomies_options_checkbox_max_height'][$tax_name]) ? $widget_options['taxonomies_options_checkbox_max_height'][$tax_name] : 0;
                                                ?>
                                                <input type="hidden" class="mdf_filter_post_blocks_toggles" name="mdf[filter_post_blocks_toggles_tax][<?php echo $tax_name ?>]" value="<?php echo $section_tax_toggle ?>" />

                                                <h4 class="data-filter-section-title"><?php
                                                    $tax_title = "";
                                                    if (isset($widget_options['taxonomies_options_tax_title'][$tax_name]) AND ! empty($widget_options['taxonomies_options_tax_title'][$tax_name])) {
                                                        $tax_title = $widget_options['taxonomies_options_tax_title'][$tax_name];
                                                    } else {
                                                        $tax_title = __(MetaDataFilterHtml::get_term_label_by_name($tax_name));
                                                    }
                                                    echo $tax_title;
                                                    ?><?php MetaDataFilter::draw_front_toggle($section_tax_toggle); ?></h4>
                                                <div class="mdf_tax_filter_section mdf_tax_filter_section_<?php echo $tax_name ?> <?php if ($section_max_height): ?>mdf_filter_section_scrolled<?php endif; ?> <?php if ($section_tax_toggle == 2): ?>mdf_front_toggle_closed_section<?php endif; ?>" <?php if ($section_max_height): ?>style="max-height:<?php echo $section_max_height; ?>px;"<?php endif; ?>>
                                                    <?php
                                                }
                                                MetaDataFilter::draw_term_childs($show_how, 0, 0, $tax_name, false, $hide, $widget_options);
                                                ?>
                                                <div class="mdf_taxonomy_child_container"><?php MetaDataFilterHtml::draw_tax_loader(); ?></div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($item['type'] == 'slider'): ?>
                        <?php
                        wp_enqueue_style('mdf_front_slider', self::get_application_uri() . 'css/front_slider.css');
                        $min = $max = 0;
                        if (isset($page_meta_data_filter[$key])) {
                            list($min, $max) = explode('^', $page_meta_data_filter[$key]);
                        }
                        list($preset_min, $preset_max) = explode('^', $item['slider']);

                        $slider_step = isset($item['slider_step']) ? $item['slider_step'] : 0;
                        $slider_prefix = isset($item['slider_prefix']) ? $item['slider_prefix'] : '';
                        $slider_postfix = isset($item['slider_postfix']) ? $item['slider_postfix'] : '';
                        $slider_prettify = isset($item['slider_prettify']) ? $item['slider_prettify'] : 1;

                        if ($min == $max AND $max == 0) {
                            $min = $preset_min;
                            $max = $preset_max;
                        }
                        if (empty($page_meta_data_filter[$key])) {
                            $page_meta_data_filter[$key] = (int) $min . '^' . $max;
                        }
                        //+++

                        if ($widget_options['show_slider_items_count']) {
                            $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, array($min, $max), $slug, 'slider');
                            if (!$items_count) {
                                if ($widget_options['hide_items_where_count_0']) {
                                    //continue; //IF NO ONE ITEM WHY NOT TO HIDE THIS?!
                                }
                            }
                        }

                        //+++
                        ?>
                        <td>
                            <?php
                            if ($search_key === '_price' AND ( isset($item['woo_price_auto']) AND $item['woo_price_auto'] == 1)) {
                                $mm = MDTF_HELPER::get_woo_min_max_price();
                                if (!empty($mm)) {
                                    $preset_max = $mm['max'];
                                    $preset_min = $mm['min'];
                                    if (!self::is_page_mdf_data()) {
                                        $max = $mm['max'];
                                        $min = $mm['min'];
                                    }
                                }
                            }
                            ?>
                            <div class="mdf_input_container mdf_slider_<?php echo $key ?>">
                                <h5 class="data-filter-section-title mdf_range_slider_header" style="margin-bottom: 4px;" for="<?php echo $uid ?>"><span class="mdf_range_slider_header_txt"><?php _e($item['name']) ?>:&nbsp;</span><span class="mdf_range"><input type="text" value="<?php echo $min ?>" class="mdf_range_min" data-slider-id="ui_slider_item_<?php echo $uid ?>" /> - <input type="text" value="<?php echo $max ?>" class="mdf_range_max" data-slider-id="ui_slider_item_<?php echo $uid ?>" /></span> &nbsp;<?php if ($widget_options['show_slider_items_count']) echo '(' . $items_count . ')'; ?>&nbsp;<?php if (!empty($item['description'])): ?>
                                        <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                            <img src="<?php echo $icon ?>" alt="help" />
                                        </span>
                                    <?php endif; ?></h5>
                                <div class="ui_slider_item ui_slider_item_<?php echo $form_uniqid ?>" id="ui_slider_item_<?php echo $uid ?>"></div>
                                <input type="hidden" name="mdf[<?php echo $key ?>]" id="<?php echo $uid ?>" data-type="double" data-min="<?php echo $preset_min ?>" data-max="<?php echo $preset_max ?>" data-min-now="<?php echo $min ?>" data-max-now="<?php echo $max ?>" data-step="<?php echo $slider_step ?>" data-slider-prefix="<?php echo $slider_prefix ?>" data-slider-postfix="<?php echo $slider_postfix ?>" data-slider-prettify="<?php echo $slider_prettify ?>" value="<?php echo $page_meta_data_filter[$key] ?>" />
                            </div>
                        </td>
                    <?php endif; ?>


                    <?php if ($item['type'] == 'checkbox'): ?>
                        <?php
                        $items_count = -1;
                        if ($widget_options['show_checkbox_items_count']) {
                            $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, 1, $slug, 'checkbox');
                            if (!$items_count) {
                                if ($widget_options['hide_items_where_count_0']) {
                                    continue; //IF NO ONE ITEM WHY NOT TO HIDE THIS?!
                                }
                            }
                        }
                        //+++
                        $is_checked = isset($page_meta_data_filter[$key]) ? (int) $page_meta_data_filter[$key] : '~';
                        ?>
                        <td>
                            <div class="mdf_input_container">
                                <input type="hidden" name="mdf[<?php echo $key ?>]" value="<?php echo $is_checked ?>">
                                <input <?php if ($items_count == 0 AND $widget_options['show_checkbox_items_count']): ?>disabled<?php endif; ?> type="checkbox" class="mdf_option_checkbox" id="<?php echo $key ?>_<?php echo $uid ?>" <?php if ($is_checked AND $is_checked != '~'): ?>checked<?php endif; ?> />
                                <label for="<?php echo $key ?>_<?php echo $uid ?>">&nbsp;<?php _e($item['name']); ?> <?php if ($widget_options['show_checkbox_items_count']) echo '(' . $items_count . ')'; ?>
                                    <?php if (!empty($item['description'])): ?>
                                        <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                            <img src="<?php echo $icon ?>" alt="help" />
                                        </span>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </td>
                    <?php endif; ?>


                    <?php if ($item['type'] == 'textinput'): ?>
                        <?php $text = isset($page_meta_data_filter[$key]) ? $page_meta_data_filter[$key] : ''; ?>
                        <td>
                            <div class="mdf_input_container">
                                <?php if ($item['name']{0} !== '~'): ?>
                                    <h5 class="data-filter-section-title"><?php _e($item['name']) ?>:
                                        <?php if (!empty($item['description'])): ?>
                                            <label for="<?php echo $key ?>_<?php echo $uid ?>">

                                                <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                                    <img src="<?php echo $icon ?>" alt="help" />
                                                </span>

                                            </label>
                                        <?php endif; ?>
                                    </h5>
                                <?php endif; ?>
                                <input type="text" class="mdf_textinput" placeholder="<?php _e($item['textinput']) ?>" name="mdf[<?php echo $key ?>]" value="<?php echo $text ?>" />&nbsp;

                            </div>
                        </td>
                    <?php endif; ?>


                    <?php if ($item['type'] == 'calendar'): ?>
                        <?php
                        wp_enqueue_style('jquery-ui-styles', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/smoothness/jquery-ui.css');
                        $calendar_date = isset($page_meta_data_filter[$key]) ? $page_meta_data_filter[$key] : '';
                        $from = "";
                        $to = "";
                        if (!empty($calendar_date) AND is_array($calendar_date)) {
                            $from = $calendar_date['from'];
                            $to = $calendar_date['to'];
                        }
                        ?>
                        <td>
                            <div class="mdf_input_container">
                                <?php if ($item['name']{0} !== '~'): ?>
                                    <h5 class="data-filter-section-title"><?php _e($item['name']) ?>:
                                        <?php if (!empty($item['description'])): ?>
                                            <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                                <img src="<?php echo $icon ?>" alt="help" />
                                            </span>
                                        <?php endif; ?>
                                    </h5>
                                <?php endif; ?>
                                <input type="hidden" name="mdf[<?php echo $key ?>][from]" value="<?php echo $from ?>" />
                                <input type="text" readonly="readonly" class="mdf_calendar mdf_calendar_from" placeholder="<?php _e('from', 'meta-data-filter') ?>" />
                                &nbsp;-&nbsp;
                                <input type="hidden" name="mdf[<?php echo $key ?>][to]" value="<?php echo $to ?>" />
                                <input type="text" readonly="readonly" class="mdf_calendar mdf_calendar_to" placeholder="<?php _e('to', 'meta-data-filter') ?>" />&nbsp;<label for="<?php echo $key ?>_<?php echo $uid ?>">

                                </label>
                            </div>
                        </td>
                    <?php endif; ?>

                    <?php if ($item['type'] == 'select'): ?>
                        <?php if (!empty($item['select'])): ?>
                            <?php
                            $selected = isset($page_meta_data_filter[$key]) ? $page_meta_data_filter[$key] : NULL;
                            $select_option_title = (isset($item['select_option_title']) AND ! empty($item['select_option_title'])) ? $item['select_option_title'] : __($widget_options['title_for_any']);
                            ?>
                            <td>
                                <div class="mdf_input_container">
                                    <?php if ($item['name']{0} !== '~'): ?>
                                        <h5 class="data-filter-section-title" style="margin-bottom: 4px;" for="<?php echo $key ?>"><?php _e($item['name']) ?>:&nbsp;<?php if (!empty($item['description'])): ?>
                                                <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                                    <img src="<?php echo $icon ?>" alt="help" />
                                                </span>
                                            <?php endif; ?>
                                        </h5>
                                    <?php endif; ?>
                                    <select size="<?php echo (isset($item['select_size']) ? abs((int) $item['select_size']) : 1) ?>" name="mdf[<?php echo $key ?>]" id="<?php echo $key ?>" class="mdf_filter_select">
                                        <option value="~"><?php echo $select_option_title ?></option>
                                        <?php foreach ($item['select'] as $value) : ?>
                                            <?php
                                            $items_count = -1;
                                            if ($widget_options['show_select_items_count']) {
                                                $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, sanitize_title($value), $slug, 'select');
                                                if (!$items_count) {
                                                    if ($widget_options['hide_items_where_count_0']) {
                                                        continue; //IF NO ONE ITEM WHY NOT TO HIDE THIS?!
                                                    }
                                                }
                                            }
                                            ?>
                                            <option <?php if ($items_count == 0 AND $widget_options['show_select_items_count']): ?>disabled<?php endif; ?> value="<?php echo sanitize_title($value); ?>" <?php echo($selected == sanitize_title($value) ? 'selected' : '') ?>><?php _e($value); ?> <?php if ($widget_options['show_select_items_count']) echo '(' . $items_count . ')'; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>


                </tr>


                <?php $counter++; ?>
            <?php endforeach; ?>

        </table>
        <?php if ($widget_options['show_filter_button_after_each_block'] AND $counter > 0): ?>
            <table class="mdf_update_button_table_<?php echo $filter_post_id ?>">
                <tr>
                    <td>
                        <input type="submit" class="mdf_button" name="" value="<?php _e($widget_options['title_for_filter_button']) ?>" /><br />
                        <br />
                    </td>
                </tr>
            </table>
        <?php endif; ?>
    </div>



    <?php
endif;
?>