<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
/*
  Template Name: Template MetaData Filter
 */
if (class_exists('MetaDataFilterPage')) {
    wp_enqueue_style('meta_data_filter_front', MetaDataFilterCore::get_application_uri() . 'css/front.css');
    wp_enqueue_script('meta_data_filter_widget', MetaDataFilterCore::get_application_uri() . 'js/front.js', array('jquery'));
}

get_header();
?>

<!-- - - - - - - - - - - - Entry - - - - - - - - - - - - - - -->
<?php if (class_exists('MetaDataFilterPage')) echo MetaDataFilterPage::draw_sort_by_filter(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php
        the_content();
        wp_reset_query();
    endwhile;
endif;
?>
<!-- - - - - - - - - - - - end Entry - - - - - - - - - - - - - - -->

<?php
get_footer();


