<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>

<table class="form-table">
    <tbody>

        <tr valign="top">
            <th scope="row"><?php _e("Link", 'meta-data-filter') ?><br></th>
            <td>
                <fieldset>
                    <?php echo home_url(); ?>/<?php echo $mdf_link_seo_prefix ?>/<?php echo $post_id ?>/<?php echo $mdf_link_seo_suffix ?>
                </fieldset>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e("Link SEO prefix", 'meta-data-filter') ?><br></th>
            <td>
                <fieldset>
                    <select name="mdf_link_seo_prefix">
                        <?php foreach(MDF_Marketing::get_link_prefixes() as $prefix) : ?>
                            <option <?php echo selected($mdf_link_seo_prefix, $prefix) ?> value="<?php echo $prefix ?>"><?php echo $prefix ?></option>
                        <?php endforeach; ?>
                    </select>
                </fieldset>
            </td>
        </tr>


        <tr valign="top">
            <th scope="row"><?php _e("Link SEO suffix", 'meta-data-filter') ?><br></th>
            <td>
                <fieldset>
                    <input type="text" name="mdf_link_seo_suffix" value="<?php echo $mdf_link_seo_suffix ?>" placeholder="<?php echo home_url(); ?>/mdf/<?php echo $post_id ?>/my-link-seo-suffix-for-google" class="text" style="width: 90%;" />
                </fieldset>
            </td>
        </tr>


    </tbody>
</table>












</tbody>
</table>