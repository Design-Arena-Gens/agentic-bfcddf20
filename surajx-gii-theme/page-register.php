<?php
/**
 * Template Name: Register Page
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

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_user'])) {
    // Verify nonce
    if (!isset($_POST['register_nonce']) || !wp_verify_nonce($_POST['register_nonce'], 'user_registration')) {
        $error = __('Security check failed.', 'surajx-gii-theme');
    } else {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $error = __('All fields are required.', 'surajx-gii-theme');
        } elseif ($password !== $confirm_password) {
            $error = __('Passwords do not match.', 'surajx-gii-theme');
        } elseif (username_exists($username)) {
            $error = __('Username already exists.', 'surajx-gii-theme');
        } elseif (email_exists($email)) {
            $error = __('Email already registered.', 'surajx-gii-theme');
        } elseif (!is_email($email)) {
            $error = __('Invalid email address.', 'surajx-gii-theme');
        } else {
            // Create user
            $user_id = wp_create_user($username, $password, $email);

            if (!is_wp_error($user_id)) {
                // Set user role
                $user = new WP_User($user_id);
                $user->set_role('subscriber');

                // Redirect to login
                wp_redirect(home_url('/login?registered=success'));
                exit;
            } else {
                $error = $user_id->get_error_message();
            }
        }
    }
}

get_header();
?>

<div class="auth-container">
    <h2><?php _e('Create Your Account', 'surajx-gii-theme'); ?></h2>

    <?php if (isset($error)) : ?>
        <div class="alert alert-error"><?php echo esc_html($error); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <?php wp_nonce_field('user_registration', 'register_nonce'); ?>

        <div class="form-group">
            <label for="username"><?php _e('Username', 'surajx-gii-theme'); ?></label>
            <input type="text" name="username" id="username" required
                   value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="email"><?php _e('Email Address', 'surajx-gii-theme'); ?></label>
            <input type="email" name="email" id="email" required
                   value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password"><?php _e('Password', 'surajx-gii-theme'); ?></label>
            <input type="password" name="password" id="password" required minlength="8">
            <small style="color: #6b7280; font-size: 0.875rem;">
                <?php _e('Minimum 8 characters', 'surajx-gii-theme'); ?>
            </small>
        </div>

        <div class="form-group">
            <label for="confirm_password"><?php _e('Confirm Password', 'surajx-gii-theme'); ?></label>
            <input type="password" name="confirm_password" id="confirm_password" required minlength="8">
        </div>

        <button type="submit" name="register_user" class="btn btn-primary" style="width: 100%;">
            <?php _e('Sign Up', 'surajx-gii-theme'); ?>
        </button>
    </form>

    <div class="divider">
        <span><?php _e('OR', 'surajx-gii-theme'); ?></span>
    </div>

    <?php echo do_shortcode('[gii_google_signin]'); ?>

    <p style="text-align: center; margin-top: 1.5rem;">
        <?php _e('Already have an account?', 'surajx-gii-theme'); ?>
        <a href="<?php echo esc_url(home_url('/login')); ?>"><?php _e('Login', 'surajx-gii-theme'); ?></a>
    </p>

    <p style="text-align: center; margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
        <?php _e('By signing up, you agree to our', 'surajx-gii-theme'); ?>
        <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>"><?php _e('Terms of Service', 'surajx-gii-theme'); ?></a>
        <?php _e('and', 'surajx-gii-theme'); ?>
        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php _e('Privacy Policy', 'surajx-gii-theme'); ?></a>
    </p>
</div>

<?php
get_footer();
