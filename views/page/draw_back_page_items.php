<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php if (!empty($filter['items'])): ?>

    <h2 class="section-title" style="border-bottom: solid 1px #aaa">
        <?php echo $filter['name'] ?>
        <div style="float: right; position: relative;">
            <a href="#" class="button mdf-section-title-button closed">+</a>
        </div>
    </h2>

    <?php $backend_section_max_height = (int) get_post_meta($filter_post_id, 'backend_section_max_height', true); ?>
    <div style="display:none; <?php if ($backend_section_max_height > 0): ?>max-height: <?php echo $backend_section_max_height ?>px; overflow: auto;<?php endif; ?>">
        <table class="form-table">
            <tbody>
                <?php foreach ($filter['items'] as $key => $item) : $uid = uniqid(); ?>
                    <?php
                    $is_reflected = isset($item['is_reflected']) ? $item['is_reflected'] : 0;
                    $reflected_key = isset($item['reflected_key']) ? $item['reflected_key'] : '';
                    ?>
                    <?php if ($item['type'] == 'slider'): ?>

                        <tr valign="top">
                            <th scope="row"><?php _e($item['name']); ?>

                                <?php
                                if (!isset($item['slider_range_value']))
                                {
                                    $item['slider_range_value'] = 0;
                                }
                                //***
                                if ($item['slider_range_value'] == 1)
                                {
                                    ?>
                                    <br /><i style="font-size:11px;">(<?php _e("from/to", 'meta-data-filter') ?>)</i>
                                    <?php
                                }
                                ?>

                                <?php
                                if ($is_reflected)
                                {
                                    printf('<br /><i style="font-size:11px;">' . __('is reflected by %s', 'meta_data_filter') . '</i>', $reflected_key);
                                } else
                                {
                                    echo '<br /><i style="font-size:11px;">' . $key . '</i>';
                                }

                                //***
                                ?>
                            </th>
                            <td>


                                <?php
                                if (!isset($item['slider_range_value']))
                                {
                                    $item['slider_range_value'] = 0;
                                }
                                //***
                                if ($item['slider_range_value'] == 1):
                                    ?>
                                    <!-- double value slider slider -->

                                    <fieldset>
                                        <legend class="screen-reader-text"><span><?php _e($item['name']) ?></span></legend>
                                        <label for="<?php echo $key ?>">
                                            <?php list($min, $max) = explode('^', $item['slider']); ?>
                                            <?php
                                            $key_from = $key;
                                            $key_to = $key . '_to';
                                            $slider_value_from = floatval(get_post_meta($post_id, $key_from, true));
                                            $slider_value_to = floatval(get_post_meta($post_id, $key_to, true));
                                            ?>
                                            <input type="text" placeholder="<?php _e("from", 'meta-data-filter') ?>" id="<?php echo $key_from ?>" name="page_meta_data_filter[<?php echo $key_from ?>]" value="<?php echo $slider_value_from ?>" />
                                            &nbsp;<input type="text" placeholder="<?php _e("to", 'meta-data-filter') ?>" id="<?php echo $key_to ?>" name="page_meta_data_filter[<?php echo $key_to ?>]" value="<?php echo $slider_value_to ?>" />

                                            <br /><i><?php echo(__('min', 'meta-data-filter') . ' :' . $min . ', ' . __('max', 'meta-data-filter') . ': ' . $max) ?>. <?php _e($item['description']) ?></i>
                                        </label>
                                    </fieldset>

                                        <?php else: ?>

                                    <!-- usual 1 value slider -->
                                    <fieldset>
                                        <legend class="screen-reader-text"><span><?php _e($item['name']) ?></span></legend>
                                        <label for="<?php echo $key ?>">
                                            <?php list($min, $max) = explode('^', $item['slider']); ?>
                                            <?php if (!$is_reflected): ?>
                                                <?php
                                                $slider_value = get_post_meta($post_id, $key, true);
                                                if (empty($slider_value))
                                                {
                                                    $slider_value = 0;
                                                }
                                                ?>
                                                <input type="text"  placeholder="<?php _e("example", 'meta-data-filter') ?>:<?php echo($min + 1) ?>" id="<?php echo $key ?>" name="page_meta_data_filter[<?php echo $key ?>]" value="<?php echo $slider_value ?>" />
                                            <?php else: ?>
                                                <?php
                                                if (!empty($reflected_key))
                                                {
                                                    echo get_post_meta($post_id, $reflected_key, TRUE);
                                                } else
                                                {
                                                    _e('key is empty, please check filter data!', 'meta-data-filter');
                                                }
                                                ?>
                <?php endif; ?>
                                            <br /><i><?php echo(__('min', 'meta-data-filter') . ' :' . $min . ', ' . __('max', 'meta-data-filter') . ': ' . $max) ?>. <?php _e($item['description']) ?></i>
                                        </label>
                                    </fieldset>


            <?php endif; ?>



                            </td>
                        </tr>

                            <?php endif; ?>


                            <?php if ($item['type'] == 'checkbox'): ?>

                        <tr valign="top">
                            <th scope="row"><?php _e($item['name']) ?>
            <?php
            if ($is_reflected)
            {
                printf('<br /><i style="font-size:11px;">' . __('is reflected by %s', 'meta_data_filter') . '</i>', $reflected_key);
            } else
            {
                echo '<br /><i style="font-size:11px;">' . $key . '</i>';
            }
            ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><span><?php _e($item['name']); ?></span></legend>
                                    <?php if (!$is_reflected): ?>
                                        <?php $is_checked = isset($page_meta_data_filter[$key]) ? (int) $page_meta_data_filter[$key] : 0 ?>
                                        <label for="<?php echo $key ?>">
                                            <input type="hidden" name="page_meta_data_filter[<?php echo $key ?>]" value="<?php echo $is_checked ?>">
                                            <input type="checkbox" class="mdf_option_checkbox" id="<?php echo $key ?>" <?php if ($is_checked): ?>checked<?php endif; ?> />
                                            <i><?php _e($item['description']) ?></i>
                                        </label>
                                    <?php else: ?>
                                        <?php
                                        if (!empty($reflected_key))
                                        {
                                            $val = get_post_meta($post_id, $reflected_key, TRUE);
                                            ?>
                                            <input disabled type="checkbox" <?php if ($val): ?>checked<?php endif; ?> />
                    <?php
                } else
                {
                    _e('key is empty, please check filter data!', 'meta-data-filter');
                }
                ?>
                                <?php endif; ?>
                                </fieldset>
                            </td>
                        </tr>

                            <?php endif; ?>


        <?php if ($item['type'] == 'textinput'): ?>

                        <tr valign="top">
                            <th scope="row"><?php _e($item['name']) ?>
                                    <?php
                                    if ($is_reflected)
                                    {
                                        printf('<br /><i style="font-size:11px;">' . __('is reflected by %s', 'meta_data_filter') . '</i>', $reflected_key);
                                    } else
                                    {
                                        echo '<br /><i style="font-size:11px;">' . $key . '</i>';
                                    }
                                    ?>
                            </th>
                            <td>
                                <fieldset>
                                    <?php
                                    //$text = isset($page_meta_data_filter[$key]) ? $page_meta_data_filter[$key] : '';
                                    $text = get_post_meta($post_id, $key, TRUE);
                                    $target = 'self';
                                    $textinput_inback_display_as = 'textinput';
                                    if (isset($item['textinput_inback_display_as']))
                                    {
                                        $textinput_inback_display_as = $item['textinput_inback_display_as'];
                                    }
                                    if (isset($item['textinput_target']) AND ! empty($item['textinput_target']))
                                    {
                                        $target = $item['textinput_target'];
                                    }
                                    ?>
                                    <legend class="screen-reader-text"><span><?php _e($item['name']); ?></span></legend>
                                    <?php if (!$is_reflected): ?>

                                        <?php if ($target == 'self'): ?>
                                            <?php if ($textinput_inback_display_as == 'textinput'): ?>
                                                <input type="text" class="widefat" placeholder="<?php echo $item['textinput'] ?>" name="page_meta_data_filter[<?php echo $key ?>]" value="<?php echo $text ?>" /><br />
                                            <?php endif; ?>

                                            <?php if ($textinput_inback_display_as == 'textarea'): ?>
                                                <textarea class="textarea widefat" name="page_meta_data_filter[<?php echo $key ?>]"><?php echo $text ?></textarea><br />
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if ($target == 'title'): ?>
                                            <?php if ($textinput_inback_display_as == 'textinput'): ?>
                                                <input type="text" readonly="" class="widefat" name="page_meta_data_filter[<?php echo $key ?>]" value="<?php echo the_title() ?>" /><br />
                                            <?php endif; ?>

                                            <?php if ($textinput_inback_display_as == 'textarea'): ?>
                                                <textarea class="textarea widefat" name="page_meta_data_filter[<?php echo $key ?>]"><?php echo the_title() ?></textarea><br />
                                            <?php endif; ?>
                <?php endif; ?>

                <?php if ($target == 'title'): ?> <i>(<small><?php _e("title", 'meta-data-filter'); ?></small>)</i>&nbsp;<?php endif; ?>
                                        <i><?php _e($item['description']) ?></i>
                        <?php else: ?>
                <?php
                if (!empty($reflected_key))
                {
                    $val = get_post_meta($post_id, $reflected_key, TRUE);
                    ?>
                                            <input type="text" class="wide" value="<?php echo $val ?>" disabled="" />
                                        <?php
                                    } else
                                    {
                                        _e('key is empty, please check filter data!', 'meta-data-filter');
                                    }
                                    ?>
                                <?php endif; ?>
                                </fieldset>
                            </td>
                        </tr>

                                <?php endif; ?>


                                <?php if ($item['type'] == 'calendar'): ?>

                        <tr valign="top">
                            <th scope="row"><?php _e($item['name']) ?>
            <?php
            if ($is_reflected)
            {
                printf('<br /><i style="font-size:11px;">' . __('is reflected by %s', 'meta_data_filter') . '</i>', $reflected_key);
            } else
            {
                echo '<br /><i style="font-size:11px;">' . $key . '</i>';
            }
            ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><span><?php _e($item['name']); ?></span></legend>
                                    <?php if (!$is_reflected): ?>
                                        <?php
                                        $from = isset($page_meta_data_filter[$key . '_from']) ? $page_meta_data_filter[$key . '_from'] : 0;
                                        $to = isset($page_meta_data_filter[$key . '_to']) ? $page_meta_data_filter[$key . '_to'] : 0;
                                        ?>
                                        <input type="hidden" name="page_meta_data_filter[<?php echo $key ?>_from]" value="<?php echo $from ?>" />
                                        <input type="text" readonly="readonly" class="text_input mdf_calendar mdf_calendar_from" placeholder="<?php _e('from', 'meta-data-filter') ?>" />
                                        &nbsp;-&nbsp;
                                        <input type="hidden" name="page_meta_data_filter[<?php echo $key ?>_to]" value="<?php echo $to ?>" />
                                        <input type="text" readonly="readonly" class="text_input mdf_calendar mdf_calendar_to" placeholder="<?php _e('to', 'meta-data-filter') ?>" /><br />
                                        <i><?php _e($item['description']) ?></i>
                        <?php else: ?>
                <?php
                if (!empty($reflected_key))
                {
                    $val = get_post_meta($post_id, $reflected_key, TRUE);
                    ?>
                                            <input type="text" class="text_input" value="<?php echo date(get_option('date_format_custom'), $val) ?>" disabled="" />
                                            <?php
                                        } else
                                        {
                                            _e('key is empty, please check filter data!', 'meta-data-filter');
                                        }
                                        ?>
                                    <?php endif; ?>
                                </fieldset>
                            </td>
                        </tr>

        <?php endif; ?>


                            <?php if ($item['type'] == 'select'): ?>
                                <?php if (!empty($item['select'])): ?>
                            <tr>
                                <th scope="row"><label for="<?php echo $key ?>"><?php _e($item['name']) ?>
                                    <?php
                                    if (!isset($item['select_range_value']))
                                    {
                                        $item['select_range_value'] = 0;
                                    }
                                    if ($item['select_range_value'] == 1)
                                    {
                                        echo '<br /><i style="font-size:11px;">is range</i>';
                                    }
                                    //***
                                    if ($is_reflected)
                                    {
                                        printf('<br /><i style="font-size:11px;">' . __('is reflected by %s', 'meta_data_filter') . '</i>', $reflected_key);
                                    } else
                                    {
                                        echo '<br /><i style="font-size:11px;">' . $key . '</i>';
                                    }
                                    ?>
                                    </label></th>
                                <td>

                                    <?php if (!$is_reflected): ?>
                                        <?php if ($item['select_range_value'] == 1): ?>
                                            <?php $val = floatval(get_post_meta($post_id, $key, TRUE)); ?>
                                            <input type="text" class="widefat" placeholder="<?php _e('Enter value. Empty means zero.', 'meta-data-filter') ?>" name="page_meta_data_filter[<?php echo $key ?>]" value="<?php echo $val ?>" /><br />
                                        <?php else: ?>

                                            <?php $selected = isset($page_meta_data_filter[$key]) ? $page_meta_data_filter[$key] : NULL ?>
                                            <select name="page_meta_data_filter[<?php echo $key ?>]" id="<?php echo $key ?>">
                                                <option value="~"><?php _e('none', 'meta-data-filter') ?></option>
                        <?php foreach ($item['select'] as $k => $value) : ?>
                            <?php
                            $option_value = sanitize_title($value);
                            if (isset($item['select_key'][$k]) AND ! empty($item['select_key'][$k]))
                            {
                                $option_value = sanitize_title($item['select_key'][$k]);
                            } else
                            {
                                $option_value = sanitize_title($value);
                            }
                            ?>
                                                    <option value="<?php echo $option_value; ?>" <?php echo($selected == $option_value ? 'selected' : '') ?>><?php _e($value) ?></option>
                                    <?php endforeach; ?>
                                            </select>

                    <?php endif; ?>



                <?php else: ?>
                                                        <?php
                                                        if (!empty($reflected_key))
                                                        {
                                                            echo get_post_meta($post_id, $reflected_key, TRUE);
                                                        } else
                                                        {
                                                            _e('key is empty, please check filter data!', 'meta-data-filter');
                                                        }
                                                        ?>
                                                    <?php endif; ?>





                                    <i><?php _e($item['description']) ?></i>
                                </td>
                            </tr>
                                                <?php endif; ?>
                                            <?php endif; ?>


        <?php if ($item['type'] == 'map'): ?>

                        <tr valign="top">
                            <th scope="row"><?php _e($item['name']) ?></th>
                            <td style="padding:0 !important;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 49%;">
                                            <fieldset>
            <?php
            $latitude = get_post_meta($post_id, $key . '_latitude', TRUE);
            ?>
                                                <input type="text" class="widefat" placeholder="<?php _e('enter latitude', 'meta-data-filter'); ?>" name="page_meta_data_filter[<?php echo $key ?>_latitude]" value="<?php echo $latitude ?>" /><br />
                                            </fieldset>
                                        </td>
                                        <td style="width: 49%;">
                                            <fieldset>
            <?php
            $longitude = get_post_meta($post_id, $key . '_longitude', TRUE);
            ?>
                                                <input type="text" class="widefat" placeholder="<?php _e('enter longitude', 'meta-data-filter'); ?>" name="page_meta_data_filter[<?php echo $key ?>_longitude]" value="<?php echo $longitude ?>" /><br />
                                            </fieldset>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <fieldset>
                        <?php
                        $desc = get_post_meta($post_id, $key . '_desc', TRUE);
                        ?>
                                                <input type="text" class="widefat" placeholder="<?php _e('enter pin description', 'meta-data-filter'); ?>" name="page_meta_data_filter[<?php echo $key ?>_desc]" value="<?php echo $desc ?>" /><br />
                                            </fieldset>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

        <?php endif; ?>


        <?php if ($item['type'] == 'taxonomy'): ?>

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
    </div>

<?php endif; ?>

