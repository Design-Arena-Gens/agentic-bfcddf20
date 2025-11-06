<?php
/**
 * Header Template
 *
 * @package Surajx_GII_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="header-container">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
                <?php bloginfo('name'); ?>
            </a>

            <nav class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'menu',
                    'container'      => false,
                    'fallback_cb'    => function() {
                        echo '<ul class="menu">
                            <li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'surajx-gii-theme') . '</a></li>
                            <li><a href="' . esc_url(home_url('/pricing')) . '">' . __('Pricing', 'surajx-gii-theme') . '</a></li>';

                        if (is_user_logged_in()) {
                            echo '<li><a href="' . esc_url(home_url('/account')) . '">' . __('Dashboard', 'surajx-gii-theme') . '</a></li>
                                  <li><a href="' . esc_url(wp_logout_url(home_url('/'))) . '">' . __('Logout', 'surajx-gii-theme') . '</a></li>';
                        } else {
                            echo '<li><a href="' . esc_url(home_url('/login')) . '">' . __('Login', 'surajx-gii-theme') . '</a></li>
                                  <li><a href="' . esc_url(home_url('/register')) . '">' . __('Sign Up', 'surajx-gii-theme') . '</a></li>';
                        }

                        echo '</ul>';
                    }
                ));
                ?>
            </nav>
        </div>
    </div>
</header>

<main id="main-content" class="site-content">
