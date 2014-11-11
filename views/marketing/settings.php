<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="wrap">
    <h2><?php _e("MDTF Marketing Links Settings", 'meta-data-filter') ?></h2>

    <?php if(!empty($_POST)): ?>
        <div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php _e("Settings are saved.", 'meta-data-filter') ?></strong></p></div>
    <?php endif; ?>

    <form action="<?php echo admin_url('edit.php?post_type=' . MetaDataFilterCore::$slug_links . '&page=mdf_marketing_settings') ?>" method="post">
        <input type="hidden" name="mdf_marketing_settings" value="" />
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Links prefixes", 'meta-data-filter') ?></label></th>
                    <td>
                        <a href="#" class="button" id="mdf_add_markering_link_prefix"><?php _e("Add link prefix", 'meta-data-filter') ?></a><br />
                        <ul id="mdf_add_markering_links_list">
                            <?php if(!empty($settings['link_prefix']) AND is_array($settings['link_prefix'])): ?>
                                <?php foreach($settings['link_prefix'] as $link_prefix) : ?>
                                    <li>
                                        <input type="text" class="regular-text" value="<?php echo $link_prefix ?>" name="mdf_marketing_settings[link_prefix][]">&nbsp;<a href="#" class="button mdf_del_markering_link_prefix">X</a><br />
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul> 
                        <p class="description"><?php _e("Example: http://site.net/<b>mdf</b>/192/man-jackets - <b>mdf</b> is prefix. You can create unlimited count of them. But be careful with such words as <b>category</b> or something from wordpress!", 'meta-data-filter') ?></p>
                        <p class="description"><?php _e("If no one prefix in settings, you can use default prefix <b>mdf</b> only.", 'meta-data-filter') ?></p>
                    </td>
                </tr>


            </tbody>
        </table>


        <p class="submit"><input type="submit" value="<?php _e("Save Changes", 'meta-data-filter') ?>" class="button button-primary" name="meta_data_filter_settings_submit"></p>
    </form>
    <div style="display: none;">
        <div id="mdf_prefix_input_tpl">
            <li><input type="text" class="regular-text" value="" name="mdf_marketing_settings[link_prefix][]">&nbsp;<a href="#" class="button mdf_del_markering_link_prefix">X</a><br /></li>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(function() {
            jQuery("#mdf_add_markering_links_list").sortable();

            jQuery('#mdf_add_markering_link_prefix').click(function() {
                jQuery('#mdf_add_markering_links_list').append(jQuery('#mdf_prefix_input_tpl').html());
                return false;
            });
            jQuery('.mdf_del_markering_link_prefix').life('click',function() {
                jQuery(this).parents('li').hide(220, function() {
                    jQuery(this).remove();
                });
                return false;
            });
        });

    </script>
</div>

