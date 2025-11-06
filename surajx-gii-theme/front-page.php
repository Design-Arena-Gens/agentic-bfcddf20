<?php
/**
 * Front Page Template
 *
 * @package Surajx_GII_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<section class="hero-section">
    <div class="container">
        <h1><?php _e('GST Invoice & Inventory Management', 'surajx-gii-theme'); ?></h1>
        <p><?php _e('Complete SaaS solution for Indian businesses. Generate GST-compliant invoices, manage inventory, and grow your business.', 'surajx-gii-theme'); ?></p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(home_url('/account')); ?>" class="btn btn-primary">
                    <?php _e('Go to Dashboard', 'surajx-gii-theme'); ?>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-primary">
                    <?php _e('Start Free Trial', 'surajx-gii-theme'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/pricing')); ?>" class="btn btn-secondary">
                    <?php _e('View Pricing', 'surajx-gii-theme'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="features-section" style="padding: 4rem 0; background: white;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 3rem; font-size: 2rem;"><?php _e('Features', 'surajx-gii-theme'); ?></h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div style="text-align: center; padding: 2rem;">
                <h3 style="color: #2563eb; margin-bottom: 1rem;"><?php _e('GST Compliant Invoices', 'surajx-gii-theme'); ?></h3>
                <p><?php _e('Generate professional, GST-compliant invoices in seconds. Support for IGST, CGST, and SGST calculations.', 'surajx-gii-theme'); ?></p>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <h3 style="color: #2563eb; margin-bottom: 1rem;"><?php _e('Inventory Management', 'surajx-gii-theme'); ?></h3>
                <p><?php _e('Track your products, manage stock levels, and get alerts when inventory runs low.', 'surajx-gii-theme'); ?></p>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <h3 style="color: #2563eb; margin-bottom: 1rem;"><?php _e('Real-time Reports', 'surajx-gii-theme'); ?></h3>
                <p><?php _e('Get instant insights into your business with comprehensive reports and analytics.', 'surajx-gii-theme'); ?></p>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <h3 style="color: #2563eb; margin-bottom: 1rem;"><?php _e('Multi-language Support', 'surajx-gii-theme'); ?></h3>
                <p><?php _e('Available in English and Hindi. More languages coming soon.', 'surajx-gii-theme'); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section" style="padding: 4rem 0; background: #f9fafb; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2rem; margin-bottom: 1rem;"><?php _e('Ready to Get Started?', 'surajx-gii-theme'); ?></h2>
        <p style="font-size: 1.125rem; margin-bottom: 2rem; color: #6b7280;">
            <?php _e('Join thousands of businesses already using our platform.', 'surajx-gii-theme'); ?>
        </p>
        <?php if (!is_user_logged_in()) : ?>
            <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-primary">
                <?php _e('Sign Up Now', 'surajx-gii-theme'); ?>
            </a>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
