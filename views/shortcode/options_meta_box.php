<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>

<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e('Select filter category', 'meta-data-filter') ?><br /></th>
            <td>
                <fieldset>

                    <?php
                    if(!empty($mdf_terms)) {
                        ?>
                        <select id="mdf_shortcode_cat" name="shortcode_options[shortcode_cat]">
                            <option value="-1"><?php _e('Select filter category', 'meta-data-filter') ?></option>
                            <?php foreach($mdf_terms as $term) : ?>
                                <option <?php echo selected($shortcode_options['shortcode_cat'], $term->term_id) ?> value="<?php echo $term->term_id ?>"><?php echo $term->name ?></option>
                            <?php endforeach; ?>
                        </select><br />
                        <?php
                    } else {
                        _e('No one filter MDTF Category created!', 'meta-data-filter');
                    }
                    ?>

                </fieldset>
            </td>
        </tr>

    </tbody>
</table>

<ul id="mdf_custom_popup_selected_filters">
    <?php
    if($shortcode_options['shortcode_cat'] > 0) {
        echo MetaDataFilterShortcodes::draw_shortcode_html_items($shortcode_options, $html_items);
    }
    ?>

</ul>

<br /><br /><br />
<h3><?php _e('Shortcode options', 'meta-data-filter') ?></h3>

