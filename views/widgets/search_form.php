<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php $unique_id = uniqid(); ?>

<p>
    <label for="<?php echo $widget->get_field_id('title'); ?>"><?php _e('Title', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id('title'); ?>" name="<?php echo $widget->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
</p>

<p>
    <label for="<?php echo $widget->get_field_id(MetaDataFilterCore::$slug_cat); ?>"><?php _e('Meta Data Filter Categories', 'meta-data-filter') ?>:</label>
    <?php
    wp_dropdown_categories(
            array(
                'hide_empty'=>0,
                'orderby'=>'name',
                'taxonomy'=>MetaDataFilterCore::$slug_cat,
                'id'=>$widget->get_field_id(MetaDataFilterCore::$slug_cat),
                'class'=>'widefat',
                'show_option_none'=>__('select category', 'meta-data-filter'),
                'name'=>$widget->get_field_name(MetaDataFilterCore::$slug_cat),
                'selected'=>@$instance[MetaDataFilterCore::$slug_cat]
            )
    );
    ?>
</p>



<p>
    <label for="<?php echo $widget->get_field_id('meta_data_filter_slug'); ?>"><?php _e('Slug', 'meta-data-filter') ?>:</label>
    <?php
    $settings = self::get_settings();
    if(isset($settings['post_types'])) {
        if(!empty($settings['post_types'])) {
            ?>
            <select class="widefat" id="<?php echo $widget->get_field_id('meta_data_filter_slug') ?>" name="<?php echo $widget->get_field_name('meta_data_filter_slug') ?>">
                <?php foreach($settings['post_types'] as $post_name) : ?>
                    <option <?php if($instance['meta_data_filter_slug'] == $post_name) echo 'selected="selected"'; ?> value="<?php echo $post_name ?>" class="level-0"><?php echo $post_name ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }
    ?>
</p>

<p>
    <?php
    $checked = "";
    if($instance['hide_meta_filter_values'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('hide_meta_filter_values'); ?>" name="<?php echo $widget->get_field_name('hide_meta_filter_values'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('hide_meta_filter_values'); ?>"><?php _e('Hide meta values filter options.<br />ATTENTION: this checkbox necessarily if you are going to filter by taxonomy only!', 'meta-data-filter') ?></label>
</p>


<p>
    <?php
    $checked = "";
    if($instance['hide_tax_filter_values'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('hide_tax_filter_values'); ?>" name="<?php echo $widget->get_field_name('hide_tax_filter_values'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('hide_tax_filter_values'); ?>"><?php _e('Hide taxonomies values filter options', 'meta-data-filter') ?>:</label>
</p>


<p>
    <label for="<?php echo $widget->get_field_id('search_result_page'); ?>"><?php _e('Results output page link', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id('search_result_page'); ?>" name="<?php echo $widget->get_field_name('search_result_page'); ?>" value="<?php echo $instance['search_result_page']; ?>" /><br />
    <i><?php _e('Leave this field empty if you want to use the default value from settings', 'meta-data-filter') ?></i>
</p>


<p>
    <label for="<?php echo $widget->get_field_id('search_result_tpl'); ?>"><?php _e('Results output template', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id('search_result_tpl'); ?>" name="<?php echo $widget->get_field_name('search_result_tpl'); ?>" value="<?php echo $instance['search_result_tpl']; ?>" /><br />
    <i><?php _e('Leave this field empty if you want to use the default value from settings', 'meta-data-filter') ?></i>
</p>

<hr />

<h3><?php _e('Meta options', 'meta-data-filter') ?></h3>


<p>
    <label for="<?php echo $widget->get_field_id('and_or'); ?>"><?php _e('AND/OR behaviour for meta filelds', 'meta-data-filter') ?>:</label>
    <?php
    $settings = array(
        'AND'=>__('AND', 'meta-data-filter'),
        'OR'=>__('OR', 'meta-data-filter'),
        'BOTH'=>__('show AND/OR filter', 'meta-data-filter'),
    );
    ?>
    <select class="widefat" id="<?php echo $widget->get_field_id('and_or') ?>" name="<?php echo $widget->get_field_name('and_or') ?>">
        <?php foreach($settings as $k=> $val) : ?>
            <option <?php if($instance['and_or'] == $k) echo 'selected="selected"'; ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
        <?php endforeach; ?>
    </select>

</p>

<p>
    <?php
    $checked = "";
    if($instance['show_checkbox_items_count'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('show_checkbox_items_count'); ?>" name="<?php echo $widget->get_field_name('show_checkbox_items_count'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('show_checkbox_items_count'); ?>"><?php _e('Show near checkbox count of posts', 'meta-data-filter') ?>:</label>
</p>

<p>
    <?php
    $checked = "";
    if($instance['show_select_items_count'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('show_select_items_count'); ?>" name="<?php echo $widget->get_field_name('show_select_items_count'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('show_select_items_count'); ?>"><?php _e('Show count of posts in select option', 'meta-data-filter') ?>:</label>
</p>


<p>
    <?php
    $checked = "";
    if($instance['show_slider_items_count'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('show_slider_items_count'); ?>" name="<?php echo $widget->get_field_name('show_slider_items_count'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('show_slider_items_count'); ?>"><?php _e('Show count of posts in range slider', 'meta-data-filter') ?>:</label>
</p>


<p>
    <?php
    $checked = "";
    if($instance['show_items_count_dynam'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('show_items_count_dynam'); ?>" name="<?php echo $widget->get_field_name('show_items_count_dynam'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('show_items_count_dynam'); ?>"><?php _e('Dynamic post items recount', 'meta-data-filter') ?>:</label>
</p>


<p>
    <?php
    $checked = "";
    if($instance['hide_items_where_count_0'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('hide_items_where_count_0'); ?>" name="<?php echo $widget->get_field_name('hide_items_where_count_0'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('hide_items_where_count_0'); ?>"><?php _e('Hide items where count of items is 0', 'meta-data-filter') ?>:</label>
</p>


<p>
    <?php
    $checked = "";
    if($instance['show_filter_button_after_each_block'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('show_filter_button_after_each_block'); ?>" name="<?php echo $widget->get_field_name('show_filter_button_after_each_block'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('show_filter_button_after_each_block'); ?>"><?php _e('Show filter button after each block', 'meta-data-filter') ?>:</label>
</p>


<hr />

<h3><?php _e('Taxonomies options', 'meta-data-filter') ?></h3>


<p style="background: #ccc; padding: 5px;">
    <label for="<?php echo $widget->get_field_id('taxonomies'); ?>"><b><?php _e('Taxonomies for the selected slug', 'meta-data-filter') ?></b>:</label>
    <?php
    $taxonomies = get_object_taxonomies($instance['meta_data_filter_slug'], 'objects');

    if(!empty($taxonomies)) {
        ?>
        <input type="hidden" name="" value="<?php echo $widget->get_field_name('taxonomies') ?>[]" />
    <ul id="meta_data_filter_tax_ul_<?php echo $unique_id ?>" class="meta_data_filter_tax_ul">
        <?php
        //sort them as were saved
        $taxonomies_sorted = (array) $instance['taxonomies'];
        foreach($taxonomies as $tax) {
            $taxonomies_sorted[$tax->name] = $tax->label;
        }
        if(isset($taxonomies_sorted['post_format'])) {
            unset($taxonomies_sorted['post_format']);
        }
        //removing values from not current post type
        foreach($taxonomies_sorted as $name=> $label) {
            if($label === 'true') {
                unset($taxonomies_sorted[$name]);
            }
        }
        //+++
        $check_checked = false;
        ?>
        <?php foreach($taxonomies_sorted as $name=> $label) :
            ?>
            <li>
                <?php
                $checked = "";
                if(isset($instance['taxonomies'][$name]) AND ! $check_checked) {
                    $checked = 'checked="checked"';
                    $check_checked = true;
                }
                ?>
                <div style="float:left;">
                    <input type="checkbox" id="<?php echo $widget->get_field_id('taxonomies'); ?>_<?php echo $name ?>" name="<?php echo $widget->get_field_name('taxonomies'); ?>[<?php echo $name ?>]" value="true" <?php echo $checked; ?> />
                    <label for="<?php echo $widget->get_field_id('taxonomies'); ?>_<?php echo $name ?>"><?php echo $label ?>:</label>
                </div>
                <div style="float: right;">
                    <?php
                    $show_how = 'select';
                    if(is_array($instance['taxonomies_options_show_how']) AND isset($instance['taxonomies_options_show_how'][$name])) {
                        $show_how = $instance['taxonomies_options_show_how'][$name];
                    }

                    $select_size = 1;
                    if(is_array($instance['taxonomies_options_select_size']) AND isset($instance['taxonomies_options_select_size'][$name])) {
                        $select_size = $instance['taxonomies_options_select_size'][$name];
                    }

                    $hide = '';
                    if(is_array($instance['taxonomies_options_hide']) AND isset($instance['taxonomies_options_hide'][$name])) {
                        $hide = trim($instance['taxonomies_options_hide'][$name], ', ');
                    }
                    ?>
                    <a href="#" class="button mdf_tax_options" data-tax-name="<?php echo $name ?>" data-hide="<?php echo $widget->get_field_id('taxonomies_options_hide'); ?>_<?php echo $name ?>" data-data-show-how="<?php echo $widget->get_field_id('taxonomies_options_show_how'); ?>_<?php echo $name ?>" data-data-select-size="<?php echo $widget->get_field_id('taxonomies_options_select_size'); ?>_<?php echo $name ?>"><?php _e('Options', 'meta-data-filter'); ?></a>
                    <input type="hidden" name="<?php echo $widget->get_field_name('taxonomies_options_hide'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id('taxonomies_options_hide'); ?>_<?php echo $name ?>" value="<?php echo $hide; ?>" />
                    <input type="hidden" name="<?php echo $widget->get_field_name('taxonomies_options_show_how'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id('taxonomies_options_show_how'); ?>_<?php echo $name ?>" value="<?php echo $show_how; ?>" />
                    <input type="hidden" name="<?php echo $widget->get_field_name('taxonomies_options_select_size'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id('taxonomies_options_select_size'); ?>_<?php echo $name ?>" value="<?php echo $select_size; ?>" />
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <div style="background: yellow; padding: 3px;"><?php _e('To show slugs taxonomies you have to add in selected Meta Data Filter Category a data constructor block of type <b>"taxonomy"</b>. It must appear one time only in search form of one widget! Otherwise maybe problems.', 'meta-data-filter'); ?></div>
    <?php
} else {
    echo '<br />';
    _e('No Taxonomies exists for selected slug', 'meta-data-filter');
}
?>
</p>

<p>
    <label for="<?php echo $widget->get_field_id('taxonomies_options_behaviour'); ?>"><?php _e('AND/OR behaviour for taxonomies', 'meta-data-filter') ?>:</label>
    <?php
    $settings = array(
        'AND'=>__('AND', 'meta-data-filter'),
        'OR'=>__('OR', 'meta-data-filter')
    );
    ?>
    <select class="widefat" id="<?php echo $widget->get_field_id('taxonomies_options_behaviour') ?>" name="<?php echo $widget->get_field_name('taxonomies_options_behaviour') ?>">
        <?php foreach($settings as $k=> $val) : ?>
            <option <?php if($instance['taxonomies_options_behaviour'] == $k) echo 'selected="selected"'; ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
        <?php endforeach; ?>
    </select>
</p>


<p>
    <?php
    $checked = "";
    if($instance['taxonomies_options_show_count'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('taxonomies_options_show_count'); ?>" name="<?php echo $widget->get_field_name('taxonomies_options_show_count'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('taxonomies_options_show_count'); ?>"><?php _e('Show count of posts/custom', 'meta-data-filter') ?>:</label>
</p>




<p class="mdf_taxonomies_options_show_count" <?php if(!$instance['taxonomies_options_show_count']): ?>style="display: none;"<?php endif; ?>>
    <?php
    $checked = "";
    if($instance['taxonomies_options_post_recount_dyn'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('taxonomies_options_post_recount_dyn'); ?>" name="<?php echo $widget->get_field_name('taxonomies_options_post_recount_dyn'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('taxonomies_options_post_recount_dyn'); ?>"><?php _e('Dynamic post recount', 'meta-data-filter') ?>:</label>
</p>

<p class="mdf_taxonomies_options_show_count" <?php if(!$instance['taxonomies_options_show_count']): ?>style="display: none;"<?php endif; ?>>
    <?php
    $checked = "";
    if($instance['taxonomies_options_hide_terms_0'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('taxonomies_options_hide_terms_0'); ?>" name="<?php echo $widget->get_field_name('taxonomies_options_hide_terms_0'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('taxonomies_options_hide_terms_0'); ?>"><?php _e('Hide terms where count of items is 0', 'meta-data-filter') ?>:</label>
</p>


<p>
    <?php
    $checked = "";
    if($instance['show_reset_button'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('show_reset_button'); ?>" name="<?php echo $widget->get_field_name('show_reset_button'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('show_reset_button'); ?>"><?php _e('Show reset button', 'meta-data-filter') ?>:</label>
</p>


<p>
    <?php
    $checked = "";
    if($instance['act_without_button'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('act_without_button'); ?>" name="<?php echo $widget->get_field_name('act_without_button'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('act_without_button'); ?>"><?php _e('Auto submit form', 'meta-data-filter') ?>:</label>
</p>



<hr />

<p>
    <label for="<?php echo $widget->get_field_id('title_for_any'); ?>"><?php _e('Title for Any', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id('title_for_any'); ?>" name="<?php echo $widget->get_field_name('title_for_any'); ?>" value="<?php echo $instance['title_for_any']; ?>" />
</p>

<p>
    <label for="<?php echo $widget->get_field_id('title_for_filter_button'); ?>"><?php _e('Title for filter button', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id('title_for_filter_button'); ?>" name="<?php echo $widget->get_field_name('title_for_filter_button'); ?>" value="<?php echo $instance['title_for_filter_button']; ?>" />
</p>



<p>

    <script type="text/javascript">
        jQuery(function() {

            jQuery('#meta_data_filter_ul_<?php echo $unique_id ?>,#meta_data_filter_tax_ul_<?php echo $unique_id ?>').sortable();
            //+++
            //saving widget for getting selected post slug terms
            jQuery('#<?php echo $widget->get_field_id('meta_data_filter_slug') ?>, #<?php echo $widget->get_field_id(MetaDataFilterCore::$slug_cat) ?>').change(function() {
                var widget = jQuery(this).closest('div.widget');
                wpWidgets.save(widget, 0, 1, 0);
            });
            //+++
            jQuery('#<?php echo $widget->get_field_id('taxonomies_options_show_count'); ?>').click(function() {
                if (jQuery(this).is(":checked")) {
                    jQuery(this).parent().siblings('.mdf_taxonomies_options_show_count').show(200);
                } else {
                    jQuery(this).parent().siblings('.mdf_taxonomies_options_show_count').hide(200);
                }
            });
        });
    </script>
</p>
