<h2><?php printf(__('Terms items of "%s"', 'meta-data-filter'), $tax_name); ?></h2>
<ul>
    <li>
        <b><?php echo _e('Show as', 'meta-data-filter'); ?></b>:&nbsp;
        <select class="mdf_popup_terms_show_how">
            <?php foreach(self::$tax_items_types as $key=> $value) : ?>
                <option <?php if($show_how == $key): ?>selected<?php endif; ?> value="<?php echo $key ?>"><?php echo $value ?></option>
            <?php endforeach; ?>
        </select>
    </li>
    <li>
        <input class="mdf_popup_terms_select_size" type="hidden" name="" value="1" /><br />
    </li>
</ul>


