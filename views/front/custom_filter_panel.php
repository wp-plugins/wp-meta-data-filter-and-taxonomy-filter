<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php if (!empty($custom_filter_panel)): ?>    
    <?php
    $order = 'DESC';
    if (isset($_GET['order'])) {
        $order = $_GET['order'];
    }

    $order_by = 'date';
    if (isset($_GET['order_by'])) {
        $order_by = $_GET['order_by'];
    }
    //***
    $url = /* MetaDataFilterCore::get_search_url_page() . */'?';
    if (!empty($_GET)) {
        $counter = 0;
        foreach ($_GET as $key => $value) {
            if ($key == 'order_by' OR $key == 'order') {
                continue;
            }
            if ($counter > 0) {
                $url.='&';
            }
            $url.=$key . '=' . $value;
            $counter++;
        }
    }
    ?>
    <ul class="mdf_custom_filter_panel">
        <li <?php if ($order_by == 'date'): ?>class="meta_data_filter_order_<?php echo strtolower($order) ?>"<?php endif; ?>>
            <a href="<?php echo $url ?>&order_by=date&order=<?php echo($order == 'ASC' ? 'DESC' : 'ASC'); ?>"><?php echo _e('Date', 'meta-data-filter'); ?></a>
        </li>
        <?php foreach ($custom_filter_panel as $value) : ?>            
            <?php
            list($key, $post_id) = explode('~', $value);
            $html_items = MetaDataFilterCore::get_html_items($post_id);
            $title = $html_items[$key]['name'];
            if (isset($html_items[$key]['is_reflected'])) {
                if ($html_items[$key]['is_reflected'] == 1) {
                    $key = $html_items[$key]['reflected_key'];
                }
            }
            ?>
            <li <?php if ($key == $order_by): ?>class="meta_data_filter_order_<?php echo strtolower($order) ?>"<?php endif; ?>>
                <a href="<?php echo $url ?>&order_by=<?php echo $key ?>&order=<?php echo($order == 'ASC' ? 'DESC' : 'ASC'); ?>"><?php _e($title); ?></a>
            </li>
        <?php endforeach; ?>
    </ul><br />
    <?php
    
 endif;

