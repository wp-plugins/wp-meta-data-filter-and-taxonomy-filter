<?php

//[mdf_chain_menu taxonomy='product_cat' exclude='' show_count=1 post_slug='product' button_title='Watch It' target='_blank']
//11-09-2014
class MDTF_ChainMenu {

    private $button_title = 'GO!';
    private $target = '_self';

    public function init() {
        add_shortcode('mdf_chain_menu', array($this, 'draw_chain_menu'));
        add_action('wp_head', array($this, 'wp_head'), 1);
        add_action('wp_ajax_mdf_get_chain_select', array($this, 'get_chain_select'));
        add_action('wp_ajax_nopriv_mdf_get_chain_select', array($this, 'get_chain_select'));
    }

    public function wp_head() {
        wp_enqueue_script('jquery');
    }

    public function wp_footer() {
        ?>
        <script type="text/javascript">
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            jQuery(function() {
                jQuery.fn.life = function(types, data, fn) {
                    jQuery(this.context).on(types, this.selector, data, fn);
                    return this;
                };

                //+++

                jQuery('.mdf_chain_menu').life('change', function() {
                    var cat_id = jQuery(this).val();
                    var _this = this;
                    if (cat_id > 0) {
                        var data = {
                            action: "mdf_get_chain_select",
                            term_id: cat_id,
                            taxonomy: jQuery(this).data('taxonomy'),
                            show_count: jQuery(this).data('show-count'),
                            post_slug: jQuery(this).data('post-slug'),
                            exclude: jQuery(this).data('exclude')
                        };
                        jQuery.post(ajaxurl, data, function(responce) {
                            jQuery(_this).nextAll('.mdf_chain_menu').remove();
                            jQuery(_this).nextAll('.mdf_chain_menu_posts').remove();
                            jQuery(_this).nextAll('.mdf_chain_menu_post_button').remove();
                            jQuery(_this).after(responce);
                        });
                    } else {
                        jQuery(this).nextAll('.mdf_chain_menu').hide(200, function() {
                            jQuery(this).remove();
                        });
                        jQuery(this).nextAll('.mdf_chain_menu_posts').hide(200, function() {
                            jQuery(this).remove();
                        });
                        jQuery(this).nextAll('.mdf_chain_menu_post_button').hide(200, function() {
                            jQuery(this).remove();
                        });
                    }
                });

                //+++

                jQuery('.mdf_chain_menu_posts').life('change', function() {
                    var link = jQuery(this).val();
                    jQuery(this).nextAll('.mdf_chain_menu_post_button').remove();
                    if (link.length) {
                        jQuery(this).after('<a href="' + link + '" class="button mdf_chain_menu_post_button" target="<?php echo $this->target ?>"><?php echo $this->button_title ?></a>');
                    }
                });

            });
        </script>
        <?php
    }

    //ajax
    public function get_chain_select() {
        $args = array(
            'term_id'=>$_REQUEST['term_id'],
            'taxonomy'=>$_REQUEST['taxonomy'],
            'show_count'=>$_REQUEST['show_count'],
            'post_slug'=>$_REQUEST['post_slug'],
            'exclude'=>$_REQUEST['exclude']
        );
        $childs_ids = get_term_children($_REQUEST['term_id'], $_REQUEST['taxonomy']);
        if(!empty($childs_ids)) {
            $this->generate_tax_select($args);
        } else {
            $this->generate_post_select($_REQUEST['term_id'], $_REQUEST['taxonomy'], $_REQUEST['post_slug']);
        }

        exit;
    }

    private function generate_post_select( $term_id, $taxonomy, $post_slug = 'post' ) {
        $args = array(
            'posts_per_page'=>0,
            'orderby'=>'title',
            'order'=>'ASC',
            'post_type'=>$post_slug,
            'tax_query'=>array(
                'relation'=>'AND',
                array(
                    'taxonomy'=>$taxonomy,
                    'field'=>'term_id',
                    'terms'=>$term_id
                )
            )
        );
        $posts = get_posts($args);
        if(!empty($posts)) {
            ?>
            <select class="mdf_chain_menu_posts">
                <option value=""><?php _e('Select post', 'meta-data-filter') ?></option>
                <?php foreach($posts as $post) : ?>
                    <option value="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }

    //shortcode action
    public function draw_chain_menu( $args ) {
        add_action('wp_footer', array($this, 'wp_footer'), 9999);
        if(!isset($args['term_id'])) {
            $args['term_id'] = 0;
        }
        if(!isset($args['show_count'])) {
            $args['show_count'] = 0;
        }
        if(!isset($args['post_slug'])) {
            $args['post_slug'] = 'post';
        }
        //***
        if(isset($args['button_title'])) {
            $this->button_title = __($args['button_title'],'meta-data-filter');
        }
        if(isset($args['target'])) {
            $this->target = $args['target'];
        }
        //***
        $this->generate_tax_select($args);
    }

    private function generate_tax_select( $args ) {
        $terms = $this->get_terms($args);
        if(!empty($terms)):
            ?>
            <select class="mdf_chain_menu" data-taxonomy="<?php echo $args['taxonomy'] ?>" data-exclude="<?php echo $args['exclude'] ?>" data-show-count="<?php echo $args['show_count'] ?>" data-post-slug="<?php echo $args['post_slug'] ?>">
                <option value="0"><?php _e('Select category', 'meta-data-filter') ?></option>
                <?php foreach($terms as $term) : ?>
                    <option value="<?php echo $term['term_id'] ?>"><?php echo $term['name'] ?><?php if($args['show_count']): ?>&nbsp;(<?php echo $term['count'] ?>)<?php endif; ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        endif;
    }

    private function get_terms( $args ) {
        $cats_objects = get_categories(array(
            'orderby'=>'name',
            'order'=>'ASC',
            'style'=>'list',
            'show_count'=>0,
            'hide_empty'=>1,
            'use_desc_for_title'=>1,
            'child_of'=>0,
            'hierarchical'=>true,
            'title_li'=>'',
            'show_option_none'=>'',
            'number'=>NULL,
            'echo'=>0,
            'depth'=>0,
            'current_category'=>0,
            'pad_counts'=>0,
            'exclude'=>$args['exclude'],
            'taxonomy'=>$args['taxonomy'],
            'walker'=>'Walker_Category'));

        $cats = array();
        if(!empty($cats_objects)) {
            foreach($cats_objects as $value) {
                if($value->category_parent == $args['term_id']) {
                    $cats[$value->term_id] = array();
                    $cats[$value->term_id]['term_id'] = $value->term_id;
                    $cats[$value->term_id]['name'] = $value->name;
                    $cats[$value->term_id]['count'] = $value->count;
                    /*
                      if($get_childs) {
                      $cats[$value->term_id]['childs'] = $this->assemble_terms_childs($cats_objects, $value->term_id);
                      }
                     * 
                     */
                }
            }
        }
        return $cats;
    }

    private function assemble_terms_childs( $cats_objects, $parent_id ) {
        $res = array();
        foreach($cats_objects as $value) {
            if($value->category_parent == $parent_id) {
                $res[$value->term_id]['term_id'] = $value->term_id;
                $res[$value->term_id]['name'] = $value->name;
                $res[$value->term_id]['count'] = $value->count;
                $res[$value->term_id]['childs'] = $this->assemble_terms_childs($cats_objects, $value->term_id);
            }
        }

        return $res;
    }

}

//if(MetaDataFilterCore::get_setting('ext_activate_taxonomy_chain_menu')) {
    add_action('init', array((new MDTF_ChainMenu()), 'init'), 1);
//}
