<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php if(!empty($filter_post['items'])): ?>
    <?php if($filter_post['name']{0} !== '~'): ?>
        <h4 class="data-filter-section-title" style="margin-bottom: 9px;"><?php _e($filter_post['name']); ?></h4>
    <?php endif; ?>
    
    <table style="width: 100%;">
        <tbody>
            <?php foreach($filter_post['items'] as $key=> $item) : $uid = uniqid(); ?>

                <?php
                $is_reflected = false;
                if(isset($item['is_reflected']) AND $item['is_reflected'] == 1) {
                    $is_reflected = true;
                }
                ?>

                <?php if($item['type'] == 'slider'): ?>

                    <tr valign="top">
                        <td>
                            <div class="mdf_input_container" style="padding: 0;">
                                <label>
                                    <?php _e($item['name']); ?>:&nbsp;<?php echo MetaDataFilter::get_field($key, $post_id, $is_reflected) ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>


                <?php if($item['type'] == 'checkbox'): ?>
                    <?php
                    $is_checked = (int) MetaDataFilter::get_field($key, $post_id, $is_reflected);
                    $to_show = true;
                    if(!$show_absent_items) {
                        if(!$is_checked) {
                            $to_show = false;
                        }
                    }
                    ?>
                    <?php if($to_show): ?>
                        <tr valign="top">
                            <td>
                                <div class="mdf_input_container">
                                    <input disabled class="mdf_disabled_checkbox" type="checkbox" <?php if($is_checked): ?>checked<?php endif; ?> />
                                    <label>&nbsp;<?php _e($item['name']) ?>
                                    </label>
                                </div>
                            </td>

                        </tr>
                    <?php endif; ?>
                <?php endif; ?>


                <?php if($item['type'] == 'select'): ?>
                    <?php if(!empty($item['select'])): ?>
                        <?php
                        $selected = MetaDataFilter::get_field($key, $post_id, $is_reflected);
                        $to_show = true;
                        if(!$show_absent_items) {
                            if(!$selected) {
                                $to_show = false;
                            }
                        }
                        //***
                        $val = "~";
                        foreach($item['select'] as $value) {
                            if($selected == sanitize_title($value))
                                $val = $value;
                        }
                        ?>
                        <?php if($to_show AND ! empty($val) AND $val != '~'): ?>
                            <tr>
                                <td>
                                    <div class="mdf_input_container">
                                        <h5 style="margin-bottom: 4px;" class="data-filter-section-title"><?php _e($item['name']) ?>:</h5>
                                        <select disabled class="mdf_item_chars_select mdf_disabled_select">
                                            <option><?php echo $val; ?></option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

            <?php endforeach; ?>
        </tbody>
    </table>
    <?php





 endif;

