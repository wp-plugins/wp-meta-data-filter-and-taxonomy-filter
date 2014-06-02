<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php if(!empty($filter['items'])): ?>
    <h2 class="section-title" style="border-bottom: solid 1px #aaa"><?php echo $filter['name'] ?></h2>

    <table class="form-table">
        <tbody>
            <?php foreach($filter['items'] as $key=> $item) : $uid = uniqid(); ?>
                <?php
                $is_reflected = isset($item['is_reflected']) ? $item['is_reflected'] : 0;
                $reflected_key = isset($item['reflected_key']) ? $item['reflected_key'] : '';
                ?>
                <?php if($item['type'] == 'slider'): ?>

                    <tr valign="top">
                        <th scope="row"><?php _e($item['name']); ?>
                            <?php
                            if($is_reflected) {
                                printf('<br /><i style="font-size:11px;">' . __('is reflected by %s', 'meta_data_filter') . '</i>', $reflected_key);
                            } else {
                                echo '<br /><i style="font-size:11px;">' . @$item['meta_key'] . '</i>';
                            }
                            ?>
                        </th>
                        <td>

                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e($item['name']) ?></span></legend>
                                <label for="<?php echo $key ?>">
                                    <?php list($min, $max) = explode('^', $item['slider']); ?>
                                    <?php if(!$is_reflected): ?>
                                        <input type="text"  placeholder="<?php _e("example", 'meta-data-filter') ?>:<?php echo($min + 1) ?>" id="<?php echo $key ?>" name="page_meta_data_filter[<?php echo $key ?>]" value="<?php echo $page_meta_data_filter[$key] ?>" />
                                    <?php else: ?>
                                        <?php
                                        if(!empty($reflected_key)) {
                                            echo get_post_meta($post_id, $reflected_key, TRUE);
                                        } else {
                                            _e('key is empty, please check filter data!', 'meta-data-filter');
                                        }
                                        ?>
                                    <?php endif; ?>
                                    <br /><i><?php echo(__('min', 'meta-data-filter') . ' :' . $min . ', ' . __('max', 'meta-data-filter') . ': ' . $max) ?>. <?php _e($item['description']) ?></i>                                    
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                <?php endif; ?>


                <?php if($item['type'] == 'checkbox'): ?>

                    <tr valign="top">
                        <th scope="row"><?php _e($item['name']) ?>
                            <?php
                            if($is_reflected) {
                                printf('<br /><i style="font-size:11px;">' . __('is reflected by %s', 'meta_data_filter') . '</i>', $reflected_key);
                            } else {
                                echo '<br /><i style="font-size:11px;">' . @$item['meta_key'] . '</i>';
                            }
                            ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e($item['name']); ?></span></legend>
                                <?php if(!$is_reflected): ?>
                                    <?php $is_checked = isset($page_meta_data_filter[$key]) ? (int) $page_meta_data_filter[$key] : 0 ?>
                                    <label for="<?php echo $key ?>">
                                        <input type="hidden" name="page_meta_data_filter[<?php echo $key ?>]" value="<?php echo $is_checked ?>">
                                        <input type="checkbox" class="mdf_option_checkbox" id="<?php echo $key ?>" <?php if($is_checked): ?>checked<?php endif; ?> />
                                        <i><?php _e($item['description']) ?></i>
                                    </label>
                                <?php else: ?>
                                    <?php
                                    if(!empty($reflected_key)) {
                                        $val = get_post_meta($post_id, $reflected_key, TRUE);
                                        ?>
                                        <input disabled type="checkbox" <?php if($val): ?>checked<?php endif; ?> />
                                        <?php
                                    } else {
                                        _e('key is empty, please check filter data!', 'meta-data-filter');
                                    }
                                    ?>
                                <?php endif; ?>
                            </fieldset>
                        </td>
                    </tr>

                <?php endif; ?>


                <?php if($item['type'] == 'select'): ?>
                    <?php if(!empty($item['select'])): ?>
                        <tr>
                            <th scope="row"><label for="<?php echo $key ?>"><?php _e($item['name']) ?>
                                    <?php
                                    if($is_reflected) {
                                        printf('<br /><i style="font-size:11px;">' . __('is reflected by %s', 'meta_data_filter') . '</i>', $reflected_key);
                                    } else {
                                        echo '<br /><i style="font-size:11px;">' . @$item['meta_key'] . '</i>';
                                    }
                                    ?>
                                </label></th>
                            <td>
                                <?php if(!$is_reflected): ?>
                                    <?php $selected = isset($page_meta_data_filter[$key]) ? $page_meta_data_filter[$key] : NULL ?>
                                    <select name="page_meta_data_filter[<?php echo $key ?>]" id="<?php echo $key ?>">
                                        <option value="~"><?php _e('none', 'meta-data-filter') ?></option>
                                        <?php foreach($item['select'] as $value) : ?>
                                            <option value="<?php echo sanitize_title($value); ?>" <?php echo($selected == sanitize_title($value) ? 'selected' : '') ?>><?php _e($value) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <?php
                                    if(!empty($reflected_key)) {
                                        echo get_post_meta($post_id, $reflected_key, TRUE);
                                    } else {
                                        _e('key is empty, please check filter data!', 'meta-data-filter');
                                    }
                                    ?>
                                <?php endif; ?>
                                <i><?php _e($item['description']) ?></i>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>


                <?php if($item['type'] == 'taxonomy'): ?>

                    <tr valign="top">
                        <th scope="row"><?php _e($item['name']) ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e($item['name']) ?></span></legend>
                                <label for="<?php echo $key ?>">

                                    <?php _e('Taxonomy block is placed here', 'meta-data-filter') ?>

                                </label>
                            </fieldset>
                        </td>
                    </tr>

                <?php endif; ?>

            <?php endforeach; ?>
        </tbody>
    </table>
    <?php




















 endif;

