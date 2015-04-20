<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Total
 * @since Total 1.0
 */
?>

<?php
// Main bottom hook
wpex_hook_main_bottom();
?>

</div><!-- #main-content --><?php // main-content opens in header.php  ?>

<?php
// Main after hook
wpex_hook_main_after();
?>

<?php
// Get footer unless disabled
// See functions/footer-display.php
if (wpex_display_footer() == true)
{
    ?>

    <?php
    // Footer before hook
    // The callout is added to this hook by default
    wpex_hook_footer_before();
    ?>

        <?php
        // Display footer Widgets if enabled
        if (wpex_option('footer_widgets', '1'))
        {
            ?>

        <footer id="footer" class="site-footer">
                <?php
                // Footer top hook
                wpex_hook_footer_top();
                ?>
            <div id="footer-inner" class="container row clr">
            <?php
            // Footer innner hook
            // The widgets are added to this hook by default
            // See functions/hooks/hooks-default.php
            wpex_hook_footer_inner();
            ?>
            </div><!-- #footer-widgets -->
        <?php
        // Footer bottom hook
        wpex_hook_footer_bottom();
        ?>
        </footer><!-- #footer -->

    <?php } // End disable widgets check  ?>

    <?php
    // Footer after hook
    // The footer bottom area is added to this hook by default
    wpex_hook_footer_after();
    ?>

<?php } ?>

</div><!-- #wrap -->

<?php
// Important WordPress Hook - DO NOT DELETE!
wp_footer();
?>

<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-55313890-1', 'auto');
    ga('send', 'pageview');
</script>
<?php if (class_exists('MetaDataFilter') AND MetaDataFilter::is_page_mdf_data()): ?>
    <script type="text/javascript">
        jQuery(function () {
            //pagination fix
            var pag_links = jQuery('ul.page-numbers').find('li a');
            jQuery.each(pag_links, function (index, a) {

                jQuery(a).attr('href', jQuery(a).attr('href').replace('#038;', '&'));
                var l = jQuery(a).attr('href');
                var res = l.split("page/");
                var p = 1;
                if (res[1] !== undefined) {
                    p = parseInt(res[1]);
                }

                jQuery(a).attr('href', l + '&mdf_page_num=' + p);
            });
        });
    </script>
<?php endif; ?>
</body>
</html>