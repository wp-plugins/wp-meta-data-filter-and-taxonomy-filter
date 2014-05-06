<div class="wrap">
    <h2><?php _e("Meta Data Filter Settings", 'meta-data-filter') ?></h2>

    <?php if(!empty($_POST)): ?>
        <div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php _e("Settings are saved.", 'meta-data-filter') ?></strong></p></div>
    <?php endif; ?>

    <form action="<?php echo admin_url('edit.php?post_type=' . MetaDataFilterCore::$slug . '&page=mdf_settings') ?>" method="post">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Search Result Page", 'meta-data-filter') ?></label></th>
                    <td>
                        <input type="text" class="regular-text" value="<?php echo $data['search_url'] ?>" name="meta_data_filter_settings[search_url]">
                        <p class="description"><?php _e("Link to site page where shows searching results", 'meta-data-filter') ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Output searched posts template", 'meta-data-filter') ?></label></th>
                    <td>
                        <input type="text" class="regular-text" value="<?php echo $data['output_tpl'] ?>" name="meta_data_filter_settings[output_tpl]">
                        <p class="description"><?php _e("Output template, search by default. For example: search,archive,content or your custom which is in current wp theme. If you want to set double name template, write it in such manner for example: content-special. If you do not understood - leave search!", 'meta-data-filter') ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Supported post types", 'meta-data-filter') ?></label></th>
                    <td>
                        <?php
                        $all_post_types = get_post_types();
                        unset($all_post_types['nav_menu_item']);
                        unset($all_post_types['revision']);
                        unset($all_post_types['attachment']);
                        unset($all_post_types[MetaDataFilterCore::$slug]);
                        ?>

                        <?php foreach($all_post_types as $post_type=> $post_type_name) : ?>

                            <fieldset>
                                <label>
                                    <input type="checkbox" <?php if(@in_array($post_type_name, $data['post_types'])) echo 'checked'; ?> value="<?php echo $post_type_name ?>" name="meta_data_filter_settings[post_types][]" />
                                    <?php echo $post_type_name ?>
                                </label>
                            </fieldset>

                        <?php endforeach; ?>
                        <p class="description"><?php _e("Check post types which should be searched", 'meta-data-filter') ?></p>
                    </td>
                </tr>

<!-- <tr valign="top">
    <th scope="row"><label><?php _e("Ajax searching", 'meta-data-filter') ?></label></th>
    <td>
        <fieldset>
            <label>
                <input type="checkbox" <?php if(isset($data['ajax_searching'])) echo 'checked'; ?> value="1" name="meta_data_filter_settings[ajax_searching]" /> ajax
            </label>
        </fieldset>
        <p class="description"><?php _e("Check this checkbox if you want to get search results by ajax on 'Search Result Page'", 'meta-data-filter') ?></p>
    </td>
</tr> -->
                
                <tr valign="top">
                    <th scope="row"><label><?php _e("Draw post features panel automatically", 'meta-data-filter') ?></label></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" <?php if(isset($data['post_features_panel_auto'])) echo 'checked'; ?> value="1" name="meta_data_filter_settings[post_features_panel_auto]" /> auto
                            </label>
                        </fieldset>
                        <p class="description"><?php _e("Uncheck the checkbox if post featured html code under post title is incorrect.<br />Add this code under content title in your content.php: <b>&#60;?php if(class_exists('MetaDataFilterPage'))MetaDataFilterPage::draw_post_features_panel(\$post->ID); ?&#62;</b>", 'meta-data-filter') ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Item features under title out", 'meta-data-filter') ?></label></th>
                    <td>

                        <input type="text" class="regular-text" value="<?php echo @$data['under_title_out'] ?>" name="meta_data_filter_settings[under_title_out]">

                        <p class="description"><?php _e("It is depends of html structure of your current wordpress theme. Usually: 0 ... 2. Works when post features panel is draws automatically.", 'meta-data-filter') ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">

                        <br />

                        <hr />
                        <br />
                        <a class="button" href="http://meta-data-filter.pluginus.net/documentation/" target="_blank"><?php _e("Documentation", 'meta-data-filter') ?></a><br />

                        <br />
                        <h3><?php _e("Look for  the plugin full premium version", 'meta-data-filter') ?>:</h3>
                        <a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" target="_blank"><img src="<?php echo self::get_application_uri() ?>images/mdf_banner5.jpg" alt="" /></a><br />


                    </td>
                </tr>

            </tbody>
        </table>


        <p class="submit"><input type="submit" value="<?php _e("Save Changes", 'meta-data-filter') ?>" class="button button-primary" name="meta_data_filter_settings_submit"></p>
    </form>

</div>

