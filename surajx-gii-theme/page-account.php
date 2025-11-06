<?php
/**
 * Template Name: Account Dashboard
 *
 * @package Surajx_GII_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Redirect non-logged-in users
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

get_header();
?>

<div class="container" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem;"><?php printf(__('Welcome, %s', 'surajx-gii-theme'), wp_get_current_user()->display_name); ?></h1>

    <?php echo do_shortcode('[gii_customer_dashboard]'); ?>
</div>

<?php
get_footer();
