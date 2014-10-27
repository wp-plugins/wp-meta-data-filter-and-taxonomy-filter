<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<a class="button button-primary button-medium add_item_to_data_group" data-add-to='top' href="#"><?php _e("Prepend Filter Item", 'meta-data-filter') ?></a><br />
<br />
<?php global $post; ?>
<script type="text/javascript">
    var mdf_current_post_id=<?php echo $post->ID ?>;
</script>
<ul id="data_group_items">
    <?php if (!empty($html_items) AND is_array($html_items)): ?>
        <?php foreach ($html_items as $key => $value) : ?>
            <li class="admin-drag-holder mdf_filter_item"><?php echo MetaDataFilterHtml::render_html(MetaDataFilter::get_application_path() . 'views/add_item_to_data_group.php', array('itemdata' => $value, 'uniqid' => $key)); ?></li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<br />
<a class="button button-primary button-medium add_item_to_data_group" data-add-to='bottom' href="#"><?php _e("Append Filter Item", 'meta-data-filter') ?></a><br />

