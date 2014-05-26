<?php $settings = MetaDataFilterCore::get_settings(); ?>
<script type="text/javascript">
    var lang_one_moment = "<?php _e("One Moment ...", 'meta-data-filter') ?>";
    var mdf_tax_loader = '<?php MetaDataFilterHtml::draw_tax_loader(); ?>';
<?php if(is_admin()): ?>
        //admin
        var lang_no_ui_sliders = "<?php _e("no ui sliders in selected mdf category", 'meta-data-filter') ?>";
        var lang_updated = "<?php _e("Updated", 'meta-data-filter') ?>";
        //+++
        var mdf_slug_cat = "<?php echo MetaDataFilterCore::$slug_cat; ?>";
        var mdf_plugin_url = "<?php echo MetaDataFilterCore::get_application_uri() ?>";
<?php else: ?>
        var mdf_tooltip_theme = "<?php echo((isset($settings['tooltip_theme'])) ? $settings['tooltip_theme'] : 'shadow'); ?>";
        var tooltip_max_width = parseInt(<?php echo((isset($settings['tooltip_max_width'])) ? $settings['tooltip_max_width'] : 220); ?>, 10);
        var under_title_out = parseInt(<?php echo((isset($settings['under_title_out'])) ? $settings['under_title_out'] : 1); ?>, 10);
        var post_features_panel_auto = <?php echo MetaDataFilterCore::get_setting('post_features_panel_auto') ?>;
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        var mdf_front_qtrans_lang = "";
    <?php if(function_exists('qtrans_getLanguage')) : ?>
            mdf_front_qtrans_lang = "<?php echo qtrans_getLanguage() ?>";
    <?php endif; ?>
<?php endif; ?>
</script>
