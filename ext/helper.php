<?php

final class MDTF_HELPER {

    public static function get_post_featured_image($post_id, $alias) {
        $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'single-post-thumbnail');
        $img_src = $img_src[0];
        $url = self::get_image($img_src, $alias);
        return $url;
    }

    public static function get_image($img_src, $alias) {
        if (empty($alias)) {
            return $img_src;
        }

        $al = explode('x', $alias);
        include_once MetaDataFilterCore::get_application_path() . 'lib/aq_resizer.php';
        $new_img_src = aq_resize($img_src, $al[0], $al[1], true);

        if (!$new_img_src) {
            
        }

        return $new_img_src;
    }

    //we need it when have filter-item by post_title
    public static function mdf_post_title_filter($where = '') {
        if (!isset($_REQUEST['mdf_post_title_request'])) {
            return $where;
        }
        //classes/html.php#361
        $data = explode('^', $_REQUEST['mdf_post_title_request']);
        if (isset($data[2]) AND ! empty($data[2])) {
            $search = trim($data[2]);
            if ($data[0] == 'LIKE') {
                $where .= "AND post_title LIKE '%{$search}%'";
            } else {
                $where .= "AND post_title='{$search}'";
            }
        }
        //$_REQUEST['mdf_post_title_request_lock'] = TRUE;
        return $where;
    }

    //for _price in woocommerce
    public static function get_woo_min_max_price() {
        if (!class_exists('WooCommerce')) {
            return array();
        }
        //+++
        global $wpdb;
        if (sizeof(WC()->query->layered_nav_product_ids) === 0) {
            $min = floor($wpdb->get_var(
                            $wpdb->prepare('
					SELECT min(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE ( meta_key = \'%3$s\' OR meta_key = \'%4$s\' )
					AND meta_value != ""
				', $wpdb->posts, $wpdb->postmeta, '_price', '_min_variation_price')
            ));
            $max = ceil($wpdb->get_var(
                            $wpdb->prepare('
					SELECT max(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key = \'%3$s\'
				', $wpdb->posts, $wpdb->postmeta, '_price')
            ));
        } else {
            $min = floor($wpdb->get_var(
                            $wpdb->prepare('
					SELECT min(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE ( meta_key =\'%3$s\' OR meta_key =\'%4$s\' )
					AND meta_value != ""
					AND (
						%1$s.ID IN (' . implode(',', array_map('absint', WC()->query->layered_nav_product_ids)) . ')
						OR (
							%1$s.post_parent IN (' . implode(',', array_map('absint', WC()->query->layered_nav_product_ids)) . ')
							AND %1$s.post_parent != 0
						)
					)
				', $wpdb->posts, $wpdb->postmeta, '_price', '_min_variation_price'
            )));
            $max = ceil($wpdb->get_var(
                            $wpdb->prepare('
					SELECT max(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key =\'%3$s\'
					AND (
						%1$s.ID IN (' . implode(',', array_map('absint', WC()->query->layered_nav_product_ids)) . ')
						OR (
							%1$s.post_parent IN (' . implode(',', array_map('absint', WC()->query->layered_nav_product_ids)) . ')
							AND %1$s.post_parent != 0
						)
					)
				', $wpdb->posts, $wpdb->postmeta, '_price'
            )));
        }

        return array('min' => $min, 'max' => $max);
    }

}
