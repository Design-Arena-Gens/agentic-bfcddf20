<?php
/**
 * Template Name: Login Page
 *
 * @package Surajx_GII_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Redirect logged-in users
if (is_user_logged_in()) {
    wp_redirect(home_url('/account'));
    exit;
}

get_header();
?>

<div class="auth-container">
    <h2><?php _e('Login to Your Account', 'surajx-gii-theme'); ?></h2>

    <?php
    // Display errors
    if (isset($_GET['login']) && $_GET['login'] == 'failed') {
        echo '<div class="alert alert-error">' . __('Invalid username or password.', 'surajx-gii-theme') . '</div>';
    }
    if (isset($_GET['login']) && $_GET['login'] == 'empty') {
        echo '<div class="alert alert-error">' . __('Please enter username and password.', 'surajx-gii-theme') . '</div>';
    }
    if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
        echo '<div class="alert alert-success">' . __('Registration successful! Please login.', 'surajx-gii-theme') . '</div>';
    }
    ?>

    <form method="post" action="<?php echo esc_url(wp_login_url()); ?>">
        <div class="form-group">
            <label for="log"><?php _e('Username or Email', 'surajx-gii-theme'); ?></label>
            <input type="text" name="log" id="log" required>
        </div>

        <div class="form-group">
            <label for="pwd"><?php _e('Password', 'surajx-gii-theme'); ?></label>
            <input type="password" name="pwd" id="pwd" required>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="checkbox" name="rememberme" id="rememberme" value="forever">
            <label for="rememberme" style="margin: 0;"><?php _e('Remember Me', 'surajx-gii-theme'); ?></label>
        </div>

        <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/account')); ?>">

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <?php _e('Login', 'surajx-gii-theme'); ?>
        </button>
    </form>

    <div class="divider">
        <span><?php _e('OR', 'surajx-gii-theme'); ?></span>
    </div>

    <?php echo do_shortcode('[gii_google_signin]'); ?>

    <p style="text-align: center; margin-top: 1.5rem;">
        <a href="<?php echo esc_url(home_url('/forgot-password')); ?>">
            <?php _e('Forgot Password?', 'surajx-gii-theme'); ?>
        </a>
    </p>

    <p style="text-align: center; margin-top: 0.5rem;">
        <?php _e("Don't have an account?", 'surajx-gii-theme'); ?>
        <a href="<?php echo esc_url(home_url('/register')); ?>"><?php _e('Sign Up', 'surajx-gii-theme'); ?></a>
    </p>
</div>

<?php
get_footer();
