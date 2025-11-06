<?php
/**
 * Template Name: Pricing Page
 *
 * @package Surajx_GII_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="pricing-section">
    <div class="container">
        <h1 style="text-align: center; font-size: 2.5rem; margin-bottom: 1rem;">
            <?php _e('Simple, Transparent Pricing', 'surajx-gii-theme'); ?>
        </h1>
        <p style="text-align: center; font-size: 1.125rem; color: #6b7280; margin-bottom: 3rem;">
            <?php _e('Choose the plan that fits your business needs', 'surajx-gii-theme'); ?>
        </p>

        <div class="pricing-grid">
            <!-- Free Plan -->
            <div class="pricing-card">
                <h3><?php _e('Free', 'surajx-gii-theme'); ?></h3>
                <div class="price">₹0<span style="font-size: 1rem; color: #6b7280;">/<?php _e('month', 'surajx-gii-theme'); ?></span></div>
                <ul>
                    <li><?php _e('Up to 10 invoices/month', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('5 products', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Basic GST compliance', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Email support', 'surajx-gii-theme'); ?></li>
                </ul>
                <?php if (is_user_logged_in()) : ?>
                    <button class="btn btn-primary" disabled><?php _e('Current Plan', 'surajx-gii-theme'); ?></button>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-primary">
                        <?php _e('Get Started', 'surajx-gii-theme'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Starter Plan -->
            <div class="pricing-card" style="border: 2px solid #2563eb; transform: scale(1.05);">
                <div style="background: #2563eb; color: white; padding: 0.5rem; margin: -2rem -2rem 1rem; border-radius: 0.5rem 0.5rem 0 0;">
                    <?php _e('Most Popular', 'surajx-gii-theme'); ?>
                </div>
                <h3><?php _e('Starter', 'surajx-gii-theme'); ?></h3>
                <div class="price">₹499<span style="font-size: 1rem; color: #6b7280;">/<?php _e('month', 'surajx-gii-theme'); ?></span></div>
                <ul>
                    <li><?php _e('Unlimited invoices', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('50 products', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Full GST compliance', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Inventory management', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Priority email support', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Basic reports', 'surajx-gii-theme'); ?></li>
                </ul>
                <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-primary">
                    <?php _e('Get Started', 'surajx-gii-theme'); ?>
                </a>
            </div>

            <!-- Business Plan -->
            <div class="pricing-card">
                <h3><?php _e('Business', 'surajx-gii-theme'); ?></h3>
                <div class="price">₹999<span style="font-size: 1rem; color: #6b7280;">/<?php _e('month', 'surajx-gii-theme'); ?></span></div>
                <ul>
                    <li><?php _e('Unlimited invoices', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Unlimited products', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Full GST compliance', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Advanced inventory management', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Multi-user access', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Advanced reports & analytics', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('API access', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Phone & email support', 'surajx-gii-theme'); ?></li>
                </ul>
                <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-primary">
                    <?php _e('Get Started', 'surajx-gii-theme'); ?>
                </a>
            </div>

            <!-- Enterprise Plan -->
            <div class="pricing-card">
                <h3><?php _e('Enterprise', 'surajx-gii-theme'); ?></h3>
                <div class="price"><?php _e('Custom', 'surajx-gii-theme'); ?></div>
                <ul>
                    <li><?php _e('Everything in Business', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Custom integrations', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Dedicated account manager', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('Custom reports', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('SLA guarantee', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('On-premise deployment option', 'surajx-gii-theme'); ?></li>
                    <li><?php _e('24/7 priority support', 'surajx-gii-theme'); ?></li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary">
                    <?php _e('Contact Sales', 'surajx-gii-theme'); ?>
                </a>
            </div>
        </div>

        <div style="margin-top: 4rem; text-align: center; padding: 2rem; background: #f0f9ff; border-radius: 0.5rem;">
            <h3 style="margin-bottom: 1rem;"><?php _e('All Plans Include:', 'surajx-gii-theme'); ?></h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
                <div>✓ <?php _e('GST Compliance', 'surajx-gii-theme'); ?></div>
                <div>✓ <?php _e('Cloud Backup', 'surajx-gii-theme'); ?></div>
                <div>✓ <?php _e('Data Security', 'surajx-gii-theme'); ?></div>
                <div>✓ <?php _e('Regular Updates', 'surajx-gii-theme'); ?></div>
                <div>✓ <?php _e('Mobile Access', 'surajx-gii-theme'); ?></div>
                <div>✓ <?php _e('Free Training', 'surajx-gii-theme'); ?></div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
