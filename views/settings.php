<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="wrap">
    <h2><?php _e("MDTF Settings", 'meta-data-filter') ?> v.<?php echo MetaDataFilter::get_plugin_ver() ?>&nbsp;&nbsp;&nbsp;<a href="http://wordpress-filter.com/step-by-step/" target="_blank" class="button"><?php _e("Read", 'meta-data-filter') ?></a>&nbsp;<?php _e("&", 'meta-data-filter') ?>&nbsp; <a href="http://wordpress-filter.com/video/" target="_blank" class="button"><?php _e("Watch", 'meta-data-filter') ?></a></h2>


    <?php if (!empty($_POST)): ?>
        <div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php _e("Settings are saved.", 'meta-data-filter') ?></strong></p></div>
    <?php endif; ?>


    <form action="<?php echo admin_url('edit.php?post_type=' . MetaDataFilterCore::$slug . '&page=mdf_settings') ?>" method="post">

        <div id="tabs">
            <ul>
                <li><a href="#tabs-1"><?php _e("Main settings", 'meta-data-filter') ?></a></li>
                <li><a href="#tabs-3"><?php _e("Front interface", 'meta-data-filter') ?></a></li>
                <li><a href="#tabs-2"><?php _e("Miscellaneous", 'meta-data-filter') ?></a></li>
                <?php if (class_exists('WooCommerce')): ?>
                    <li><a href="#tabs-4"><?php _e("WooCommerce", 'meta-data-filter') ?></a></li>
                <?php endif; ?>
                <li><a href="#tabs-5"><?php _e("In-Built Pagination", 'meta-data-filter') ?></a></li>
                <!-- <li><a href="#tabs-6"><?php _e("Extencions", 'meta-data-filter') ?></a></li> -->
            </ul>


            <div id="tabs-1">
                <p>
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
                            <th scope="row"><label><?php _e("Output search template", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo $data['output_tpl'] ?>" name="meta_data_filter_settings[output_tpl]">
                                <p class="description"><?php _e("Output template, search by default. For example: search,archive,content or your custom which is in current wp theme. If you want to set double name template, write it in such manner for example: content-special. If you do not understood - leave search!", 'meta-data-filter') ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Supported post types", 'meta-data-filter') ?></label></th>
                            <td>

                                <?php foreach (MetaDataFilterCore::get_post_types() as $post_type => $post_type_name) : ?>

                                    <fieldset>
                                        <label>
                                            <input type="checkbox" <?php if (@in_array($post_type_name, $data['post_types'])) echo 'checked'; ?> value="<?php echo $post_type_name ?>" name="meta_data_filter_settings[post_types][]" />
                                            <?php echo $post_type_name ?>
                                        </label>
                                    </fieldset>

                                <?php endforeach; ?>
                                <p class="description"><?php _e("Check post types which should be searched", 'meta-data-filter') ?></p>

                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Reset custom link", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo @$data['reset_link'] ?>" name="meta_data_filter_settings[reset_link]">
                                <p class="description"><?php _e("Leave this field empty if you do not need this. Of course each widget and shortcode has such option too.", 'meta-data-filter') ?></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label><?php _e("Results per page", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo(isset($data['results_per_page']) ? $data['results_per_page'] : 0) ?>" name="meta_data_filter_settings[results_per_page]">
                                <p class="description"><?php _e("Leave this field empty if you want to use wordpress or your theme settings.", 'meta-data-filter') ?></p>

                            </td>
                        </tr>
                    </tbody>
                </table>
                </p>
            </div>
            <div id="tabs-2">
                <p>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Loading text", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo @$data['loading_text'] ?>" name="meta_data_filter_settings[loading_text]">
                                <p class="description"><?php _e("Example: One Moment ...", 'meta-data-filter') ?></p>
                                <br />
                                <hr />
                            </td>
                        </tr>


                        <tr valign="top">
                            <th scope="row"><label><?php _e("Toggle open sign", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo(isset($data['toggle_open_sign']) ? $data['toggle_open_sign'] : '+') ?>" name="meta_data_filter_settings[toggle_open_sign]">
                                <p class="description"><?php _e("Toggle open sign on front widget while using toggles for sections.", 'meta-data-filter') ?></p>
                            </td>
                        </tr>


                        <tr valign="top">
                            <th scope="row"><label><?php _e("Toggle close sign", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo(isset($data['toggle_close_sign']) ? $data['toggle_close_sign'] : '-') ?>" name="meta_data_filter_settings[toggle_close_sign]">
                                <p class="description"><?php _e("Toggle close sign on front widget while using toggles for sections.", 'meta-data-filter') ?></p>
                            </td>
                        </tr>


                        <tr valign="top">
                            <th scope="row"><label for="ignore_sticky_posts"><?php _e("Ignore sticky posts", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input id="ignore_sticky_posts" type="checkbox" <?php if (isset($data['ignore_sticky_posts']) AND $data['ignore_sticky_posts'] == 1) echo 'checked'; ?> value="1" name="meta_data_filter_settings[ignore_sticky_posts]" />
                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("Ignore sticky posts in search results", 'meta-data-filter') ?></p>
                            </td>
                        </tr>

                    </tbody>
                </table>
                </p>
            </div>
            <div id="tabs-3">
                <p>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Tooltip theme", 'meta-data-filter') ?></label></th>
                            <td>
                                <?php
                                $tooltip_themes = array(
                                    'default' => __("Default", 'meta-data-filter'),
                                    'light' => __("Light", 'meta-data-filter'),
                                    'noir' => __("Noir", 'meta-data-filter'),
                                    'punk' => __("Punk", 'meta-data-filter'),
                                    'shadow' => __("Shadow", 'meta-data-filter')
                                );
                                ?>
                                <select name="meta_data_filter_settings[tooltip_theme]">
                                    <?php foreach ($tooltip_themes as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($data['tooltip_theme'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Tooltip icon image URL", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" placeholder="<?php _e("default icon", 'meta-data-filter') ?>" value="<?php echo $data['tooltip_icon'] ?>" name="meta_data_filter_settings[tooltip_icon]">
                                <p class="description"><?php _e("Link to png icon for tooltip in widgets and shortcodes", 'meta-data-filter') ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Tooltip max width", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo @$data['tooltip_max_width'] ?>" name="meta_data_filter_settings[tooltip_max_width]">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Tab slideout icon image settings", 'meta-data-filter') ?></label></th>
                            <td>
                                <input type="text" class="regular-text" placeholder="<?php _e("default icon url", 'meta-data-filter') ?>" value="<?php echo @$data['tab_slideout_icon'] ?>" name="meta_data_filter_settings[tab_slideout_icon]"><br />
                                <input type="text" class="regular-text" placeholder="<?php _e("default icon width", 'meta-data-filter') ?>" value="<?php echo @$data['tab_slideout_icon_w'] ?>" name="meta_data_filter_settings[tab_slideout_icon_w]"><br />
                                <input type="text" class="regular-text" placeholder="<?php _e("default icon height", 'meta-data-filter') ?>" value="<?php echo @$data['tab_slideout_icon_h'] ?>" name="meta_data_filter_settings[tab_slideout_icon_h]"><br />
                                <p class="description"><?php _e("Link and width/height to png icon for tab slideout shortcode", 'meta-data-filter') ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("jQuery-ui calendar date format", 'meta-data-filter') ?></label></th>
                            <td>
                                <?php
                                $calendar_date_formats = array(
                                    'mm/dd/yy' => __("Default - mm/dd/yy", 'meta-data-filter'),
                                    'dd-mm-yy' => __("Europe - dd-mm-yy", 'meta-data-filter'),
                                    'yy-mm-dd' => __("ISO 8601 - yy-mm-dd", 'meta-data-filter'),
                                    'd M, y' => __("Short - d M, y", 'meta-data-filter'),
                                    'd MM, y' => __("Medium - d MM, y", 'meta-data-filter'),
                                    'DD, d MM, yy' => __("Full - DD, d MM, yy", 'meta-data-filter')
                                );
                                $calendar_date_format = (isset($data['calendar_date_format']) ? $data['calendar_date_format'] : 'mm/dd/yy');
                                ?>
                                <select name="meta_data_filter_settings[calendar_date_format]">
                                    <?php foreach ($calendar_date_formats as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($calendar_date_format == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Use chosen js library for drop-downs in the search forms", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" <?php if (isset($data['use_chosen_js_w']) AND $data['use_chosen_js_w'] == 1) echo 'checked'; ?> value="1" name="meta_data_filter_settings[use_chosen_js_w]" />
                                        <?php _e("for widgets", 'meta-data-filter') ?>
                                    </label>
                                </fieldset>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" disabled="" value="0" name="" />
                                        <input type="hidden" name="meta_data_filter_settings[use_chosen_js_s]" value="0" />
                                        <?php _e("for shortcodes", 'meta-data-filter') ?>
                                    </label>
                                </fieldset>
                                <img src="<?php echo MetaDataFilter::get_application_uri() ?>images/chosen_selects.png" alt="" /><br />
                                <p class="description"><?php _e("Not compatible with all wp themes. Uncheck the checkbox it is works bad.", 'meta-data-filter') ?></p>

                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="use_custom_scroll_bar"><?php _e("Use custom scroll js bar", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input id="use_custom_scroll_bar" type="checkbox" <?php if (isset($data['use_custom_scroll_bar']) AND $data['use_custom_scroll_bar'] == 1) echo 'checked'; ?> value="1" name="meta_data_filter_settings[use_custom_scroll_bar]" />
                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("Beautiful js scroll bars. Sometimes not compatible with some wordpress themes", 'meta-data-filter') ?></p>
                            </td>
                        </tr>

                        <tr valign="top" colspan="2">
                            <th scope="row"><label for="use_custom_icheck"><?php _e("Use icheck js library for checkboxes", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input id="use_custom_icheck" type="checkbox" <?php if (isset($data['use_custom_icheck']) AND $data['use_custom_icheck'] == 1) echo 'checked'; ?> value="1" name="meta_data_filter_settings[use_custom_icheck]" />
                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("JS CUSTOMIZED CHECKBOXES AND RADIO BUTTONS FOR JQUERY: http://fronteed.com/iCheck/", 'meta-data-filter') ?></p>
                            </td>
                            <td>
                                <?php _e("Skin", 'meta-data-filter') ?>:<br />
                                <?php
                                $skins = array(
                                    'flat' => array(
                                        'flat_aero',
                                        'flat_blue',
                                        'flat_flat',
                                        'flat_green',
                                        'flat_grey',
                                        'flat_orange',
                                        'flat_pink',
                                        'flat_purple',
                                        'flat_red',
                                        'flat_yellow'
                                    ),
                                    'line' => array(
                                        //'line_aero',
                                        'line_blue',
                                        'line_green',
                                        'line_grey',
                                        'line_line',
                                        'line_orange',
                                        'line_pink',
                                        'line_purple',
                                        'line_red',
                                        'line_yellow'
                                    ),
                                    'minimal' => array(
                                        'minimal_aero',
                                        'minimal_blue',
                                        'minimal_green',
                                        'minimal_grey',
                                        'minimal_minimal',
                                        'minimal_orange',
                                        'minimal_pink',
                                        'minimal_purple',
                                        'minimal_red',
                                        'minimal_yellow'
                                    ),
                                    'square' => array(
                                        'square_aero',
                                        'square_blue',
                                        'square_green',
                                        'square_grey',
                                        'square_orange',
                                        'square_pink',
                                        'square_purple',
                                        'square_red',
                                        'square_yellow',
                                        'square_square'
                                    )
                                );
                                $skin = (isset($data['icheck_skin']) ? $data['icheck_skin'] : 'flat_blue');
                                ?>
                                <select name="meta_data_filter_settings[icheck_skin]">
                                    <?php foreach ($skins as $key => $schemes) : ?>
                                        <optgroup label="<?php echo $key ?>">
                                            <?php foreach ($schemes as $scheme) : ?>
                                                <option value="<?php echo $scheme; ?>" <?php if ($skin == $scheme): ?>selected="selected"<?php endif; ?>><?php echo $scheme; ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>&nbsp;
                            </td>
                        </tr>

                    </tbody>
                </table>
                </p>
            </div>

            <?php if (class_exists('WooCommerce')): ?>
                <div id="tabs-4">
                    <p>
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row"><label for="mdtf_label_41"><?php _e("Exclude 'out of stock' products from search", 'meta-data-filter') ?></label></th>
                                <td>
                                    <fieldset>
                                        <label>
                                            <input id="mdtf_label_41" type="checkbox" <?php if (isset($data['exclude_out_stock_products'])) echo 'checked'; ?> value="1" name="meta_data_filter_settings[exclude_out_stock_products]" />
                                        </label>
                                    </fieldset>
                                    <p class="description"></p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row"><label for="mdtf_label_42"><?php _e("Try to make shop page AJAXED", 'meta-data-filter') ?></label></th>
                                <td>
                                    <fieldset>
                                        <label>
                                            <input id="mdtf_label_42" disabled="" type="checkbox" value="0" name="" />
                                            <input type="hidden" name="meta_data_filter_settings[try_make_shop_page_ajaxed]" value="0" />
                                        </label>
                                    </fieldset>
                                    <p class="description"><?php _e("In premium version only! Check it if you want to make ajax searching directly on woocommerce shop page. BUT! It is not possible for 100% because a lot of wordpress themes developers not use in the right way woocommerce hooks woocommerce_before_shop_loop AND woocommerce_after_shop_loop. Works well with native woocommerce themes as Canvas and Primashop for example!", 'meta-data-filter') ?></p>
                                </td>
                            </tr>




                        </tbody>
                    </table>
                    </p>
                </div>
            <?php endif; ?>

            <div id="tabs-5">
                <p>
                    In premium version only! Needs for ajax.
                </p>
            </div>



        </div>

        <p>
            <a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" alt="Get the plugin" target="_blank"><img src="<?php echo self::get_application_uri() ?>images/mdtf_banner.jpg" /></a>

        </p>

        <p class="submit" style="padding: 9px;"><input type="submit" value="<?php _e("Save Settings", 'meta-data-filter') ?>" class="button button-primary" name="meta_data_filter_settings_submit"></p>
    </form>

    <hr />

    <p>
    <h3><?php _e("Mass assign filter-category ID to any posts types", 'meta-data-filter') ?></h3>
    <form action="<?php echo admin_url('edit.php?post_type=' . MetaDataFilterCore::$slug . '&page=mdf_settings') ?>" method="post">
        <table class="form-table">
            <tbody>

                <tr valign="top">
                    <th scope="row"><label><?php _e("Supported post types", 'meta-data-filter') ?></label></th>
                    <td>
                        <select name="mass_filter_slug">
                            <?php foreach (MetaDataFilterCore::get_post_types() as $post_type => $post_type_name) : ?>
                                <option value="<?php echo $post_type_name ?>"><?php echo $post_type_name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e("Check post types to which filter ID should be assign. Enter right data, do not joke with it!", 'meta-data-filter') ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e("Enter filter-category ID", 'meta-data-filter') ?></label></th>
                    <td>
                        <input type="text" class="regular-text" value="" name="mass_filter_id">
                        <p class="description">
                            <img src="<?php echo MetaDataFilter::get_application_uri() ?>images/mass_filter_id.png" alt="" /><br />
                        </p>
                    </td>
                </tr>


            </tbody>
        </table>


        <p class="submit"><input type="submit" value="<?php _e("Assign", 'meta-data-filter') ?>" class="button button-primary" name="meta_data_filter_assign_filter_id"></p>
    </form>

</p>



<script type="text/javascript">
    jQuery(function () {
        jQuery("#tabs").tabs();
    });
</script>
</div>

