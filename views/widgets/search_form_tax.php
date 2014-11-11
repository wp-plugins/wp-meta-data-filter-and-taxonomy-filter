<?php if( ! defined ('ABSPATH') ) die ('No direct access allowed'); ?>

<?php
$unique_id = uniqid ();
$settings = self::get_settings ();
?>

<p>
    <label for="<?php echo $widget->get_field_id ('title'); ?>"><?php _e ('Title', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id ('title'); ?>" name="<?php echo $widget->get_field_name ('title'); ?>" value="<?php echo $instance['title']; ?>" />
</p>

<p>
    <label for="<?php echo $widget->get_field_id ('meta_data_filter_slug'); ?>"><?php _e ('Slug', 'meta-data-filter') ?>:</label>
    <?php
    if( isset ($settings['post_types']) ) {
        if( ! empty ($settings['post_types']) ) {
            ?>
            <select class="widefat meta_data_filter_slug" id="<?php echo $widget->get_field_id ('meta_data_filter_slug') ?>" name="<?php echo $widget->get_field_name ('meta_data_filter_slug') ?>">
                <?php foreach ( $settings['post_types'] as $post_name ) : ?>
                    <option <?php if( $instance['meta_data_filter_slug'] == $post_name ) echo 'selected="selected"'; ?> value="<?php echo $post_name ?>" class="level-0"><?php echo $post_name ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }
    ?>
</p>


<p>
    <label for="<?php echo $widget->get_field_id ('search_result_page'); ?>"><?php _e ('Results output page link', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id ('search_result_page'); ?>" name="<?php echo $widget->get_field_name ('search_result_page'); ?>" value="<?php echo $instance['search_result_page']; ?>" /><br />
    <i><?php _e ('Leave this field empty if you want to use the default value from settings', 'meta-data-filter') ?></i>
</p>


<p>
    <label for="<?php echo $widget->get_field_id ('search_result_tpl'); ?>"><?php _e ('Results output template', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id ('search_result_tpl'); ?>" name="<?php echo $widget->get_field_name ('search_result_tpl'); ?>" value="<?php echo $instance['search_result_tpl']; ?>" /><br />
    <i><?php _e ('Leave this field empty if you want to use the default value from settings', 'meta-data-filter') ?></i>
</p>

<p>
    <label for="<?php echo $widget->get_field_id ('reset_link'); ?>"><?php _e ('Custom reset link/url', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id ('reset_link'); ?>" name="<?php echo $widget->get_field_name ('reset_link'); ?>" value="<?php echo $instance['reset_link']; ?>" /><br />
    <i><?php _e ('Leave this field empty if you want to use the default value from settings', 'meta-data-filter') ?></i>
</p>

<p>
    <label for="<?php echo $widget->get_field_id ('show_items_count_text'); ?>"><?php _e ('Custom text for search results', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id ('show_items_count_text'); ?>" name="<?php echo $widget->get_field_name ('show_items_count_text'); ?>" value="<?php echo $instance['show_items_count_text']; ?>" /><br />
    <i><?php _e ('Example: Found &lt;span&gt;%s&lt;/span&gt; items', 'meta-data-filter') ?></i>
</p>

<p>
    <?php
    $checked = "";
    if($instance['show_found_totally'] == 'true') {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id('show_found_totally'); ?>" name="<?php echo $widget->get_field_name('show_found_totally'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id('show_found_totally'); ?>"><?php _e('Show found items count text', 'meta-data-filter') ?>:</label>
</p>

<hr />

<h3><?php _e ('Taxonomies options', 'meta-data-filter') ?></h3>


<p style="background: #ccc; padding: 5px;">
    <label for="<?php echo $widget->get_field_id ('taxonomies'); ?>"><b><?php _e ('Taxonomies for the selected slug', 'meta-data-filter') ?></b>:</label>
    <?php
    if( empty ($instance['meta_data_filter_slug']) ) {
        $instance['meta_data_filter_slug'] = reset ($settings['post_types']);
    }
    $taxonomies = get_object_taxonomies ($instance['meta_data_filter_slug'], 'objects');

    if( ! empty ($taxonomies) ) {
        ?>
        <input type="hidden" name="" value="<?php echo $widget->get_field_name ('taxonomies') ?>[]" />
    <ul id="meta_data_filter_tax_ul_<?php echo $unique_id ?>" class="meta_data_filter_tax_ul">
        <?php
        //sort them as were saved
        $taxonomies_sorted = (array) $instance['taxonomies'];
        foreach ( $taxonomies as $tax ) {
            $taxonomies_sorted[$tax->name] = $tax->label;
        }
        if( isset ($taxonomies_sorted['post_format']) ) {
            unset ($taxonomies_sorted['post_format']);
        }
        //removing values from not current post type
        foreach ( $taxonomies_sorted as $name=> $label ) {
            if( $label === 'true' ) {
                unset ($taxonomies_sorted[$name]);
            }
        }
        //+++
        ?>
        <?php foreach ( $taxonomies_sorted as $name=> $label ) : ?>
            <li>
                <?php
                $checked = "";
                if( isset ($instance['taxonomies'][$name]) ) {
                    $checked = 'checked="checked"';
                }
                ?>
                <div style="float:left;">
                    <input type="checkbox" id="<?php echo $widget->get_field_id ('taxonomies'); ?>_<?php echo $name ?>" name="<?php echo $widget->get_field_name ('taxonomies'); ?>[<?php echo $name ?>]" value="true" <?php echo $checked; ?> />
                    <label for="<?php echo $widget->get_field_id ('taxonomies'); ?>_<?php echo $name ?>"><?php echo $label ?>:</label>
                </div>
                <div style="float: right;">
                    <?php
                    $show_how = 'select';
                    if( is_array ($instance['taxonomies_options_show_how']) AND isset ($instance['taxonomies_options_show_how'][$name]) ) {
                        $show_how = $instance['taxonomies_options_show_how'][$name];
                    }

                    $select_size = 1;
                    if( is_array ($instance['taxonomies_options_select_size']) AND isset ($instance['taxonomies_options_select_size'][$name]) ) {
                        $select_size = $instance['taxonomies_options_select_size'][$name];
                    }


                    $show_child_terms = 0;
                    if( is_array ($instance['taxonomies_options_show_child_terms']) AND isset ($instance['taxonomies_options_show_child_terms'][$name]) ) {
                        $show_child_terms = $instance['taxonomies_options_show_child_terms'][$name];
                    }

                    $terms_section_toggle = 0;
                    if( is_array ($instance['taxonomies_options_terms_section_toggle']) AND isset ($instance['taxonomies_options_terms_section_toggle'][$name]) ) {
                        $terms_section_toggle = $instance['taxonomies_options_terms_section_toggle'][$name];
                    }

                    $tax_title = '';
                    if( is_array ($instance['taxonomies_options_tax_title']) AND isset ($instance['taxonomies_options_tax_title'][$name]) ) {
                        $tax_title = $instance['taxonomies_options_tax_title'][$name];
                    }

                    $checkbox_max_height = 0;
                    if( is_array ($instance['taxonomies_options_checkbox_max_height']) AND isset ($instance['taxonomies_options_checkbox_max_height'][$name]) ) {
                        $checkbox_max_height = $instance['taxonomies_options_checkbox_max_height'][$name];
                    }

                    $hide = '';
                    if( is_array ($instance['taxonomies_options_hide']) AND isset ($instance['taxonomies_options_hide'][$name]) ) {
                        $hide = trim ($instance['taxonomies_options_hide'][$name], ', ');
                    }
                    ?>
                    <a href="#" class="button mdf_tax_options" data-tax-name="<?php echo $name ?>"
                       data-hide="<?php echo $widget->get_field_id ('taxonomies_options_hide'); ?>_<?php echo $name ?>"
                       data-show-how="<?php echo $widget->get_field_id ('taxonomies_options_show_how'); ?>_<?php echo $name ?>"
                       data-select-size="<?php echo $widget->get_field_id ('taxonomies_options_select_size'); ?>_<?php echo $name ?>"
                       data-show-child-terms="<?php echo $widget->get_field_name ('taxonomies_options_show_child_terms'); ?>[<?php echo $name ?>]"
                       data-terms-section-toggle="<?php echo $widget->get_field_name ('taxonomies_options_terms_section_toggle'); ?>[<?php echo $name ?>]"
                       data-select-title="<?php echo $widget->get_field_id ('taxonomies_options_tax_title'); ?>_<?php echo $name ?>"
                       data-checkbox-max-height="<?php echo $widget->get_field_id ('taxonomies_options_checkbox_max_height'); ?>_<?php echo $name ?>"
                       ><?php _e ('Options', 'meta-data-filter'); ?></a>
                    <input type="hidden" name="<?php echo $widget->get_field_name ('taxonomies_options_hide'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id ('taxonomies_options_hide'); ?>_<?php echo $name ?>" value="<?php echo $hide; ?>" />
                    <input type="hidden" name="<?php echo $widget->get_field_name ('taxonomies_options_show_how'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id ('taxonomies_options_show_how'); ?>_<?php echo $name ?>" value="<?php echo $show_how; ?>" />
                    <input type="hidden" name="<?php echo $widget->get_field_name ('taxonomies_options_select_size'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id ('taxonomies_options_select_size'); ?>_<?php echo $name ?>" value="<?php echo $select_size; ?>" />
                    <input type="hidden" name="<?php echo $widget->get_field_name ('taxonomies_options_show_child_terms'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id ('taxonomies_options_show_child_terms'); ?>_<?php echo $name ?>" value="<?php echo $show_child_terms; ?>" />
                    <input type="hidden" name="<?php echo $widget->get_field_name ('taxonomies_options_terms_section_toggle'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id ('taxonomies_options_terms_section_toggle'); ?>_<?php echo $name ?>" value="<?php echo $terms_section_toggle; ?>" />
                    <input type="hidden" name="<?php echo $widget->get_field_name ('taxonomies_options_tax_title'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id ('taxonomies_options_tax_title'); ?>_<?php echo $name ?>" value="<?php echo $tax_title; ?>" />
                    <input type="hidden" name="<?php echo $widget->get_field_name ('taxonomies_options_checkbox_max_height'); ?>[<?php echo $name ?>]" id="<?php echo $widget->get_field_id ('taxonomies_options_checkbox_max_height'); ?>_<?php echo $name ?>" value="<?php echo $checkbox_max_height; ?>" />
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
} else {
    echo '<br />';
    _e ('No Taxonomies exists for selected slug', 'meta-data-filter');
}
?>
</p>

<p style="display: none;">
    <label for="<?php echo $widget->get_field_id ('taxonomies_options_behaviour'); ?>"><?php _e ('AND/OR behaviour for taxonomies', 'meta-data-filter') ?>:</label>
    <?php
    $sett = array(
        'AND'=>__ ('AND', 'meta-data-filter'),
        'OR'=>__ ('OR', 'meta-data-filter')
    );
    ?>
    <select class="widefat" id="<?php echo $widget->get_field_id ('taxonomies_options_behaviour') ?>" name="<?php echo $widget->get_field_name ('taxonomies_options_behaviour') ?>">
        <?php foreach ( $sett as $k=> $val ) : ?>
            <option <?php if( $instance['taxonomies_options_behaviour'] == $k ) echo 'selected="selected"'; ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
        <?php endforeach; ?>
    </select>
</p>


<p>
    <?php
    $checked = "";
    if( $instance['taxonomies_options_show_count'] == 'true' ) {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id ('taxonomies_options_show_count'); ?>" name="<?php echo $widget->get_field_name ('taxonomies_options_show_count'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id ('taxonomies_options_show_count'); ?>"><?php _e ('Show count of posts/custom', 'meta-data-filter') ?>:</label>
</p>




<p class="mdf_taxonomies_options_show_count" <?php if( ! $instance['taxonomies_options_show_count'] ): ?>style="display: none;"<?php endif; ?>>
    <?php
    $checked = "";
    if( $instance['taxonomies_options_post_recount_dyn'] == 'true' ) {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id ('taxonomies_options_post_recount_dyn'); ?>" name="<?php echo $widget->get_field_name ('taxonomies_options_post_recount_dyn'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id ('taxonomies_options_post_recount_dyn'); ?>"><?php _e ('Dynamic post recount', 'meta-data-filter') ?>:</label>
</p>

<p class="mdf_taxonomies_options_show_count" <?php if( ! $instance['taxonomies_options_show_count'] ): ?>style="display: none;"<?php endif; ?>>
    <?php
    $checked = "";
    if( $instance['taxonomies_options_hide_terms_0'] == 'true' ) {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id ('taxonomies_options_hide_terms_0'); ?>" name="<?php echo $widget->get_field_name ('taxonomies_options_hide_terms_0'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id ('taxonomies_options_hide_terms_0'); ?>"><?php _e ('Hide terms where count of items is 0', 'meta-data-filter') ?>:</label>
</p>


<p>
    <?php
    $checked = "";
    if( $instance['show_reset_button'] == 'true' ) {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id ('show_reset_button'); ?>" name="<?php echo $widget->get_field_name ('show_reset_button'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id ('show_reset_button'); ?>"><?php _e ('Show reset button', 'meta-data-filter') ?>:</label>
</p>




<p <?php if( $instance['ajax_items_recount'] == 'true' ): ?>style="display:none;"<?php endif; ?>>
    <?php
    $checked = "";
    if( $instance['act_without_button'] == 'true' ) {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id ('act_without_button'); ?>" name="<?php echo $widget->get_field_name ('act_without_button'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id ('act_without_button'); ?>"><?php _e ('Auto submit form', 'meta-data-filter') ?>:</label>
</p>

<p <?php if( $instance['act_without_button'] == 'true' ): ?>style="display:none;"<?php endif; ?>>
    <?php
    $checked = "";
    if( $instance['ajax_items_recount'] == 'true' ) {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id ('ajax_items_recount'); ?>" name="<?php echo $widget->get_field_name ('ajax_items_recount'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id ('ajax_items_recount'); ?>"><?php _e ('AJAX items recount', 'meta-data-filter') ?>:</label>
</p>


<p <?php if( $instance['act_without_button'] == 'true' ): ?>style="display:none;"<?php endif; ?>>
    <?php
    $checked = "";
    if( $instance['ajax_results'] == 'true' ) {
        $checked = 'checked="checked"';
    }
    ?>
    <input type="checkbox" id="<?php echo $widget->get_field_id ('ajax_results'); ?>" name="<?php echo $widget->get_field_name ('ajax_results'); ?>" value="true" <?php echo $checked; ?> />
    <label for="<?php echo $widget->get_field_id ('ajax_results'); ?>"><?php _e ('AJAX items output', 'meta-data-filter') ?>:</label>
</p>


<hr />

<p>
    <label for="<?php echo $widget->get_field_id ('additional_taxonomies'); ?>"><?php _e ('Additional taxonomies conditions', 'meta-data-filter') ?>:</label>
    <textarea class="widefat" id="<?php echo $widget->get_field_id ('additional_taxonomies'); ?>" name="<?php echo $widget->get_field_name ('additional_taxonomies'); ?>"><?php echo $instance['additional_taxonomies']; ?></textarea>
    <i><b><?php _e ('Example', 'meta-data-filter') ?></b>: product_cat=96,3^boutiques=77,88. <b><?php _e ('Syntax is', 'meta-data-filter') ?></b>: taxonomy_name=term_id,term_id,term_id ^ taxonomy_name2=term_id,term_id,term_id <br />...</i>
</p>

<hr />


    <p>
        <label for="<?php echo $widget->get_field_id ('woo_search_panel_id'); ?>"><?php _e ('Sort panel', 'meta-data-filter') ?>:</label>
        <?php MDTF_SORT_PANEL::draw_options_select ($instance['woo_search_panel_id'], $widget->get_field_name ('woo_search_panel_id'), $widget->get_field_id ('woo_search_panel_id')); ?>
    </p>


<p>
    <label for="<?php echo $widget->get_field_id ('title_for_filter_button'); ?>"><?php _e ('Title for filter button', 'meta-data-filter') ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id ('title_for_filter_button'); ?>" name="<?php echo $widget->get_field_name ('title_for_filter_button'); ?>" value="<?php echo $instance['title_for_filter_button']; ?>" />
</p>

<p>

    <input type="hidden" name="<?php echo $widget->get_field_name (MetaDataFilterCore::$slug_cat) ?>" value="<?php echo MetaDataFilter::WIDGET_TAXONOMIES_ONLY; ?>" />
    <input type="hidden" name="<?php echo $widget->get_field_name ('hide_meta_filter_values') ?>" value="true" />
    <input type="hidden" name="<?php echo $widget->get_field_name ('hide_tax_filter_values') ?>" value="false" />


    <script type="text/javascript">
        jQuery(function() {
            //+++
            jQuery('#meta_data_filter_ul_<?php echo $unique_id ?>,#meta_data_filter_tax_ul_<?php echo $unique_id ?>').sortable();
            //saving widget for getting selected post slug terms
            jQuery('#<?php echo $widget->get_field_id ('meta_data_filter_slug') ?>, #<?php echo $widget->get_field_id (MetaDataFilterCore::$slug_cat) ?>').life('change', function() {
                var widget = jQuery(this).closest('div.widget');
                wpWidgets.save(widget, 0, 1, 0);
            });
            //+++
            jQuery('#<?php echo $widget->get_field_id ('taxonomies_options_show_count'); ?>').click(function() {
                if (jQuery(this).is(":checked")) {
                    jQuery(this).parent().siblings('.mdf_taxonomies_options_show_count').show(200);
                } else {
                    jQuery(this).parent().siblings('.mdf_taxonomies_options_show_count').hide(200);
                }
            });
            //+++
            jQuery('#<?php echo $widget->get_field_id ('act_without_button'); ?>').click(function() {
                if (jQuery(this).is(":checked")) {
                    jQuery('#<?php echo $widget->get_field_id ('ajax_items_recount'); ?>').parent().hide(200);
                    jQuery('#<?php echo $widget->get_field_id ('ajax_results'); ?>').parent().hide(200);
                } else {
                    jQuery('#<?php echo $widget->get_field_id ('ajax_items_recount'); ?>').parent().show(200);
                    jQuery('#<?php echo $widget->get_field_id ('ajax_results'); ?>').parent().show(200);
                }
            });
            jQuery('#<?php echo $widget->get_field_id ('ajax_items_recount'); ?>').click(function() {
                if (jQuery(this).is(":checked")) {
                    jQuery('#<?php echo $widget->get_field_id ('act_without_button'); ?>').parent().hide(200);
                    jQuery('#<?php echo $widget->get_field_id ('ajax_results'); ?>').parent().show(200);
                } else {
                    jQuery('#<?php echo $widget->get_field_id ('act_without_button'); ?>').parent().show(200);
                    jQuery('#<?php echo $widget->get_field_id ('ajax_results'); ?>').parent().hide(200);
                }
            });
        });
    </script>
</p>
