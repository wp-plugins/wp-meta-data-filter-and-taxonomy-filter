<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
$uniqid = uniqid();
$slug = $shortcode_options['options']['post_type'];
$ajax_results = (isset($shortcode_options['options']['ajax_results'])) ? $shortcode_options['options']['ajax_results'] : 0;
//*** additional taxonomies for Pre-sale question: 
//I have woocommerce with brands plugin installed, each brand page shows the products of this brand, 
//can this plugin filter those products based on category?
//$_REQUEST['MDF_ADDITIONAL_TAXONOMIES'] = array();
if (isset($shortcode_options['options']['additional_taxonomies']) AND ! empty($shortcode_options['options']['additional_taxonomies'])) {
    MetaDataFilter::add_additional_taxonomies($shortcode_options['options']['additional_taxonomies']);
}
?>
<div class="mdf_shortcode_container <?php if ($shortcode_options['options']['auto_submit']): ?>mdf_shortcode_auto_submit<?php endif; ?> mdf_shortcode_skin_<?php echo $shortcode_options['options']['skin'] ?>">
    <form method="get" action="" data-unique-id="<?php echo $uniqid; ?>" data-slug="<?php echo $slug ?>" data-shortcode-id="<?php echo $shortcode_id ?>" id="meta_data_filter_<?php echo $uniqid ?>" class="<?php if ($shortcode_options['options']['ajax_auto_submit'] == 1 AND $ajax_results): ?>mdf_ajax_content_redraw<?php endif; ?> <?php if ($shortcode_options['options']['ajax_auto_submit'] == 1 AND ! $shortcode_options['options']['auto_submit']): ?>mdf_ajax_auto_recount<?php endif; ?> mdf_shortcode_form mdf_shortcode_form_<?php echo $shortcode_id ?>">
        <?php if (self::is_page_mdf_data() AND $shortcode_options['options']['show_found_totally']): ?>
            <div class="mdf_widget_found_count"><?php
                $show_items_count_text = __('Found <span>%s</span> items', 'meta-data-filter');
                if (isset($shortcode_options['options']['show_items_count_text']) AND ! empty($shortcode_options['options']['show_items_count_text'])) {
                    $show_items_count_text = str_replace('%s', '<span>%s</span>', $shortcode_options['options']['show_items_count_text']);
                }
                //***
                echo sprintf(__($show_items_count_text), (isset($_REQUEST['meta_data_filter_count']) ? $_REQUEST['meta_data_filter_count'] : 0));
                ?></div>
        <?php endif; ?>

        <?php
        $meta_data_filter_bool = 'AND';
        $mdf_tax_bool = 'AND';


        $widget_options = array(
            'meta_data_filter_cat' => $shortcode_options['shortcode_cat'],
            'meta_data_filter_slug' => $slug,
            'hide_items_where_count_0' => $shortcode_options['options']['hide_items_where_count_0'],
            'taxonomies_options_hide_terms_0' => $shortcode_options['options']['hide_items_where_count_0'],
            'taxonomies_options_show_count' => $shortcode_options['options']['show_count'],
            'taxonomies_options_post_recount_dyn' => 1,
            'show_slider_items_count' => $shortcode_options['options']['show_count'],
            'show_select_items_count' => $shortcode_options['options']['show_count'],
            'show_checkbox_items_count' => $shortcode_options['options']['show_count'],
            'act_without_button' => $shortcode_options['options']['auto_submit'],
            'shortcode_taxonomies_title' => (isset($shortcode_options['options']['taxonomies_title']) ? $shortcode_options['options']['taxonomies_title'] : ''),
            'taxonomies_show_child_terms' => (isset($shortcode_options['options']['taxonomies_show_child_terms']) ? $shortcode_options['options']['taxonomies_show_child_terms'] : ''),
        );

        $_REQUEST['meta_data_filter_cat_form'] = $shortcode_options['shortcode_cat'];

        $shortcode_has_taxonomies = 0;
        $shortcode_has_meta = 0;

        $icon = MetaDataFilter::get_application_uri() . 'images/tooltip-info.png';
        $settings = MetaDataFilter::get_settings();
        if (!empty($settings['tooltip_icon'])) {
            $icon = $settings['tooltip_icon'];
        }
        //+++
        if (empty($html_items) AND $shortcode_options['options']['tax_only_in_filter']) {
            $html_items = array(
                array(
                    'post_title' => '~Taxonomies',
                    'html_items' => array(
                        'medafi_shortcode_tax_only' => array(
                            'name' => '~Taxonomies',
                            'type' => 'taxonomy',
                            'meta_key' => 'medafi_shortcode_tax_only',
                        )
                    )
                )
            );
        }
        //+++
        if (!empty($html_items) AND is_array($html_items)) {
            $css_block_counter = 0;
            foreach ($html_items as $filter_id => $h_items) {
                //check it for hiding
                $this_block_is_hidden = false;
                if (isset($shortcode_options['filters'][$filter_id])) {
                    if ($shortcode_options['filters'][$filter_id]['is_hidden'] == 1) {
                        $this_block_is_hidden = true;
                    }
                }
                ?>
                <div class="mdf_input_container_block mdf_input_container_block_<?php echo $css_block_counter ++ ?>" <?php if ($this_block_is_hidden): ?>style="display: none;"<?php endif; ?>>
                    <?php if (isset($h_items['post_title']) AND $h_items['post_title']{0} !== '~'): ?>
                        <h4 class="data-filter-section-title"><?php _e($h_items['post_title']) ?></h4>
                    <?php endif; ?>
                    <?php
                    //+++
                    if (!empty($h_items['html_items'])) {
                        foreach ($h_items['html_items'] as $meta_key => $item) {
                            //check is this html option is hidden
                            $this_item_is_hidden = false;
                            if (isset($shortcode_options['filters'][$filter_id])) {
                                if ($shortcode_options['filters'][$filter_id][$meta_key] == 1) {
                                    $this_item_is_hidden = true;
                                }
                            }
                            if ($this_block_is_hidden) {
                                $this_item_is_hidden = true;
                            }
                            //+++
                            $search_key = $meta_key;
                            if (isset($item['is_reflected']) AND $item['is_reflected'] == 1) {
                                $search_key = $item['reflected_key'];
                            }
                            $uid = uniqid();
                            //+++
                            switch ($item['type']) {
                                case 'slider':
                                    $shortcode_has_meta ++;
                                    //wp_enqueue_style('mdf_front_slider_' . $shortcode_options['options']['skin'], self::get_application_uri() . 'views/shortcode/skins/' . $shortcode_options['options']['skin'] . '/slider.css');
                                    $min = $max = 0;


                                    if (isset($page_meta_data_filter[$meta_key])) {
                                        list($min, $max) = explode('^', $page_meta_data_filter[$meta_key]);
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

                                    if (empty($page_meta_data_filter[$meta_key])) {
                                        $page_meta_data_filter[$meta_key] = (int) $min . '^' . $max;
                                    }

                                    //+++

                                    if ($widget_options['show_slider_items_count'] AND ! $this_item_is_hidden) {
                                        $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, array($min, $max), $slug, 'slider');
                                        if (!$items_count) {
                                            if ($widget_options['hide_items_where_count_0']) {
                                                //continue; //IF NO ONE ITEM WHY NOT TO HIDE THIS?!
                                            }
                                        }
                                    }
                                    //++++++++++++++++++++++++++++++++++++
                                    if ($search_key === '_price' AND (isset($item['woo_price_auto']) AND $item['woo_price_auto'] == 1)) {
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
                                    <div class="mdf_input_container" <?php if ($this_item_is_hidden): ?>style="display: none;"<?php endif; ?>>
                                        <h5 class="data-filter-section-title" style="margin-bottom: 4px;" for="<?php echo $uid ?>"><?php _e($item['name']) ?>:&nbsp;<span class="mdf_range"><input type="text" value="<?php echo $min ?>" class="mdf_range_min" data-slider-id="ui_slider_item_<?php echo $uid ?>" /> - <input type="text" value="<?php echo $max ?>" class="mdf_range_max" data-slider-id="ui_slider_item_<?php echo $uid ?>" /></span> &nbsp;<?php if ($widget_options['show_slider_items_count']) echo '(<span class="' . $meta_key . '">' . $items_count . '</span>)'; ?>&nbsp;<?php if (!empty($item['description'])): ?>
                                                <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                                    <img src="<?php echo $icon ?>" alt="help" />
                                                </span>
                                            <?php endif; ?></h5> 
                                        <div class="ui_slider_item ui_slider_item_<?php echo $uniqid ?>" id="ui_slider_item_<?php echo $uid ?>"></div>
                                        <input type="hidden" name="mdf[<?php echo $meta_key ?>]" id="<?php echo $uid ?>" data-type="double" data-min="<?php echo $preset_min ?>" data-max="<?php echo $preset_max ?>" data-min-now="<?php echo $min ?>" data-max-now="<?php echo $max ?>" data-step="<?php echo $slider_step ?>" data-slider-prefix="<?php echo $slider_prefix ?>" data-slider-postfix="<?php echo $slider_postfix ?>" data-slider-prettify="<?php echo $slider_prettify ?>" value="<?php echo $page_meta_data_filter[$meta_key] ?>" />
                                    </div>
                                    <?php
                                    break;

                                case 'select':
                                    $shortcode_has_meta ++;
                                    if (!empty($item['select'])) {
                                        $selected = isset($page_meta_data_filter[$meta_key]) ? $page_meta_data_filter[$meta_key] : NULL;
                                        $select_option_title = (isset($item['select_option_title']) AND ! empty($item['select_option_title'])) ? $item['select_option_title'] : __('Any', 'meta-data-filter');
                                        ?>
                                        <div class="mdf_input_container" <?php if ($this_item_is_hidden): ?>style="display: none;"<?php endif; ?>>

                                            <?php if ($item['name']{0} !== '~'): ?>
                                                <h5 class="data-filter-section-title" style="margin-bottom: 4px;" for="<?php echo $meta_key ?>"><?php _e($item['name']) ?>:
                                                    &nbsp;<?php if (!empty($item['description'])): ?>
                                                        <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                                            <img src="<?php echo $icon ?>" alt="help" />
                                                        </span>
                                                    <?php endif; ?>
                                                </h5>
                                            <?php endif; ?>
                                            <select name="mdf[<?php echo $meta_key ?>]" id="<?php echo $meta_key ?>" class="mdf_filter_select">
                                                <option value="~"><?php echo $select_option_title; ?></option>
                                                <?php foreach ($item['select'] as $value) : ?>
                                                    <?php
                                                    $items_count = -1;
                                                    if ($widget_options['show_select_items_count'] AND ! $this_item_is_hidden) {
                                                        $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, sanitize_title($value), $slug, 'select');
                                                        if (!$items_count) {
                                                            if ($widget_options['hide_items_where_count_0']) {
                                                                continue; //IF NO ONE ITEM WHY NOT TO HIDE THIS?!
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <option <?php if ($items_count == 0 AND $widget_options['show_select_items_count']): ?>disabled<?php endif; ?> value="<?php echo sanitize_title($value); ?>" <?php echo($selected == sanitize_title($value) ? 'selected' : '') ?>><?php _e($value) ?> <?php if ($widget_options['show_select_items_count']) echo '(' . $items_count . ')'; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <?php
                                    }
                                    break;

                                case 'checkbox':
                                    $shortcode_has_meta ++;
                                    $items_count = -1;
                                    if ($widget_options['show_checkbox_items_count'] AND ! $this_item_is_hidden) {
                                        $items_count = MetaDataFilterHtml::get_item_posts_count($page_meta_data_filter, $search_key, 1, $slug, 'checkbox');
                                        if (!$items_count) {
                                            if ($widget_options['hide_items_where_count_0']) {
                                                continue; //IF NO ONE ITEM WHY NOT TO HIDE THIS?!
                                            }
                                        }
                                    }
                                    //+++
                                    $is_checked = isset($page_meta_data_filter[$meta_key]) ? (int) $page_meta_data_filter[$meta_key] : '~';
                                    ?>

                                    <div class="mdf_input_container" <?php if ($this_item_is_hidden): ?>style="display: none;"<?php endif; ?>>                      
                                        <input type="hidden" name="mdf[<?php echo $meta_key ?>]" value="<?php echo $is_checked ?>">
                                        <input <?php if ($items_count == 0 AND $widget_options['show_checkbox_items_count']): ?>disabled<?php endif; ?> type="checkbox" class="mdf_option_checkbox" id="<?php echo $meta_key ?>_<?php echo $uid ?>" <?php if ($is_checked AND $is_checked != '~'): ?>checked<?php endif; ?> />
                                        <label for="<?php echo $meta_key ?>_<?php echo $uid ?>">&nbsp;<?php _e($item['name']) ?> <?php if ($widget_options['show_checkbox_items_count']) echo '(<span class="' . $meta_key . '">' . $items_count . '</span>)'; ?>
                                            <?php if (!empty($item['description'])): ?>
                                                <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                                    <img src="<?php echo $icon ?>" alt="help" />
                                                </span>
                                            <?php endif; ?>
                                        </label>
                                    </div>

                                    <?php
                                    break;


                                case 'calendar':
                                    $shortcode_has_meta ++;
                                    wp_enqueue_style('jquery-ui-styles', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/smoothness/jquery-ui.css');
                                    $calendar_date = isset($page_meta_data_filter[$meta_key]) ? $page_meta_data_filter[$meta_key] : '';
                                    $from = "";
                                    $to = "";
                                    if (!empty($calendar_date) AND is_array($calendar_date)) {
                                        $from = $calendar_date['from'];
                                        $to = $calendar_date['to'];
                                    }
                                    ?>

                                    <div class="mdf_input_container" <?php if ($this_item_is_hidden): ?>style="display: none;"<?php endif; ?>>
                                        <?php if ($item['name']{0} !== '~'): ?>
                                            <h5 class="data-filter-section-title"><?php _e($item['name']) ?>:
                                                <?php if (!empty($item['description'])): ?>
                                                    <label for="<?php echo $meta_key ?>_<?php echo $uid ?>">
                                                        <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                                            <img src="<?php echo $icon ?>" alt="help" />
                                                        </span>
                                                    </label>
                                                <?php endif; ?>
                                            </h5>

                                        <?php endif; ?>
                                        <input type="hidden" name="mdf[<?php echo $meta_key ?>][from]" value="<?php echo $from ?>" />
                                        <input type="text" readonly="readonly" class="mdf_calendar mdf_calendar_from" placeholder="<?php _e('from', 'meta-data-filter') ?>" />
                                        &nbsp;-&nbsp;
                                        <input type="hidden" name="mdf[<?php echo $meta_key ?>][to]" value="<?php echo $to ?>" />
                                        <input type="text" readonly="readonly" class="mdf_calendar mdf_calendar_to" placeholder="<?php _e('to', 'meta-data-filter') ?>" />
                                    </div>                       

                                    <?php
                                    break;


                                case 'textinput':
                                    $shortcode_has_meta ++;
                                    $text = isset($page_meta_data_filter[$meta_key]) ? $page_meta_data_filter[$meta_key] : '';
                                    ?>

                                    <div class="mdf_input_container" <?php if ($this_item_is_hidden): ?>style="display: none;"<?php endif; ?>>
                                        <?php if ($item['name']{0} !== '~'): ?>
                                            <h5 class="data-filter-section-title"><?php _e($item['name']) ?>:
                                                <?php if (!empty($item['description'])): ?>
                                                    <label for="<?php echo $meta_key ?>_<?php echo $uid ?>">
                                                        <span class="mdf_tooltip" title="<?php echo str_replace('"', "'", __($item['description'])); ?>">
                                                            <img src="<?php echo $icon ?>" alt="help" />
                                                        </span>
                                                    </label>
                                                <?php endif; ?>
                                            </h5>

                                        <?php endif; ?>
                                        <input type="text" class="mdf_textinput text" placeholder="<?php _e($item['textinput']) ?>" name="mdf[<?php echo $meta_key ?>]" value="<?php echo $text ?>" />
                                    </div>                       

                                    <?php
                                    break;

                                case 'taxonomy':
                                    $shortcode_has_taxonomies ++;
                                    ?>
                                    <div class="mdf_input_container_taxonomies">
                                        <?php foreach ($shortcode_options['options']['taxonomies'] as $tax_name => $v) : ?>

                                            <?php
                                            if (!$v) {
                                                continue;
                                            }
                                            ?>


                                            <div class="mdf_input_container mdf_taxonomy_<?php echo $tax_name ?> <?php if ($widget_options['act_without_button']): ?>mdf_tax_auto_submit<?php endif; ?> <?php if (isset($widget_options['ajax_items_recount']) AND $widget_options['ajax_items_recount']): ?>mdf_tax_ajax_autorecount<?php endif; ?>" <?php if ($this_item_is_hidden): ?>style="display: none;"<?php endif; ?>>
                                                <?php $show_how = $shortcode_options['options']['taxonomies_type'][$tax_name]; ?>

                                                <?php if ($item['name']{0} !== '~' AND $show_how != 'checkbox'): ?>
                                                    <h5 class="data-filter-section-title" style="display: none;"><?php _e($item['name']) ?>:</h5>
                                                <?php endif; ?>


                                                <input type="hidden" name="mdf[taxonomy][<?php echo $show_how ?>][<?php echo $tax_name ?>]" value="" />
                                                <?php
                                                $hide = '';
                                                /*
                                                  if(isset($widget_options['taxonomies_options_hide'][$tax_name])) {
                                                  $hide = $widget_options['taxonomies_options_hide'][$tax_name];
                                                  }
                                                 */
                                                //***
                                                if ($this_item_is_hidden) {
                                                    $widget_options['taxonomies_options_show_count'] = 0;
                                                }
                                                //+++
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
                                                    //+++
                                                    //check situation when data transfered by shortcode and is in another html item
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
                                                    //draw selects
                                                    if ($show_how == 'select') {
                                                        if (!empty($terms_ids) AND is_array($terms_ids)) {
                                                            foreach ($terms_ids as $k => $term_id) {
                                                                if ($term_id == -1 AND $term_id == 0) {
                                                                    unset($terms_ids[$k]);
                                                                }
                                                            }
                                                        }
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
                                                                $cc ++;
                                                            }
                                                        } else {
                                                            MetaDataFilter::draw_term_childs('select', 0, 0, $tax_name, false, $hide, $widget_options);
                                                            echo '<div class="mdf_taxonomy_child_container" style="display:block;"></div>';
                                                        }
                                                        //+++

                                                        for ($i = 0; $i < $cc; $i ++) {
                                                            echo '</div>';
                                                        }
                                                    }
                                                    //+++
                                                    //*** draw checkboxes
                                                    if ($show_how == 'checkbox') {
                                                        $section_max_height = (isset($shortcode_options['options']['taxonomies_checkbox_maxheight'][$tax_name]) AND ! empty($shortcode_options['options']['taxonomies_checkbox_maxheight'][$tax_name])) ? $shortcode_options['options']['taxonomies_checkbox_maxheight'][$tax_name] : 0;
                                                        ?>
                                                        <h5 class="data-filter-section-title"><?php
                                                            $title = "";
                                                            if (isset($shortcode_options['options']['taxonomies_title'][$tax_name])) {
                                                                $title = $shortcode_options['options']['taxonomies_title'][$tax_name];
                                                            }

                                                            if (empty($title)) {
                                                                $title = __(MetaDataFilterHtml::get_term_label_by_name($tax_name));
                                                            }
                                                            echo $title;
                                                            ?>:</h5>
                                                        <span style="display: inline-block; <?php if (!empty($section_max_height)): ?>max-height: <?php echo $section_max_height ?>px;<?php endif; ?>" class="mdf_tax_filter_section">
                                                            <?php
                                                            $parent_id = 0;
                                                            MetaDataFilter::draw_term_childs('checkbox', $parent_id, $terms_ids, $tax_name, false, $hide, $widget_options);
                                                            ?>
                                                        </span>
                                                        <?php
                                                    }
                                                } else {
                                                    if ($show_how == 'checkbox') {
                                                        $section_max_height = (isset($shortcode_options['options']['taxonomies_checkbox_maxheight'][$tax_name]) AND ! empty($shortcode_options['options']['taxonomies_checkbox_maxheight'][$tax_name])) ? $shortcode_options['options']['taxonomies_checkbox_maxheight'][$tax_name] : 0;
                                                        ?>
                                                        <h5 class="data-filter-section-title"><?php
                                                            $title = "";
                                                            if (isset($shortcode_options['options']['taxonomies_title'][$tax_name])) {
                                                                $title = $shortcode_options['options']['taxonomies_title'][$tax_name];
                                                            }

                                                            if (empty($title)) {
                                                                $title = __(MetaDataFilterHtml::get_term_label_by_name($tax_name));
                                                            }
                                                            echo $title;
                                                            ?>:</h5>
                                                        <span style="display: inline-block; <?php if (!empty($section_max_height)): ?>max-height: <?php echo $section_max_height ?>px;<?php endif; ?>" class="mdf_tax_filter_section">
                                                            <?php
                                                        }
                                                        MetaDataFilter::draw_term_childs($show_how, 0, 0, $tax_name, false, $hide, $widget_options);
                                                        ?>
                                                        <div class="mdf_taxonomy_child_container"><?php MetaDataFilterHtml::draw_tax_loader(); ?></div>
                                                    </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>

                                        <?php endforeach; ?>
                                    </div>
                                    <?php
                                    break;

                                default:
                                    break;
                            }
                        }
                    }
                    ?>
                    <input type="hidden" value="<?php echo $filter_id ?>" name="mdf[filter_post_blocks][]">
                </div>
                <?php
            }
        }
        ?>




        <input type="hidden" class="hidden_page_mdf_for_ajax" value="<?php echo(self::is_page_mdf_data() ? self::get_page_mdf_string() : '') ?>" />
        <input type="hidden" name="mdf_tax_bool" value="<?php echo $mdf_tax_bool ?>" />
        <input type="hidden" name="meta_data_filter_bool" value="<?php echo $meta_data_filter_bool ?>" />
        <input type="hidden" name="mdf[mdf_widget_options][slug]" value="<?php echo $slug ?>" />
        <input type="hidden" name="mdf[mdf_widget_options][meta_data_filter_cat]" value="<?php echo $shortcode_options['shortcode_cat'] ?>" />
        <input type="hidden" name="mdf[mdf_widget_options][show_items_count_dynam]" value="1" />
        <input type="hidden" name="mdf[mdf_widget_options][taxonomies_options_post_recount_dyn]" value="1" />
        <input type="hidden" name="mdf[mdf_widget_options][taxonomies_options_show_count]" value="1" />
        <input type="hidden" name="mdf[mdf_widget_options][taxonomies_options_hide_terms_0]" value="<?php echo $widget_options['taxonomies_options_hide_terms_0'] ?>" />

        <input type="hidden" name="meta_data_filter_cat" value="<?php echo $shortcode_options['shortcode_cat'] ?>" />



        <input type="hidden" name="mdf[mdf_widget_options][hide_meta_filter_values]" value="<?php echo(!$shortcode_has_meta ? 1 : 0); ?>" />

        <input type="hidden" name="mdf[mdf_widget_options][hide_tax_filter_values]" value="<?php echo(!$shortcode_has_taxonomies ? 1 : 0); ?>" />

        <input type="hidden" name="mdf[mdf_widget_options][search_result_page]" value="<?php echo((isset($shortcode_options['options']['search_result_page'])) ? $shortcode_options['options']['search_result_page'] : '') ?>" />
        <input type="hidden" name="mdf[mdf_widget_options][search_result_tpl]" value="<?php echo((isset($shortcode_options['options']['search_result_tpl'])) ? $shortcode_options['options']['search_result_tpl'] : '') ?>" />
        <input type="hidden" name="mdf[mdf_widget_options][woo_search_panel_id]" value="<?php echo((isset($shortcode_options['options']['woo_search_panel_id'])) ? $shortcode_options['options']['woo_search_panel_id'] : '') ?>" />
        <input type="hidden" name="mdf[mdf_widget_options][tax_only_in_filter]" value="<?php echo((isset($shortcode_options['options']['tax_only_in_filter'])) ? $shortcode_options['options']['tax_only_in_filter'] : 0) ?>" />

        <?php
        if (isset($shortcode_options['options']['custom_filter_panel'])) {
            $custom_filter_panel = $shortcode_options['options']['custom_filter_panel'];
            if (!empty($custom_filter_panel) AND is_array($custom_filter_panel)):
                foreach ($custom_filter_panel as $k => $v) {
                    if (!$v) {
                        unset($custom_filter_panel[$k]);
                    }
                }
                ?>
                <input type="hidden" value="<?php echo implode(',', array_keys($custom_filter_panel)); ?>" name="custom_filter_panel" />
                <?php
            endif;
        }
        ?>

        <?php if ($shortcode_options['options']['auto_submit'] != 1 AND ! $ajax_results): ?>
            <div class="mdf_shortcode_submit_button">
                <?php
                $filter_button_text = (isset($shortcode_options['options']['filter_button_text']) AND ! empty($shortcode_options['options']['filter_button_text'])) ? $shortcode_options['options']['filter_button_text'] : __("Filter", 'meta-data-filter');
                ?>
                <input type="submit" class="button mdf_button" name="" value="<?php _e($filter_button_text) ?>" />
            </div>
        <?php endif; ?>

        <?php if ($shortcode_options['options']['show_reset_button'] AND self::is_page_mdf_data()): ?>
            <div class="mdf_shortcode_reset_button">
                <?php
                $reset_link = $shortcode_options['options']['custom_reset_link'];
                if ($reset_link == 'self') {
                    if (is_page() OR is_single()) {
                        $reset_link = esc_url(apply_filters('the_permalink', get_permalink()));
                    } else {
                        $reset_link = $_SERVER['SCRIPT_URI'];
                    }
                }
                ?>
                <input type="button" <?php if (!empty($reset_link)): ?>onclick="window.location = '<?php echo $reset_link ?>'"<?php endif; ?> class="button mdf_button mdf_reset_button" name="" value="<?php _e('Reset', 'meta-data-filter') ?>" />
            </div>
        <?php endif; ?>


        <div class="mdf_one_moment_txt" style="display: none;">
            <span><?php MetaDataFilterHtml::draw_tax_loader(); ?></span>
        </div>

    </form>

    <?php
//+++
    if (isset($shortcode_options['options']['search_result_page']) AND ! empty($shortcode_options['options']['search_result_page'])) {
        $search_url = $shortcode_options['options']['search_result_page'];
    } else {
        $search_url = MetaDataFilterCore::get_search_url_page();
    }

    //WPML compatibility
    if (class_exists('SitePress')) {
        $search_url = str_replace(site_url(), site_url() . '/' . ICL_LANGUAGE_CODE, $search_url);
    }

//+++
    if (substr_count($search_url, 'page_id')) {
        $search_url = $search_url . '&';
    } else {
        $search_url = $search_url . '?';
    }
    ?>
    <script type="text/javascript">
        jQuery(function () {
            mdf_init_search_form("<?php echo $uniqid ?>", "<?php echo $slug ?>", "<?php echo $search_url ?>", <?php echo $shortcode_options['options']['auto_submit'] ?>, <?php echo $shortcode_options['options']['ajax_auto_submit'] ?>);
        });
    </script>
    <div style="clear: both;"></div>
</div>
