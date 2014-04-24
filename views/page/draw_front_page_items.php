<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php if(!empty($filter_block['items'])): ?>

    <input type="hidden" name="mdf[filter_post_blocks][]" value="<?php echo $filter_post_id ?>" />

    <?php if($filter_block['name']{0} !== '~'): ?>
        <h4 class="data-filter-section-title" style="margin-bottom: 9px;"><?php _e($filter_block['name']) ?></h4>
    <?php endif; ?>

    <?php
    $counter = 0;    
    $section_height = (int) get_post_meta($filter_post_id, 'widget_section_max_height', true);
    ?>
    <div class="mdf_filter_section" <?php if($section_height): ?>style="max-height:<?php echo $filter_post_id; ?>px;"<?php endif; ?>>
        <table style="width: 98%;">
            <?php foreach($filter_block['items'] as $key=> $item) : $uid = uniqid(); ?>

                <?php
                $search_key = $key;
                if(isset($item['is_reflected']) AND $item['is_reflected'] == 1) {
                    $search_key = $item['reflected_key'];
                }

                //+++
                if($widget_options['hide_meta_filter_values'] == 'true') {
                    if($item['type'] != 'taxonomy') {
                        continue;
                    }
                }

                if($widget_options['hide_tax_filter_values'] == 'true') {
                    if($item['type'] == 'taxonomy') {
                        continue;
                    }
                }
                ?>
                <tr>

                    <?php if($item['type'] == 'taxonomy'): ?>
                        <?php $one_tax_showed = false; ?>
                        <?php if(!empty($widget_options['taxonomies']) AND is_array($widget_options['taxonomies'])): ?>
                            <td>
                                <?php foreach($widget_options['taxonomies'] as $tax_name=> $v) : ?>
                                    <?php
                                    if($one_tax_showed) {
                                        break;
                                    } else {
                                        $one_tax_showed = true;
                                    }
                                    ?>
                                    <div class="mdf_input_container mdf_section_tax mdf_section_tax_<?php echo $tax_name ?>">
                                        <?php $show_how = $widget_options['taxonomies_options_show_how'][$tax_name]; ?>

                                        <?php if($item['name']{0} !== '~' AND $show_how != 'checkbox'): ?>
                                                                                                                                                                                            <!-- <h5 class="data-filter-section-title"><?php _e($item['name']) ?>:</h5> -->
                                        <?php endif; ?>


                                        <input type="hidden" name="mdf[taxonomy][<?php echo $show_how ?>][<?php echo $tax_name ?>]" value="" />
                                        <?php
                                        $hide = '';
                                        if(isset($widget_options['taxonomies_options_hide'][$tax_name])) {
                                            $hide = $widget_options['taxonomies_options_hide'][$tax_name];
                                        }

                                        //***
                                        if(self::is_page_mdf_data()) {
                                            $taxonomies = array();
                                            if(isset($page_meta_data_filter['taxonomy'])) {
                                                $taxonomies = $page_meta_data_filter['taxonomy'];
                                                //unset($page_meta_data_filter['taxonomy']);
                                            }
                                            //+++
                                            $terms_ids = array();
                                            if(isset($taxonomies[$show_how])) {
                                                if(array_key_exists($tax_name, $taxonomies[$show_how])) {
                                                    $terms_ids = $taxonomies[$show_how][$tax_name];
                                                }
                                            }
                                            //check situation when data transfered by shortcode and data is in another html item
                                            if(empty($terms_ids)) {
                                                $tmp = $taxonomies;
                                                unset($tmp[$show_how]);
                                                if(!empty($tmp) AND is_array($tmp)) {
                                                    foreach($tmp as $s_h=> $txs) {
                                                        if(array_key_exists($tax_name, $txs)) {
                                                            $terms_ids = $tmp[$s_h][$tax_name];
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            //print_r($terms_ids);
                                            //+++
                                            //draw selects
                                            if($show_how == 'select') {
                                                if(!empty($terms_ids) AND is_array($terms_ids)) {
                                                    foreach($terms_ids as $k=> $term_id) {
                                                        if($term_id == -1 AND $term_id == 0) {
                                                            unset($terms_ids[$k]);
                                                        }
                                                    }
                                                }
                                                //+++
                                                $cc = 0;
                                                if(!empty($terms_ids) AND is_array($terms_ids)) {
                                                    foreach($terms_ids as $term_id) {
                                                        $parent_id = 0;
                                                        if($cc > 0) {
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

                                                for($i = 0; $i < $cc; $i++) {
                                                    echo '</div>';
                                                }
                                            }
                                            //+++
                                            //*** draw checkboxes
                                            if($show_how == 'checkbox') {
                                                ?>
                                                <h4 class="data-filter-section-title"><?php _e(MetaDataFilterHtml::get_term_label_by_name($tax_name)) ?>:</h4>
                                                <?php
                                                $parent_id = 0;
                                                MetaDataFilter::draw_term_childs('checkbox', $parent_id, $terms_ids, $tax_name, false, $hide, $widget_options);
                                            }
                                        } else {
                                            if($show_how == 'checkbox') {
                                                ?>
                                                <h4 class="data-filter-section-title"><?php _e(MetaDataFilterHtml::get_term_label_by_name($tax_name)) ?>:</h4>
                                                <?php
                                            }
                                            MetaDataFilter::draw_term_childs($show_how, 0, 0, $tax_name, false, $hide, $widget_options);
                                            ?>
                                            <div class="mdf_taxonomy_child_container"><?php if($tax_name !== 'post_tag') echo MetaDataFilterHtml::draw_tax_loader(); ?></div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if($item['type'] == 'slider'): ?>
                        <?php
                        wp_enqueue_style('mdf_front_slider', self::get_application_uri() . 'css/front_slider.css');
                        $min = $max = 0;
                        if(isset($page_meta_data_filter[$key])) {
                            list($min, $max) = explode('^', $page_meta_data_filter[$key]);
                        }
                        list($preset_min, $preset_max) = explode('^', $item['slider']);

                        $slider_step = isset($item['slider_step']) ? $item['slider_step'] : 0;

                        if($min == $max AND $max == 0) {
                            $min = $preset_min;
                            $max = $preset_max;
                        }
                        if(empty($page_meta_data_filter[$key])) {
                            $page_meta_data_filter[$key] = (int) $min . '^' . $max;
                        }
                        //+++

                        if($widget_options['show_slider_items_count']) {
                            $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, array($min, $max), $slug, 'slider');
                            if(!$items_count) {
                                if($widget_options['hide_items_where_count_0']) {
                                    //continue; //IF NO ONE ITEM WHY NOT TO HIDE THIS?!
                                }
                            }
                        }

                        //+++
                        ?>
                        <td>
                            <div class="mdf_input_container">
                                <h5 class="data-filter-section-title" style="margin-bottom: 4px;" for="<?php echo $uid ?>"><?php _e($item['name']) ?>:&nbsp;<span class="mdf_range"><input type="text" value="<?php echo $min ?>" class="mdf_range_min" data-slider-id="ui_slider_item_<?php echo $uid ?>" /> - <input type="text" value="<?php echo $max ?>" class="mdf_range_max" data-slider-id="ui_slider_item_<?php echo $uid ?>" /></span> &nbsp;<?php if($widget_options['show_slider_items_count']) echo '(' . $items_count . ')'; ?></h5>
                                <div class="ui_slider_item ui_slider_item_<?php echo $form_uniqid ?>" id="ui_slider_item_<?php echo $uid ?>"></div>
                                <input type="hidden" name="mdf[<?php echo $key ?>]" id="<?php echo $uid ?>" data-min="<?php echo $preset_min ?>" data-max="<?php echo $preset_max ?>" data-min-now="<?php echo $min ?>" data-max-now="<?php echo $max ?>" data-step="<?php echo $slider_step ?>" value="<?php echo $page_meta_data_filter[$key] ?>" />

                            </div>
                        </td>
                    <?php endif; ?>


                    <?php if($item['type'] == 'checkbox'): ?>
                        <?php
                        $items_count = -1;
                        if($widget_options['show_checkbox_items_count']) {
                            $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, 1, $slug, 'checkbox');
                            if(!$items_count) {
                                if($widget_options['hide_items_where_count_0']) {
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
                                <input <?php if($items_count == 0 AND $widget_options['show_checkbox_items_count']): ?>disabled<?php endif; ?> type="checkbox" class="mdf_option_checkbox" id="<?php echo $key ?>_<?php echo $uid ?>" <?php if($is_checked AND $is_checked != '~'): ?>checked<?php endif; ?> />
                                <label for="<?php echo $key ?>_<?php echo $uid ?>">&nbsp;<?php _e($item['name']); ?> <?php if($widget_options['show_checkbox_items_count']) echo '(' . $items_count . ')'; ?>
                                  
                                </label>
                            </div>
                        </td>
                    <?php endif; ?>


                    <?php if($item['type'] == 'select'): ?>
                        <?php if(!empty($item['select'])): ?>
                            <?php $selected = isset($page_meta_data_filter[$key]) ? $page_meta_data_filter[$key] : NULL; ?>
                            <td>
                                <div class="mdf_input_container">
                                    <h5 class="data-filter-section-title" style="margin-bottom: 4px;" for="<?php echo $key ?>"><?php _e($item['name']) ?>:</h5>
                                    <select size="<?php echo (isset($item['select_size']) ? abs((int) $item['select_size']) : 1) ?>" name="mdf[<?php echo $key ?>]" id="<?php echo $key ?>" class="mdf_filter_select">
                                        <option value="~"><?php _e($widget_options['title_for_any']) ?></option>
                                        <?php foreach($item['select'] as $value) : ?>
                                            <?php
                                            $items_count = -1;
                                            if($widget_options['show_select_items_count']) {
                                                $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, sanitize_title($value), $slug, 'select');
                                                if(!$items_count) {
                                                    if($widget_options['hide_items_where_count_0']) {
                                                        continue; //IF NO ONE ITEM WHY NOT TO HIDE THIS?!
                                                    }
                                                }
                                            }
                                            ?>
                                            <option <?php if($items_count == 0 AND $widget_options['show_select_items_count']): ?>disabled<?php endif; ?> value="<?php echo sanitize_title($value); ?>" <?php echo($selected == sanitize_title($value) ? 'selected' : '') ?>><?php _e($value); ?> <?php if($widget_options['show_select_items_count']) echo '(' . $items_count . ')'; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>


                </tr>


                <?php $counter++; ?>
            <?php endforeach; ?>
            <?php if($widget_options['show_filter_button_after_each_block'] AND $counter > 0): ?>
                <tr>
                    <td>
                        <input type="submit" class="mdf_button" name="" value="<?php _e($widget_options['title_for_filter_button']) ?>" />
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php
endif;
?>