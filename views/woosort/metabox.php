<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="wrap">

    <input type="hidden" name="mdf_woo_search_values" value="" />
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><label><?php _e("Show results taxonomies navigation", 'meta-data-filter') ?></label><br /><i>Disabled in free version</i><br /></th>
                <td>
                    <fieldset>
                        <label>
                            <input disabled="" type="checkbox" value="0" name="show_results_tax_navigation" />
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
                        <?php if(!empty($settings) AND is_array($settings)): ?>
                            <?php $counter=0; ?>
                            <?php foreach($settings as $value) : ?>
                                <?php
                                if($counter>=3){
                                    break;
                                }
                                ?>
                                <li>
                                    <input type="text" class="regular-text" value="<?php echo $value ?>" name="mdf_woo_search_values[]">&nbsp;<a href="#" class="button mdf_del_woo_search_value">X</a><br />
                                </li>
                            <?php $counter++;endforeach; ?>
                        <?php endif; ?>
                    </ul> 
                    <p class="description"><?php _e("Special words: date,title. Example: title^Title", 'meta-data-filter') ?></p>
                    <i>(3 items max in free version)</i><br />
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
        jQuery(function() {
            jQuery("#mdf_woo_search_values").sortable();

            jQuery('#mdf_add_woo_search_value').click(function() {
                var html = jQuery('#mdf_woo_search_values_input_tpl').html();
                html = html.replace(/__NAME__/gi, 'mdf_woo_search_values');
                jQuery('#mdf_woo_search_values').append(html);
                return false;
            });
            jQuery('.mdf_del_woo_search_value').life('click', function() {
                jQuery(this).parents('li').hide(220, function() {
                    jQuery(this).remove();
                });
                return false;
            });
        });

    </script>
</div>

