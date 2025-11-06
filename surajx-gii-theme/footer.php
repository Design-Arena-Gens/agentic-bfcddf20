<?php
/**
 * Footer Template
 *
 * @package Surajx_GII_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

</main><!-- #main-content -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'surajx-gii-theme'); ?></p>

            <?php if (has_nav_menu('footer')) : ?>
                <nav class="footer-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class'     => 'footer-menu',
                        'container'      => false,
                        'depth'          => 1,
                    ));
                    ?>
                </nav>
            <?php else : ?>
                <nav class="footer-navigation">
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php _e('Privacy Policy', 'surajx-gii-theme'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/terms-of-service')); ?>"><?php _e('Terms of Service', 'surajx-gii-theme'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact', 'surajx-gii-theme'); ?></a></li>
                    </ul>
                </nav>
            <?php endif; ?>

            <p class="powered-by">
                <?php printf(
                    __('Powered by %s', 'surajx-gii-theme'),
                    '<a href="' . esc_url(__('https://wordpress.org/', 'surajx-gii-theme')) . '">WordPress</a>'
                ); ?>
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
