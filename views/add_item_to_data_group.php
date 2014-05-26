<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
if(!isset($uniqid)) {
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

<h4><?php _e("Item Type", 'meta-data-filter') ?>:&nbsp;</h4>

<p>
    <label class="sel">
        <select name="html_item[<?php echo $uniqid ?>][type]" class="data_group_item_select">
            <?php foreach(self::$items_types as $key=> $name) : ?>
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
        <input type="hidden" value="1" name="html_item[<?php echo $uniqid ?>][slider_step]" /><br />
        <br />
    </div>
    <div class="data_group_item_template_checkbox data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'checkbox' ? "block" : "none") : "none") ?>;">
        <input type="hidden" value="<?php echo(isset($itemdata) ? $itemdata['checkbox'] : 0) ?>" name="html_item[<?php echo $uniqid ?>][checkbox]" />
        <input style="display: none" type="checkbox" <?php echo(isset($itemdata) ? ($itemdata['checkbox'] ? "checked" : "") : "") ?> class="mdf_option_checkbox" />
    </div>
    <div class="data_group_item_template_select data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'select' ? "block" : "none") : "none") ?>;">
        <input type="hidden" value="1" name="html_item[<?php echo $uniqid ?>][select_size]" /><br />
        <a href="#" class="add_option_to_data_item_select button" data-append="0" data-group-index="" data-group-item-index="<?php echo $uniqid ?>"><?php _e("Prepend drop-down option", 'meta-data-filter') ?></a><br />
        <br />
        <ul class="data_item_select_options">

            <?php if(isset($itemdata)): ?>

                <?php if(!empty($itemdata['select'])): ?>
                    <?php foreach($itemdata['select'] as $value) : ?>
                        <li><input type="text" name="html_item[<?php echo $uniqid ?>][select][]" value="<?php echo $value ?>">&nbsp;<a class="delete_option_from_data_item_select remove-button" href="#"></a><br></li>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endif; ?>

        </ul>
        <br />
        <a href="#" class="add_option_to_data_item_select button" data-append="1" data-group-index="" data-group-item-index="<?php echo $uniqid ?>"><?php _e("Append drop-down option", 'meta-data-filter') ?></a><br />

    </div>


    <div class="data_group_item_template_taxonomy data_group_input_type" style="display: <?php echo(isset($itemdata) ? ($itemdata['type'] == 'taxonomy' ? "block" : "none") : "none") ?>">
        <i><?php _e("By logic here must be taxonomies constructor. But, to add more flexibility to the plugin,  taxonomies are selected in the widget. So you can use the same group for different post types, or the same post type but different widgets. If there no taxonomies selected in the widget - nothing will appear on front of site in search form of the widget.", 'meta-data-filter') ?></i>
    </div>

    <input type="hidden" name="html_item[<?php echo $uniqid ?>][description]" value="" />

    <?php
    $is_reflected = 0;
    $reflected_key = '';
    ?>
    <input type="hidden" name="html_item[<?php echo $uniqid ?>][is_reflected]" value="<?php echo $is_reflected ?>" />
</div>

