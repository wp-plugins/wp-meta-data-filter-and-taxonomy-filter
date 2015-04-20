<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="wrap">

    <input type="hidden" name="mdf_woo_search_values" value="" />
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><label><?php _e("Panel type", 'meta-data-filter') ?></label></th>
                <td>
                    <fieldset>
                        <label>
                            <?php
                            $panel_types = array(
                                'select' => __("Drop-down", 'meta-data-filter'),
                                'buttons' => __("Buttons", 'meta-data-filter')
                            );
                            ?>
                            <select name="panel_type">
                                <?php foreach ($panel_types as $key => $value) : ?>
                                    <option value="<?php echo $key; ?>" <?php if ($panel_type == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </fieldset>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label><?php _e("Show results taxonomies navigation", 'meta-data-filter') ?></label></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" <?php checked(1, $show_results_tax_navigation) ?> value="1" name="show_results_tax_navigation" />
                        </label>
                    </fieldset>
                    <p class="description">
                        <img src="<?php echo MetaDataFilter::get_application_uri() ?>images/show_results_tax_navigation.png" alt="<?php _e("Show results taxonomies navigation", 'meta-data-filter') ?>" /><br />
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label><?php _e("Sort panel meta keys", 'meta-data-filter') ?></label></th>
                <td>
                    <a href="#" class="button" id="mdf_add_woo_search_value"><?php _e("Add meta key", 'meta-data-filter') ?></a><br />
                    <ul id="mdf_woo_search_values">
                        <?php if (!empty($settings) AND is_array($settings)): ?>
                            <?php foreach ($settings as $value) : ?>
                                <li>
                                    <input type="text" class="regular-text" value="<?php echo $value ?>" name="mdf_woo_search_values[]">&nbsp;<a href="#" class="button mdf_del_woo_search_value">X</a><br />
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul> 
                    <p class="description"><?php printf(__("Special words: %s. Example: title^Title", 'meta-data-filter'), implode(',', MetaDataFilterCore::$allowed_order_by)) ?></p>
                </td>
            </tr>

            <tr valign="top" style="display:none;">
                <th scope="row"><label><?php _e("Default order", 'meta-data-filter') ?></label></th>
                <td>
                    <fieldset>
                        <label>
                            <?php
                            $default_orders = array(
                                'DESC' => __("DESC", 'meta-data-filter'),
                                'ASC' => __("ASC", 'meta-data-filter')
                            );
                            ?>
                            <select name="default_order">
                                <?php foreach ($default_orders as $key => $value) : ?>
                                    <option value="<?php echo $key; ?>" <?php if ($default_order == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </fieldset>

                </td>
            </tr>


            <tr valign="top" style="display:none;">
                <th scope="row"><label><?php _e("Default order by", 'meta-data-filter') ?></label></th>
                <td>
                    <fieldset>
                        <label>
                            <input class="wide" type="text" name="default_order_by" value="<?php echo $default_order_by ?>" />
                        </label>
                    </fieldset>

                </td>
            </tr>

        </tbody>
    </table>



    <div class="clear"></div>
    <div style="display: none;">
        <div id="mdf_woo_search_values_input_tpl">
            <li><input type="text" class="regular-text" placeholder="<?php _e("Example: _price^Price", 'meta-data-filter') ?>" value="" name="__NAME__[]">&nbsp;<a href="#" class="button mdf_del_woo_search_value">X</a><br /></li>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(function () {
            jQuery("#mdf_woo_search_values").sortable();

            jQuery('#mdf_add_woo_search_value').click(function () {
                var html = jQuery('#mdf_woo_search_values_input_tpl').html();
                html = html.replace(/__NAME__/gi, 'mdf_woo_search_values');
                jQuery('#mdf_woo_search_values').append(html);
                return false;
            });
            jQuery('.mdf_del_woo_search_value').life('click', function () {
                jQuery(this).parents('li').hide(220, function () {
                    jQuery(this).remove();
                });
                return false;
            });
        });

    </script>
</div>

