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
                <li><a href="#tabs-6"><?php _e("Advanced options", 'meta-data-filter') ?></a></li>
                <li><a href="#tabs-7"><?php _e("Info", 'meta-data-filter') ?></a></li>
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
                            <th scope="row"><label><?php _e("Default order by", 'meta-data-filter') ?></label></th>
                            <td>
                                <?php
                                if (!isset($data['default_order_by']) OR empty($data['default_order_by']))
                                {
                                    $data['default_order_by'] = self::$default_order_by;
                                }
                                ?>
                                <input type="text" class="regular-text" value="<?php echo @$data['default_order_by'] ?>" name="meta_data_filter_settings[default_order_by]">
                                <?php
                                $default_orders = array(
                                    'DESC' => __("DESC", 'meta-data-filter'),
                                    'ASC' => __("ASC", 'meta-data-filter')
                                );
                                if (!isset($data['default_order']) OR empty($data['default_order']))
                                {
                                    $data['default_order'] = self::$default_order;
                                }
                                ?>
                                <select name="meta_data_filter_settings[default_order]">
                                    <?php foreach ($default_orders as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if (@$data['default_order'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php printf(__("Example: %s,_price. Default order-by of your filtered posts.", 'meta-data-filter'), implode(',', MetaDataFilterCore::$allowed_order_by)) ?></p>
                            </td>
                        </tr>


                        <tr valign="top">
                            <th scope="row"><label for="mdtf_label_43"><?php _e("Default sort panel", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>

                                        <?php
                                        $sort_panels_query = new WP_Query(array(
                                            'post_type' => MetaDataFilterCore::$slug_woo_sort,
                                            'post_status' => array('publish'),
                                            'orderby' => 'name',
                                            'order' => 'ASC'
                                        ));
                                        //+++
                                        if (!isset($data['default_sort_panel']))
                                        {
                                            $data['default_sort_panel'] = 0;
                                        }
                                        ?>

                                        <?php if ($sort_panels_query->have_posts()): ?>
                                            <select name="meta_data_filter_settings[default_sort_panel]">
                                                <?php while ($sort_panels_query->have_posts()) : ?>
                                                    <?php $sort_panels_query->the_post(); ?>
                                                    <option value="<?php the_ID() ?>" <?php if ($data['default_sort_panel'] == get_the_ID()): ?>selected="selected"<?php endif; ?>><?php the_title() ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                            <?php
                                            wp_reset_postdata();
                                            wp_reset_query();
                                            ?>
                                        <?php else: ?>
                                            <?php _e("No one sort panel created!", 'meta-data-filter') ?>
                                            <input type="hidden" name="meta_data_filter_settings[default_sort_panel]" value="0" />
                                        <?php endif; ?>
                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("Will be shown if the searching is not going by default if no panel id is set.", 'meta-data-filter') ?></p>
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
                            <th scope="row"><label for="hide_search_button_shortcode"><?php _e("Hide [mdf_search_button] on mobile devices", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input id="hide_search_button_shortcode" type="checkbox" <?php if (isset($data['hide_search_button_shortcode']) AND $data['hide_search_button_shortcode'] == 1) echo 'checked'; ?> value="1" name="meta_data_filter_settings[hide_search_button_shortcode]" />
                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("Hide button of search button shortcode on mobile devices", 'meta-data-filter') ?></p>
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


                        <tr valign="top">
                            <th scope="row"><label for="show_tax_all_childs"><?php _e("Show terms childs", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input id="show_tax_all_childs" type="checkbox" <?php if (isset($data['show_tax_all_childs']) AND $data['show_tax_all_childs'] == 1) echo 'checked'; ?> value="1" name="meta_data_filter_settings[show_tax_all_childs]" />
                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("Show terms childs always in taxonomies shown as checkboxes", 'meta-data-filter') ?></p>
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
                                        <input type="checkbox" <?php if (isset($data['use_chosen_js_s']) AND $data['use_chosen_js_s'] == 1) echo 'checked'; ?> value="1" name="meta_data_filter_settings[use_chosen_js_s]" />
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
                                            <input id="mdtf_label_42" type="checkbox" value="0" name="" disabled="" />
                                        </label>
                                    </fieldset>
                                    <p class="description"><?php _e("Check it if you want to make ajax searching directly on woocommerce shop page. BUT! It is not possible for 100% because a lot of wordpress themes developers not use in the right way woocommerce hooks woocommerce_before_shop_loop AND woocommerce_after_shop_loop. Works well with native woocommerce themes as Canvas and Primashop for example! Premium Only", 'meta-data-filter') ?></p>
                                </td>
                            </tr>






                        </tbody>
                    </table>
                    </p>
                </div>
            <?php endif; ?>

            <div id="tabs-5">
                <p>

                    <?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

                    <a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" target="_blank" class="button">In the premium version only</a><br />
                    <br />
                <hr />
                <br />
                <a href="http://cars.wordpress-filter.com/essential-grid/" target="_blank" class="button">Shortcodes gives you ability to create something like this</a>                    
                </p>
            </div>


            <div id="tabs-6">
                <p>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label for="gmap_js_include_pages"><?php _e("Include Google Map JS on the next pages", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input id="gmap_js_include_pages" type="text" class="regular-text" placeholder="Example: 75,134,96" value="<?php echo @$data['gmap_js_include_pages'] ?>" name="meta_data_filter_settings[gmap_js_include_pages]" />
                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("Some themes has already included google maps js, so maybe you will not need this option. But if you are need this - you can include it on pages (ID) on which you are using map, not on all pages of your site! Set <b>-1</b> if you want to include it on all pages of your site.", 'meta-data-filter') ?></p>
                            </td>
                        </tr>


                        <tr valign="top">
                            <th scope="row"><label for="keep_search_data_in"><?php _e("Where to keep search data", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>

                                        <?php
                                        $keep_search_data_in = array(
                                            'transients' => 'transients',
                                            'session' => 'session',
                                        );
                                        ?>

                                        <?php
                                        if (!isset($data['keep_search_data_in']) OR empty($data['keep_search_data_in']))
                                        {
                                            $data['keep_search_data_in'] = self::$where_keep_search_data;
                                        }
                                        ?>
                                        <select name="meta_data_filter_settings[keep_search_data_in]">
                                            <?php foreach ($keep_search_data_in as $key => $value) : ?>
                                                <option value="<?php echo $key; ?>" <?php if ($data['keep_search_data_in'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                            <?php endforeach; ?>
                                        </select>


                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("Better keep search data in sessions, but in some of reasons it sometimes not possible, so set it to transients. But transients make additional queries to data base! Set it to sessions when your search is working fine to exclude case when search doesn't work because data cannot be keeps in the session!", 'meta-data-filter') ?></p>
                            </td>
                        </tr>



                        <tr valign="top">
                            <th scope="row"><label for="cache_count_data"><?php _e("Cache dynamic recount number for each item in filter", 'meta-data-filter') ?></label></th>
                            <td>
                                <fieldset>
                                    <label>

                                        <?php
                                        $cache_count_data = array(
                                            0 => __("No", 'meta-data-filter'),
                                            1 => __("Yes", 'meta-data-filter')
                                        );
                                        ?>

                                        <?php
                                        if (!isset($data['cache_count_data']) OR empty($data['cache_count_data']))
                                        {
                                            $data['cache_count_data'] = 0;
                                        }
                                        ?>
                                        <select name="meta_data_filter_settings[cache_count_data]">
                                            <?php foreach ($cache_count_data as $key => $value) : ?>
                                                <option value="<?php echo $key; ?>" <?php if ($data['cache_count_data'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                            <?php endforeach; ?>
                                        </select>


                                        <?php if ($data['cache_count_data']): ?>
                                            &nbsp;<a href="#" class="button js_cache_count_data_clear"><?php _e("clear cache", 'meta-data-filter') ?></a>&nbsp;<span style="color: green; font-weight: bold;"></span>
                                        <?php endif; ?>

                                    </label>
                                </fieldset>
                                <p class="description"><?php _e("Useful thing when you already started your site and use dynamic recount -> it make recount very fast! Of course if you added new posts which have to be in search results you have to clean this cache!", 'meta-data-filter') ?></p>


                                <?php
                                global $wpdb;

                                $charset_collate = '';
                                if (method_exists($wpdb, 'has_cap') AND $wpdb->has_cap('collation'))
                                {
                                    if (!empty($wpdb->charset))
                                    {
                                        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                                    }
                                    if (!empty($wpdb->collate))
                                    {
                                        $charset_collate .= " COLLATE $wpdb->collate";
                                    }
                                }
                                //***
                                $sql = "CREATE TABLE IF NOT EXISTS `" . MetaDataFilterCore::$mdf_query_cache_table . "` (
                                    `mkey` text NOT NULL,
                                    `mvalue` text NOT NULL
                                  ){$charset_collate}";

                                if ($wpdb->query($sql) === false)
                                {
                                    ?>
                                    <p class="description"><?php _e("MDTF cannot create the database table! Make sure that your mysql user has the CREATE privilege! Do it manually using your host panel&phpmyadmin!", 'meta-data-filter') ?></p>
                                    <code><?php echo $sql; ?></code>
                                    <input type="hidden" name="meta_data_filter_settings[cache_count_data]" value="0" />
                                    <?php
                                    echo $wpdb->last_error;
                                }
                                ?>
                            </td>
                        </tr>


                    </tbody>
                </table>
                </p>
            </div>


            <div id="tabs-7">
                <p>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Demo sites", 'meta-data-filter') ?></label></th>
                            <td>

                                <ul style="margin: 6px;">
                                    <li>
                                        <a href="http://woocommerce.wordpress-filter.com/" target="_blank">WooCommerce</a>
                                    </li>
                                    <li>
                                        <a href="http://cars.wordpress-filter.com/" target="_blank">CarDealer</a>
                                    </li>
                                    <li>
                                        <a href="http://demo2.wordpress-filter.com/" target="_blank">Posts</a>
                                    </li>
                                    <li>
                                        <a href="http://miscellaneous.wordpress-filter.com/" target="_blank">Miscellaneous</a>
                                    </li>
                                </ul>

                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e("Recommended plugins", 'meta-data-filter') ?></label></th>
                            <td>

                                <ul style="margin: 6px;">
                                    <li>
                                        <a href="https://wordpress.org/plugins/taxonomy-terms-order/" target="_blank">Category Order and Taxonomy Terms Order</a><br />
                                        <p class="description"><?php _e("Order Categories and all custom taxonomies terms (hierarchically) and child terms using a Drag and Drop Sortable javascript capability", 'meta-data-filter') ?></p>
                                    </li>

                                    <li>
                                        <a href="https://wordpress.org/plugins/woocommerce-currency-switcher/" target="_blank">WooCommerce Currency Switcher</a><br />
                                        <p class="description"><?php _e("WooCommerce Currency Switcher – is the plugin that allows you to switch to different currencies and get their rates converted in the real time!", 'meta-data-filter') ?></p>
                                    </li>

                                    <li>
                                        <a href="https://wordpress.org/plugins/inpost-gallery/" target="_blank">InPost Gallery</a><br />
                                        <p class="description"><?php _e("Insert Gallery in post, page and custom post types just in two clicks ", 'meta-data-filter') ?></p>
                                    </li>


                                    <li>
                                        <a href="https://wordpress.org/plugins/autoptimize/" target="_blank">Autoptimize</a><br />
                                        <p class="description"><?php _e("It concatenates all scripts and styles, minifies and compresses them, adds expires headers, caches them, and moves styles to the page head, and scripts to the footer", 'meta-data-filter') ?></p>
                                    </li>


                                    <li>
                                        <a href="https://wordpress.org/plugins/pretty-link/" target="_blank">Pretty Link Lite</a><br />
                                        <p class="description"><?php _e("Shrink, beautify, track, manage and share any URL on or off of your WordPress website. Create links that look how you want using your own domain name!", 'meta-data-filter') ?></p>
                                    </li>

                                    <li>
                                        <a href="https://wordpress.org/plugins/custom-post-type-ui/" target="_blank">Custom Post Type UI</a><br />
                                        <p class="description"><?php _e("This plugin provides an easy to use interface to create and administer custom post types and taxonomies in WordPress.", 'meta-data-filter') ?></p>
                                    </li>

                                    <li>
                                        <a href="https://wordpress.org/plugins/widget-logic/other_notes/" target="_blank">Widget Logic</a><br />
                                        <p class="description"><?php _e("Widget Logic lets you control on which pages widgets appear using", 'meta-data-filter') ?></p>
                                    </li>

                                    <li>
                                        <a href="https://wordpress.org/plugins/wp-super-cache/" target="_blank">WP Super Cache</a><br />
                                        <p class="description"><?php _e("Cache pages, allow to make a lot of search queries on your site without high load on your server!", 'meta-data-filter') ?></p>
                                    </li>


                                    <li>
                                        <a href="https://wordpress.org/plugins/wp-migrate-db/" target="_blank">WP Migrate DB</a><br />
                                        <p class="description"><?php _e("Exports your database, does a find and replace on URLs and file paths, then allows you to save it to your computer.", 'meta-data-filter') ?></p>
                                    </li>

                                </ul>

                            </td>
                        </tr>


                        <tr valign="top">
                            <th scope="row"><label><?php _e("Books for business", 'meta-data-filter') ?></label></th>
                            <td>

                                <ul>
                                    <li>
                                        <a target="_blank" href="http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_tl?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=1497388686&linkCode=ur2&tag=plugnet-20&linkId=HRYE7SCPJGQIZG6Z"><img src=" data:image/jpeg;base64,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" border="0" alt="" style="border:none !important; margin:0px !important;" /></a><br />
                                        <p class="description">“Web Guys” tend to live in their mother’s basement and work whenever they feel like it. They can be costly, unresponsive, slow and downright flakey. The good news for you is that you don’t really need a Web Guy. Using WordPress, we show you how to build a professional-looking website quickly and easily without knowing any code at all. If you need a website quick, have a limited budget and a free weekend, then this book is for you. In this short, simple tutorial we give step-by-step instructions including: </p>
                                    </li>
                                    <li>
                                        <a target="_blank" href ='http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_il?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=B008REKNYE&linkCode=as2&tag=plugnet-20&linkId=OHFWMT2MHUMWQFXD'><img src='http://ws-na.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B008REKNYE&Format=_SL250_&ID=AsinImage&MarketPlace=US&ServiceVersion=20070822&WS=1&tag=plugnet-20' border='0' /></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=plugnet-20&l=as2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /><br />
                                        <p class="description">

                                            Here's what real Amazon customers are saying about E-Commerce Blueprint...<br />
                                            <br /><br />
                                            "The E-commerce Blueprint, was one of the most detailed and easy to follow guides I have ever read."  - Randal Mayo
                                            <br /><br />
                                            "Outstanding resource for new and existing business owners alike." - Mark Collier
                                            <br /><br />
                                            "This comprehensive and well-written, well organized book is a must-purchase for anyone getting into e-commerce and setting up a web-based store." - Nicolette Goff
                                            <br /><br />
                                            Discover everything you need to know about starting your own online e-commerce empire. This insider's guide to the world of e-commerce starts at the very beginning and teaches you how to find the right products to sell then guides you step-by-step through the entire process.
                                            <br /><br />
                                            You'll learn...
                                            <br /><br />
                                            How to find great products even if you have no idea what to sell.
                                            <br />How to find great suppliers and avoid bad ones.
                                            <br />How to create a top-notch business plan for free.
                                            <br />Are you a sole proprietorship, partnership, LLC or something else?
                                            <br />What are the permits and licenses you'll need to open your store?
                                            <br /><br />
                                            <br />Without a road map to success, most e-commerce entrepreneurs fail miserably. This longtime e-commerce business owner takes you through his own successes and failures to demonstrate how to manage your online business with precision and turn a profit.
                                            <br /><br />
                                            Discover these secrets...

                                        </p>
                                    </li>
                                    <li>
                                        <a target="_blank" href ='http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_il?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=1479178527&linkCode=as2&tag=plugnet-20&linkId=JBHLMCINLP2NIQZO'><img src='http://ws-na.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=1479178527&Format=_SL250_&ID=AsinImage&MarketPlace=US&ServiceVersion=20070822&WS=1&tag=plugnet-20' border='0' /></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=plugnet-20&l=as2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /><br />
                                        <p class="description">
                                            A step by step guide that tells you how to create your own online retail empire the smart way. How to find a niche market with low competition. How to source the product as cheaply as possible and get around high quantity requirements. How to pay your suppliers safely and what shipping methods to use. What kind of website to build and when to build it. How to use social media including Facebook, Twitter and Pinterest to promote your sites. How to determine pricing. How to improve your conversion rate. How to outsource. How to get good SEO done cheaply and effectively. Nathan and Tessa Hartnett have been successfully building online retail stores since 2006, and share their complete business model with you, so that you can do the same - no matter what your background!
                                        </p>
                                    </li>
                                    <li>
                                        <a target="_blank" href ='http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_il?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=1500783692&linkCode=as2&tag=plugnet-20&linkId=IP75L7WL6WZKQMY4'><img src='http://ws-na.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=1500783692&Format=_SL250_&ID=AsinImage&MarketPlace=US&ServiceVersion=20070822&WS=1&tag=plugnet-20' border='0' /></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=plugnet-20&l=as2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /><br />
                                        <p class="description">
                                            Stop guessing and start becoming an internet retailer. Retail Arbitrage is the process of buying something from a retail store or outlet and reselling it for a premium elsewhere. Many people have started to hear the term "Retail Arbitrage" and are interested in how their can be profit in shopping for items at a retail store to resell! Josh Smith takes the guess-work out of this and breaks Retail Arbitrage down into a step by step guide starting with the basics. In this book you will learn what Retail Arbitrage is, different methods of Retail Arbitrage, as well as how to pull off your own Retail Arbitrage. On top of all of that information Josh Smith wanted to empower everyone to be able to get started on their own and take action immediately after reading the book, for this reason he included a resource list of places you can actively buy at to resell at a profit! Josh Smith takes it a step farther, he shows you not only every aspect of Retail Arbitrage, but he shows you how to maximize it! You also will learn how to get your customers coming back time and time again without any effort on your part! If that was not enough, Josh Smith also shows you step by step how to build your own e-commerce store from scratch! This will help you to not only take your online business to the next level, but give you more profit just from saving on marketplace fees! Whether you are just starting out or a seasoned veteran, you will benefit from this guide. Order it today, all purchases are covered with a 100% satisfaction guarantee!
                                        </p>
                                    </li>
                                    <li>
                                        <a target="_blank" href ='http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_il?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=1118987241&linkCode=as2&tag=plugnet-20&linkId=KWLBRBJQI4VX2NTT'><img src='http://ws-na.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=1118987241&Format=_SL250_&ID=AsinImage&MarketPlace=US&ServiceVersion=20070822&WS=1&tag=plugnet-20' border='0' /></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=plugnet-20&l=as2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /><br />
                                        <p class="description">
                                            The highest rated WordPress development and design book on themarket is back with an all new third edition.<br />
                                            Professional WordPress is the only WordPress booktargeted to developers, with advanced content that exploits thefull functionality of the most popular CMS in the world. Fullyupdated to align with WordPress 4.1, this edition has updatedexamples with all new screenshots, and full exploration ofadditional tasks made possible by the latest tools and features.You will gain insight into real projects that currently useWordPress as an application framework, as well as the basic usageand functionality of the system from a developer's perspective. Thebook's key features include detailed information and real-worldexamples that illustrate the concepts and techniques at work, pluscode downloads and examples accessible through the companionwebsite. Written by practicing WordPress developers, the content ofthis edition focuses on real world application of WordPressconcepts that extend beyond the current WordPress version.
                                            <br /><br />
                                            WordPress started in 2003 with a single bit of code to enhancethe typography of everyday writing, and has grown to be the largestself-hosted website platform in the world. This book helps you useWordPress efficiently, effectively, and professionally, with newideas and expert perspectives on full system exploitation. * Get up to speed on the new features in WordPress 4.1 * Learn cutting edge uses of WordPress, including real-worldprojects * Discover how to migrate existing websites to WordPress * Understand current best practices and tools in WordPressdevelopment
                                            <br /><br />
                                            WordPress was born out of a desire for an elegant,well-architected personal publishing system built on PHP and MySQL,and has evolved to be used as a full content management systemthrough thousands of plugins, widgets, and themes. ProfessionalWordPress is the essential developer's guide to thismultifunctional system.
                                        </p>
                                    </li>
                                </ul>

                            </td>
                        </tr>

                    </tbody>
                </table>
                </p>
            </div>


        </div>



        <p>
            <a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" alt="Get the plugin" target="_blank"><img src="<?php echo self::get_application_uri() ?>images/mdtf_banner.jpg" /></a>&nbsp;
            <a href="http://codecanyon.net/item/woocommerce-currency-switcher/8085217?ref=realmag777" target="_blank"><img src="<?php echo self::get_application_uri() ?>images/woocs_banner.jpg" alt="<?php _e("full version of the plugin", 'meta-data-filter'); ?>" /></a>
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
        //+++
        jQuery('.js_cache_count_data_clear').click(function () {
            jQuery(this).next('span').html('clearing ...');
            var _this = this;
            var data = {
                action: "mdf_cache_count_data_clear"
            };
            jQuery.post(ajaxurl, data, function () {
                jQuery(_this).next('span').html('cleared!');
            });

            return false;
        });
    });

</script>
</div>

