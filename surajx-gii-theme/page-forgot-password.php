<?php
/**
 * Template Name: Forgot Password Page
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

// Handle password reset request
$success = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    if (!isset($_POST['reset_nonce']) || !wp_verify_nonce($_POST['reset_nonce'], 'password_reset')) {
        $error = __('Security check failed.', 'surajx-gii-theme');
    } else {
        $email = sanitize_email($_POST['email']);

        if (empty($email)) {
            $error = __('Email address is required.', 'surajx-gii-theme');
        } elseif (!is_email($email)) {
            $error = __('Invalid email address.', 'surajx-gii-theme');
        } else {
            $user = get_user_by('email', $email);

            if ($user) {
                // Generate password reset key
                $reset_key = get_password_reset_key($user);

                if (!is_wp_error($reset_key)) {
                    // Send email
                    $message = __('Someone has requested a password reset for the following account:', 'surajx-gii-theme') . "\r\n\r\n";
                    $message .= network_home_url('/') . "\r\n\r\n";
                    $message .= sprintf(__('Username: %s', 'surajx-gii-theme'), $user->user_login) . "\r\n\r\n";
                    $message .= __('If this was a mistake, just ignore this email and nothing will happen.', 'surajx-gii-theme') . "\r\n\r\n";
                    $message .= __('To reset your password, visit the following address:', 'surajx-gii-theme') . "\r\n\r\n";
                    $message .= network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login') . "\r\n";

                    $title = sprintf(__('[%s] Password Reset'), wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));

                    if (wp_mail($email, wp_specialchars_decode($title), $message)) {
                        $success = true;
                    } else {
                        $error = __('Failed to send email. Please try again.', 'surajx-gii-theme');
                    }
                } else {
                    $error = $reset_key->get_error_message();
                }
            } else {
                // For security, show success even if email doesn't exist
                $success = true;
            }
        }
    }
}

get_header();
?>

<div class="auth-container">
    <h2><?php _e('Reset Your Password', 'surajx-gii-theme'); ?></h2>

    <?php if ($success) : ?>
        <div class="alert alert-success">
            <?php _e('Password reset instructions have been sent to your email address.', 'surajx-gii-theme'); ?>
        </div>
        <p style="text-align: center; margin-top: 1.5rem;">
            <a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-primary">
                <?php _e('Back to Login', 'surajx-gii-theme'); ?>
            </a>
        </p>
    <?php else : ?>
        <?php if (isset($error)) : ?>
            <div class="alert alert-error"><?php echo esc_html($error); ?></div>
        <?php endif; ?>

        <p style="margin-bottom: 1.5rem; color: #6b7280;">
            <?php _e('Enter your email address and we will send you a link to reset your password.', 'surajx-gii-theme'); ?>
        </p>

        <form method="post" action="">
            <?php wp_nonce_field('password_reset', 'reset_nonce'); ?>

            <div class="form-group">
                <label for="email"><?php _e('Email Address', 'surajx-gii-theme'); ?></label>
                <input type="email" name="email" id="email" required
                       value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>">
            </div>

            <button type="submit" name="reset_password" class="btn btn-primary" style="width: 100%;">
                <?php _e('Send Reset Link', 'surajx-gii-theme'); ?>
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem;">
            <a href="<?php echo esc_url(home_url('/login')); ?>">
                <?php _e('Back to Login', 'surajx-gii-theme'); ?>
            </a>
        </p>
    <?php endif; ?>
</div>

<?php
get_footer();
