<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
if (!isset($uniqid))
{
    $uniqid = 'medafi_' . uniqid();
}
?>
<a class="delete_item_from_data_group close-drag-holder" data-item-key="<?php echo $uniqid; ?>" href="#"></a>

<h4>
    <?php _e("Item Name", 'meta-data-filter') ?>:
</h4>

<p>
    <input type="text" placeholder="<?php _e("enter item name here", 'meta-data-filter') ?>" value="<?php echo(isset($itemdata) ? $itemdata['name'] : "") ?>" name="html_item[<?php echo $uniqid ?>][name]" />
</p>

<br />
<a class="edit-slug button button-small mdf_admin_flter_item_box_toggle" href="#"><?php _e("Toggle", 'meta-data-filter') ?></a>
<div style="display:none;" class="mdf_admin_flter_item_box">

    <h4><?php _e("Item Type", 'meta-data-filter') ?>:&nbsp;</h4>

    <p>
        <label class="sel">
            <select name="html_item[<?php echo $uniqid ?>][type]" class="data_group_item_select">
                <?php foreach (self::$items_types as $key => $name) : ?>
                    <option <?php echo(isset($itemdata) ? ($itemdata['type'] == $key ? "selected" : "") : "") ?> value="<?php echo $key ?>"><?php _e($name) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </p>

    <br />

    <div class="data_group_item_html">

        <div class="data_group_item_template_slider data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'slider' ? "block" : "none") : "none") ?>">
            <h4><?php _e("From^To", 'meta-data-filter') ?></h4>
            <input type="text" placeholder="<?php _e("example", 'meta-data-filter') ?>:0^100" value="<?php echo(isset($itemdata) ? $itemdata['slider'] : '') ?>" name="html_item[<?php echo $uniqid ?>][slider]" style="width: 45%;" /><br />
            <h4><?php _e("Slider step", 'meta-data-filter') ?></h4>
            <input type="text" placeholder="<?php _e("0 or empty mean auto step", 'meta-data-filter') ?>" value="<?php echo(isset($itemdata['slider_step']) ? $itemdata['slider_step'] : '') ?>" name="html_item[<?php echo $uniqid ?>][slider_step]" style="width: 45%;" /><br />
            <h4><?php _e("Prefix", 'meta-data-filter') ?></h4>
            <input type="text" placeholder="<?php _e("Example: $", 'meta-data-filter') ?>" value="<?php echo(isset($itemdata['slider_prefix']) ? $itemdata['slider_prefix'] : '') ?>" name="html_item[<?php echo $uniqid ?>][slider_prefix]" style="width: 45%;" /><br />
            <h4><?php _e("Postfix", 'meta-data-filter') ?></h4>
            <input type="text" placeholder="<?php _e("Example: €", 'meta-data-filter') ?>" value="<?php echo(isset($itemdata['slider_postfix']) ? $itemdata['slider_postfix'] : '') ?>" name="html_item[<?php echo $uniqid ?>][slider_postfix]" style="width: 45%;" /><br />
            <h4><?php _e("Prettify", 'meta-data-filter') ?></h4>
            <?php $slider_prettify = (isset($itemdata['slider_prettify']) ? $itemdata['slider_prettify'] : 1) ?>
            <select name="html_item[<?php echo $uniqid ?>][slider_prettify]">
                <option value="1" <?php if ($slider_prettify == 1): ?>selected=""<?php endif; ?>><?php _e("Yes", 'meta-data-filter') ?></option>
                <option value="0" <?php if ($slider_prettify == 0): ?>selected=""<?php endif; ?>><?php _e("No", 'meta-data-filter') ?></option>
            </select><br />




            <h4><?php _e("Search inside range", 'meta-data-filter') ?></h4>
            <?php $slider_range_value = (isset($itemdata['slider_range_value']) ? (int) $itemdata['slider_range_value'] : 0) ?>
            <select name="html_item[<?php echo $uniqid ?>][slider_range_value]">
                <option value="0" <?php if ($slider_range_value == 0): ?>selected=""<?php endif; ?>><?php _e("No", 'meta-data-filter') ?></option>
                <option value="1" <?php if ($slider_range_value == 1): ?>selected=""<?php endif; ?>><?php _e("Yes", 'meta-data-filter') ?></option>
            </select><br />
            <i><?php _e("Enabling this mode allows you to set 2 values in post for range-slider - RANGE 'From' and 'To'. Not possible to reflect this. On the front range-slider is single!", 'meta-data-filter') ?></i>


            <?php if (class_exists('WooCommerce')): ?>
                <h4><?php _e("This is woo price, and I want to set range from min price to max price from database", 'meta-data-filter') ?></h4>
                <?php $woo_price_auto = (isset($itemdata['woo_price_auto']) ? $itemdata['woo_price_auto'] : 0) ?>
                <select name="html_item[<?php echo $uniqid ?>][woo_price_auto]">
                    <option value="0" <?php if ($woo_price_auto == 0): ?>selected=""<?php endif; ?>><?php _e("No", 'meta-data-filter') ?></option>
                    <option value="1" <?php if ($woo_price_auto == 1): ?>selected=""<?php endif; ?>><?php _e("Yes", 'meta-data-filter') ?></option>
                </select><br />
            <?php endif; ?>

            <div style="display: none;">
                <h4><?php _e("Is multi value", 'meta-data-filter') ?></h4>
                <?php $slider_multi_value = (isset($itemdata['slider_multi_value']) ? (int) $itemdata['slider_multi_value'] : 0) ?>
                <select name="html_item[<?php echo $uniqid ?>][slider_multi_value]">
                    <option value="0" <?php if ($slider_multi_value == 0): ?>selected=""<?php endif; ?>><?php _e("No", 'meta-data-filter') ?></option>
                    <option value="1" <?php if ($slider_multi_value == 1): ?>selected=""<?php endif; ?>><?php _e("Yes", 'meta-data-filter') ?></option>
                </select><br />
                <i><?php _e("ATTENTION: Do not use it if you do not need!! It takes more resources. Use this mode when you have not a lot of items to filter (about 100)", 'meta-data-filter') ?></i><br />
                <i><?php _e("Enabling this mode allows you to set more than 1 value in item for range-slider. For example: 34,45,96 - through comma.", 'meta-data-filter') ?></i>
            </div>
        </div>
        <div class="data_group_item_template_checkbox data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'checkbox' ? "block" : "none") : "none") ?>;">
            <input type="hidden" value="<?php echo(isset($itemdata) ? $itemdata['checkbox'] : 0) ?>" name="html_item[<?php echo $uniqid ?>][checkbox]" />
            <input style="display: none" type="checkbox" <?php echo(isset($itemdata) ? ($itemdata['checkbox'] ? "checked" : "") : "") ?> class="mdf_option_checkbox" />
        </div>
        <div class="data_group_item_template_textinput data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'textinput' ? "block" : "none") : "none") ?>;">
            <input type="text" placeholder="<?php _e("enter placeholder text", 'meta-data-filter') ?>" value="<?php echo(isset($itemdata['textinput']) ? $itemdata['textinput'] : '') ?>" name="html_item[<?php echo $uniqid ?>][textinput]" />&nbsp;
            <?php $target = (isset($itemdata['textinput_target']) ? $itemdata['textinput_target'] : 'self'); ?>
            <select name="html_item[<?php echo $uniqid ?>][textinput_target]" class="mdf_textinput_tag_selector">
                <option <?php selected('self', $target) ?> value="self"><?php _e("self", 'meta-data-filter') ?></option>
                <option <?php selected('title', $target) ?> value="title"><?php _e("post title", 'meta-data-filter') ?></option>
                <option <?php selected('content', $target) ?> value="content"><?php _e("post content", 'meta-data-filter') ?></option>
                <option <?php selected('title_or_content', $target) ?> value="title_or_content"><?php _e("title or content on the same time", 'meta-data-filter') ?></option>
                <option <?php selected('title_and_content', $target) ?> value="title_and_content"><?php _e("title and content on the same time", 'meta-data-filter') ?></option>
                <!-- <option <?php selected('tag', $target) ?> value="tag"><?php _e("tag", 'meta-data-filter') ?></option> -->
            </select>&nbsp;


            <!-- <input type="text" style="width: 150px;<?php if ($target != 'tag'): ?>display: none;<?php endif; ?>" class="mdf_textinput_tag" placeholder="<?php _e("enter tag", 'meta-data-filter') ?>" value="<?php echo(isset($itemdata['textinput_tag']) ? $itemdata['textinput_tag'] : 'product_tag') ?>" name="html_item[<?php echo $uniqid ?>][textinput_tag]" />&nbsp; -->


            <?php $textinput_mode = (isset($itemdata['textinput_mode']) ? $itemdata['textinput_mode'] : 'like'); ?>
            <select name="html_item[<?php echo $uniqid ?>][textinput_mode]">
                <option <?php selected('like', $textinput_mode) ?> value="like"><?php _e("like", 'meta-data-filter') ?></option>
                <option <?php selected('exact', $textinput_mode) ?> value="exact"><?php _e("exact", 'meta-data-filter') ?></option>
            </select>&nbsp;
            <?php $textinput_inback_display_as = (isset($itemdata['textinput_inback_display_as']) ? $itemdata['textinput_inback_display_as'] : 'textinput'); ?>
            <select name="html_item[<?php echo $uniqid ?>][textinput_inback_display_as]" class="mdf_textinput_display_as_selector">
                <option <?php selected('textinput', $textinput_inback_display_as) ?> value="textinput"><?php _e("show as textinput in backend", 'meta-data-filter') ?></option>
                <option <?php selected('textarea', $textinput_inback_display_as) ?> value="textarea"><?php _e("show as textarea in backend", 'meta-data-filter') ?></option>
            </select>
        </div>
        <div class="data_group_item_template_calendar data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'calendar' ? "block" : "none") : "none") ?>;">

            <?php _e("jQuery-ui calendar: from - to", 'meta-data-filter'); ?>

        </div>
        <div class="data_group_item_template_select data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'select' ? "block" : "none") : "none") ?>;">
            <h4><?php _e("Drop-down size", 'meta-data-filter') ?>:</h4>
            <input type="text" placeholder="<?php _e("enter a digit from 1 to ...", 'meta-data-filter') ?>" value="<?php echo(isset($itemdata['select_size']) ? $itemdata['select_size'] : 1) ?>" name="html_item[<?php echo $uniqid ?>][select_size]" style="width: 45%;" /><br />
            <h4><?php _e("Drop-down first option title", 'meta-data-filter') ?>:</h4>
            <input type="text" placeholder="<?php _e("Any", 'meta-data-filter') ?>" value="<?php echo(isset($itemdata['select_option_title']) ? $itemdata['select_option_title'] : '') ?>" name="html_item[<?php echo $uniqid ?>][select_option_title]" style="width: 45%;" /><br />
            <i><?php _e("Leave this field empty to use defined in widget string: Any", 'meta-data-filter') ?></i><br />

            <br />
            <a href="#" class="add_option_to_data_item_select button" data-append="0" data-group-index="" data-group-item-index="<?php echo $uniqid ?>"><?php _e("Prepend drop-down option", 'meta-data-filter') ?></a><br />
            <br /><br />
            <ul class="data_item_select_options">

                <?php if (isset($itemdata)): ?>

                    <?php if (!empty($itemdata['select'])): ?>
                        <?php foreach ($itemdata['select'] as $k => $value) : ?>
                            <li><input type="text" name="html_item[<?php echo $uniqid ?>][select][]" value="<?php echo $value ?>">&nbsp;<input type="text" placeholder="<?php _e("key for the current option - must be unique", 'meta-data-filter') ?>" name="html_item[<?php echo $uniqid ?>][select_key][]" value="<?php echo((isset($itemdata['select_key'][$k]) AND ! empty($itemdata['select_key'][$k])) ? sanitize_title($itemdata['select_key'][$k]) : sanitize_title($value)) ?>" style="width: 25%;">&nbsp;<a class="delete_option_from_data_item_select remove-button" href="#"></a><br></li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php endif; ?>

            </ul>
            <br />
            <a href="#" class="add_option_to_data_item_select button" data-append="1" data-group-index="" data-group-item-index="<?php echo $uniqid ?>"><?php _e("Append drop-down option", 'meta-data-filter') ?></a><br />
            <br />
            <h4><?php _e("Search inside range.", 'meta-data-filter') ?></h4>
            <?php $select_range_value = (isset($itemdata['select_range_value']) ? (int) $itemdata['select_range_value'] : 0) ?>
            <select name="html_item[<?php echo $uniqid ?>][select_range_value]">
                <option value="0" <?php if ($select_range_value == 0): ?>selected=""<?php endif; ?>><?php _e("No", 'meta-data-filter') ?></option>
                <option value="1" <?php if ($select_range_value == 1): ?>selected=""<?php endif; ?>><?php _e("Yes", 'meta-data-filter') ?></option>
            </select><br />
            <i><?php _e("Enabling this mode allows you to set 1 decimal value in post for looking it in range. Options of such drop-down must have names as: 0-500, 501-1000, 1001-2000 and etc...", 'meta-data-filter') ?></i>



        </div>


        <div class="data_group_item_template_taxonomy data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'taxonomy' ? "block" : "none") : "none") ?>">
            <i><?php _e("By logic here must be taxonomies constructor. But, to add more flexibility to the plugin,  taxonomies are selected in the widget. So you can use the same group for different post types, or the same post type but different widgets. If there no taxonomies selected in the widget - nothing will appear on front of site in search form of the widget.", 'meta-data-filter') ?></i>
        </div>


        <div class="mdf_item_footer" <?php if (isset($itemdata) AND ( $itemdata['type'] == 'taxonomy')): ?>style="display: none;"<?php endif; ?>>

            <h4><?php _e("Item Description", 'meta-data-filter') ?>:</h4>

            <textarea name="html_item[<?php echo $uniqid ?>][description]"><?php echo(isset($itemdata) ? $itemdata['description'] : "") ?></textarea><br />


            <h4><?php _e("Item meta key", 'meta-data-filter') ?>:</h4>
            <input type="text" placeholder="medafi_total_sales" value="<?php echo $uniqid ?>" name="html_item[<?php echo $uniqid ?>][meta_key]" style="width: 45%;" />&nbsp;<a href="#" class="button mdf_change_meta_key_butt" data-old-key="<?php echo $uniqid ?>"><?php _e("change meta key", 'meta-data-filter') ?></a><br />
            <i><b style="color: red;"><?php _e("Attention", 'meta-data-filter') ?></b>: <?php _e("meta key must be begin from <b>medafi_</b> prefix, another way you will have problems! If you are using this key somewhere in code be ready to change it there!<br /> Change keys please for saved filter and filters items only, not for new and just created!", 'meta-data-filter') ?></i><br />
            <br />


            <div class="mdf_item_footer_reflection" <?php if (isset($itemdata) AND ( $itemdata['type'] == 'map' OR $itemdata['type'] == 'calendar')): ?>style="display: none;"<?php endif; ?>>

                <h4><?php _e("Reflect value from meta key", 'meta-data-filter') ?>:</h4>
                <?php
                $is_reflected = isset($itemdata['is_reflected']) ? $itemdata['is_reflected'] : 0;
                $reflected_key = isset($itemdata['reflected_key']) ? $itemdata['reflected_key'] : '';
                ?>
                <input type="hidden" name="html_item[<?php echo $uniqid ?>][is_reflected]" value="<?php echo $is_reflected ?>" />
                <input type="checkbox" class="mdf_is_reflected" <?php if ($is_reflected): ?>checked<?php endif; ?> />&nbsp;<input type="text" <?php if (!$is_reflected): ?>disabled<?php endif; ?> placeholder="<?php if (!$is_reflected): ?>disabled<?php else: ?>enabled<?php endif; ?>" value="<?php echo trim(esc_attr($reflected_key)) ?>" name="html_item[<?php echo $uniqid ?>][reflected_key]" style="width: 45%;" /><br />
                <i><?php _e("Example: <b>_price</b>, where <i>_price</i> is reflected meta field key in products of woocommerce.<br /> Reflection synchronizes data with already existing meta keys and makes them searchable!", 'meta-data-filter') ?></i>

            </div>
        </div>

        <div class="data_group_item_template_map data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'map' ? "block" : "none") : "none") ?>;">

            <?php _e("Set map info in each post you are going to filter. The data will be applyed on the google map.", 'meta-data-filter'); ?>

        </div>


    </div>

</div>