<input type="hidden" name="shortcode_options[options]" value="" />
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e('Taxomies only in filter', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="hidden" name="shortcode_options[options][tax_only_in_filter]" value="<?php echo (int) @$shortcode_options['options']['tax_only_in_filter']; ?>">
                <input type="checkbox" class="mdf_shortcode_options" <?php if((int) @$shortcode_options['options']['tax_only_in_filter']): ?>checked<?php endif; ?> /> <br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Results output page link', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="text" class="widefat" placeholder="<?php _e('leave this field empty to use output page from the plugin settings', 'meta-data-filter') ?>" name="shortcode_options[options][search_result_page]" value="<?php echo @$shortcode_options['options']['search_result_page']; ?>"><br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Results output template', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="text" class="widefat" placeholder="<?php _e('leave this field empty to use output page from the plugin settings', 'meta-data-filter') ?>" name="shortcode_options[options][search_result_tpl]" value="<?php echo @$shortcode_options['options']['search_result_tpl']; ?>"><br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Custom text for search results', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="text" class="widefat" placeholder="<?php _e('Example: Found &lt;span&gt;%s&lt;/span&gt; items', 'meta-data-filter') ?>" name="shortcode_options[options][show_items_count_text]" value="<?php echo @$shortcode_options['options']['show_items_count_text']; ?>"><br />
            </td>
        </tr>
         <tr valign="top">
            <th scope="row"><?php _e('Custom reset link', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="text" class="widefat" placeholder="<?php _e('Custom reset link. Leave this field empty to reset the form inputs ONLY by ajax.', 'meta-data-filter') ?>" name="shortcode_options[options][custom_reset_link]" value="<?php echo @$shortcode_options['options']['custom_reset_link']; ?>"><br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Auto submit', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="hidden" name="shortcode_options[options][auto_submit]" value="<?php echo (int) @$shortcode_options['options']['auto_submit']; ?>">
                <input type="checkbox" class="mdf_shortcode_options" <?php if((int) @$shortcode_options['options']['auto_submit']): ?>checked<?php endif; ?> /> <br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('AJAX Auto recount', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="hidden" name="shortcode_options[options][ajax_auto_submit]" value="<?php echo (int) @$shortcode_options['options']['ajax_auto_submit']; ?>">
                <input type="checkbox" class="mdf_shortcode_options" <?php if((int) @$shortcode_options['options']['ajax_auto_submit']): ?>checked<?php endif; ?> />&nbsp;
                <?php _e('To use this option please uncheck "Auto submit"', 'meta-data-filter') ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('AJAX items output', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="hidden" name="shortcode_options[options][ajax_results]" value="<?php echo (int) @$shortcode_options['options']['ajax_results']; ?>">
                <input type="checkbox" class="mdf_shortcode_options" <?php if((int) @$shortcode_options['options']['ajax_results']): ?>checked<?php endif; ?> />&nbsp;
                <?php _e('To use this option please uncheck "Auto submit" and check "AJAX Auto recount"', 'meta-data-filter') ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Show count', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="hidden" name="shortcode_options[options][show_count]" value="<?php echo (int) @$shortcode_options['options']['show_count']; ?>">
                <input type="checkbox" class="mdf_shortcode_options" <?php if((int) @$shortcode_options['options']['show_count']): ?>checked<?php endif; ?> /> <br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Hide items where count is 0', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="hidden" name="shortcode_options[options][hide_items_where_count_0]" value="<?php echo (int) @$shortcode_options['options']['hide_items_where_count_0']; ?>">
                <input type="checkbox" class="mdf_shortcode_options" <?php if((int) @$shortcode_options['options']['hide_items_where_count_0']): ?>checked<?php endif; ?> /> <br />
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><?php _e('Show reset button', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="hidden" name="shortcode_options[options][show_reset_button]" value="<?php echo (int) @$shortcode_options['options']['show_reset_button']; ?>">
                <input type="checkbox" class="mdf_shortcode_options" <?php if((int) @$shortcode_options['options']['show_reset_button']): ?>checked<?php endif; ?> /> <br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Show found items count text', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="hidden" name="shortcode_options[options][show_found_totally]" value="<?php echo (int) @$shortcode_options['options']['show_found_totally']; ?>">
                <input type="checkbox" class="mdf_shortcode_options" <?php if((int) @$shortcode_options['options']['show_found_totally']): ?>checked<?php endif; ?> /> <br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Filter button text', 'meta-data-filter') ?><br /></th>
            <td>
                <input type="text" class="widefat" name="shortcode_options[options][filter_button_text]" value="<?php echo @$shortcode_options['options']['filter_button_text']; ?>"><br />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Post type', 'meta-data-filter') ?><br /></th>
            <td>
                <?php
                $settings = self::get_settings();
                if(isset($settings['post_types'])) {
                    if(!empty($settings['post_types'])) {
                        ?>
                        <select name="shortcode_options[options][post_type]" onchange="alert('<?php _e('To manage by taxonomies of the selected post type you should press Update button!', 'meta-data-filter') ?>')">
                            <?php foreach($settings['post_types'] as $post_name) : ?>
                                <option <?php echo selected(@$shortcode_options['options']['post_type'], $post_name) ?> value="<?php echo $post_name ?>" class="level-0"><?php echo $post_name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Taxonomies options', 'meta-data-filter') ?><br /></th>
            <td>

                <?php
                $taxonomies_tmp = get_object_taxonomies($shortcode_options['options']['post_type'], 'objects');
                $activated_taxonomies = @$shortcode_options['options']['taxonomies'];
                $output_tax_array = array();
                $taxonomies = array();
                if(!empty($taxonomies_tmp)) {
                    if(!empty($activated_taxonomies) AND is_array($activated_taxonomies)) {
                        foreach($activated_taxonomies as $tax_key=> $val) {
                            if($val) {
                                if(!isset($taxonomies_tmp[$tax_key])) {
                                    continue;
                                }
                                $output_tax_array[$tax_key] = $taxonomies_tmp[$tax_key]->labels->name;
                            }
                        }
                    }
                    //+++
                    foreach($taxonomies_tmp as $tax_key=> $tax) {
                        if(!key_exists($tax_key, $output_tax_array)) {
                            $taxonomies[$tax_key] = $tax->labels->name;
                        }
                    }
                }
                $output_tax_array = array_merge($output_tax_array, $taxonomies);
                ?>
                <?php if(!empty($output_tax_array)): ?>
                    <ul style="margin-left: 15px;" id="mdf_shotcode_taxonomies">
                        <?php foreach($output_tax_array as $tax_key=> $tax_name) : ?>
                            <li>
                                <input type="text" placeholder="<?php _e('taxonomy custom title', 'meta-data-filter') ?>" name="shortcode_options[options][taxonomies_title][<?php echo $tax_key ?>]" value="<?php echo @$shortcode_options['options']['taxonomies_title'][$tax_key] ?>">&nbsp;
                                <input type="text" placeholder="<?php _e('checkbox block max-height', 'meta-data-filter') ?>" name="shortcode_options[options][taxonomies_checkbox_maxheight][<?php echo $tax_key ?>]" value="<?php echo @$shortcode_options['options']['taxonomies_checkbox_maxheight'][$tax_key] ?>">&nbsp;
                                <?php
                                $taxonomies_type = 'select';
                                if(isset($shortcode_options['options']['taxonomies_type'][$tax_key])) {
                                    $taxonomies_type = $shortcode_options['options']['taxonomies_type'][$tax_key];
                                }
                                ?>
                                <select name="shortcode_options[options][taxonomies_type][<?php echo $tax_key ?>]">
                                    <?php foreach(self::$tax_items_types as $ikey=> $iname) : ?>
                                        <option <?php selected($taxonomies_type, $ikey) ?> value="<?php echo $ikey ?>"><?php echo $iname ?></option>
                                    <?php endforeach; ?>
                                </select>&nbsp;
                                <?php
                                $show_child_terms = 0;
                                if(isset($shortcode_options['options']['taxonomies_show_child_terms'][$tax_key])) {
                                    $show_child_terms = $shortcode_options['options']['taxonomies_show_child_terms'][$tax_key];
                                }
                                ?>
                                <select style="<?php if($taxonomies_type != 'select'): ?>display: none;<?php endif; ?>" name="shortcode_options[options][taxonomies_show_child_terms][<?php echo $tax_key ?>]">
                                    <option <?php selected($show_child_terms, 0) ?> value="0"><?php echo _e('One child below', 'meta-data-filter'); ?></option>
                                    <option <?php selected($show_child_terms, 1) ?> value="1"><?php echo _e('All childs below', 'meta-data-filter'); ?></option>
                                </select>&nbsp;
                                <?php $is_checked = (int) @$shortcode_options['options']['taxonomies'][$tax_key]; ?>
                                <input type="hidden" name="shortcode_options[options][taxonomies][<?php echo $tax_key ?>]" value="<?php echo $is_checked ?>">
                                <input type="checkbox" <?php echo checked($is_checked, 1) ?> class="mdf_shortcode_options" />&nbsp;<?php echo $tax_name; ?>                                
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Additional taxonomies conditions', 'meta-data-filter') ?><br /></th>
            <td>
                <textarea class="widefat" name="shortcode_options[options][additional_taxonomies]"><?php echo @$shortcode_options['options']['additional_taxonomies']; ?></textarea><br />
                <i><b><?php _e('Example', 'meta-data-filter') ?></b>: product_cat=96,3^boutiques=77,88. <b><?php _e('Syntax is', 'meta-data-filter') ?></b>: taxonomy_name=term_id,term_id,term_id ^ taxonomy_name2=term_id,term_id,term_id <br />...</i>
            </td>
        </tr>

       
            <tr valign="top">
                <th scope="row"><?php _e('Sort panel', 'meta-data-filter') ?><br /></th>
                <td>
                    <?php MDTF_SORT_PANEL::draw_options_select(@$shortcode_options['options']['woo_search_panel_id'], 'shortcode_options[options][woo_search_panel_id]'); ?>
                </td>
            </tr>
        

        <?php if(isset($shortcode_options['shortcode_cat']) AND $shortcode_options['shortcode_cat'] > 0): ?>
            <tr valign="top">
                <th scope="row"><?php _e('Custom filter panel', 'meta-data-filter') ?><br /></th>
                <td>
                    <input type="hidden" name="shortcode_options[options][custom_filter_panel]" value="" />
                    <?php
                    $sort_filters = MetaDataFilterPage::get_html_sliders_by_filter_cat($shortcode_options['shortcode_cat']);
                    $saved_sort_filters = @$shortcode_options['options']['custom_filter_panel'];
                    $sort_filters_sortded = array();
                    if(empty($saved_sort_filters)) {
                        $sort_filters_sortded = $sort_filters;
                    } else {
                        if(!empty($sort_filters) AND is_array($sort_filters)) {
                            foreach($saved_sort_filters as $key=> $v) {
                                if($v AND isset($sort_filters[$key])) {
                                    $sort_filters_sortded[$key] = $sort_filters[$key];
                                }
                            }
                            foreach($sort_filters as $key=> $name) {
                                if(!key_exists($key, $sort_filters_sortded)) {
                                    $sort_filters_sortded[$key] = $name;
                                }
                            }
                        }
                    }
                    ?>
                    <ul id="mdf_shortcodes_sort_panel">
                        <?php if(!empty($sort_filters_sortded)): ?>
                            <?php foreach($sort_filters_sortded as $key=> $name) : ?>
                                <li>
                                    <?php $is_checked = (int) @$shortcode_options['options']['custom_filter_panel'][$key]; ?>
                                    <input type="hidden" name="shortcode_options[options][custom_filter_panel][<?php echo $key ?>]" value="<?php echo $is_checked ?>">
                                    <input type="checkbox" <?php echo checked($is_checked, 1) ?> class="mdf_shortcode_options" />&nbsp;<?php echo $name; ?> 
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </td>
            </tr>
        <?php endif; ?>
        <tr valign="top">
            <th scope="row"><?php _e('Shortcode front skin', 'meta-data-filter') ?><br /></th>
            <td>
                <?php $themes = MetaDataFilterShortcodes::get_sh_skins(); ?>
                <select name="shortcode_options[options][skin]">
                    <?php foreach($themes as $theme) : ?>
                        <option <?php echo selected(@$shortcode_options['options']['skin'], $theme) ?> value="<?php echo $theme ?>"><?php echo $theme ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </tbody>
</table>

