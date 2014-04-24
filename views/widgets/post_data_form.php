<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<p>
    <label for="<?php echo $widget->get_field_id('title'); ?>"><?php _e('Title', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id('title'); ?>" name="<?php echo $widget->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
</p>

<p>
    <label for="<?php echo $widget->get_field_id('meta_data_filter_slug'); ?>"><?php _e('Slug', 'meta-data-filter') ?>:</label>
    <?php
    $settings = self::get_settings();
    if (isset($settings['post_types'])) {
        if (!empty($settings['post_types'])) {
            ?>
            <select class="widefat" id="<?php echo $widget->get_field_id('meta_data_filter_slug') ?>" name="<?php echo $widget->get_field_name('meta_data_filter_slug') ?>">
                <?php foreach ($settings['post_types'] as $post_name) : ?>
                    <option <?php if ($instance['meta_data_filter_slug'] == $post_name) echo 'selected="selected"'; ?> value="<?php echo $post_name ?>" class="level-0"><?php echo $post_name ?></option>
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
    if ($instance['show_absent_items'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('show_absent_items'); ?>" name="<?php echo $widget->get_field_name('show_absent_items'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('show_absent_items'); ?>"><?php _e('Show absent items', 'meta-data-filter') ?>:</label>
</p>
