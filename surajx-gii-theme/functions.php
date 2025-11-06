<?php
/**
 * Surajx GII Theme Functions
 *
 * @package Surajx_GII_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function surajx_gii_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'surajx-gii-theme'),
        'footer'  => __('Footer Menu', 'surajx-gii-theme'),
    ));

    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Load text domain for translations
    load_theme_textdomain('surajx-gii-theme', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'surajx_gii_setup');

/**
 * Enqueue scripts and styles
 */
function surajx_gii_scripts() {
    // Theme stylesheet
    wp_enqueue_style('surajx-gii-style', get_stylesheet_uri(), array(), '1.0.0');

    // Dashboard scripts
    if (is_page_template('page-account.php')) {
        wp_enqueue_script(
            'surajx-gii-dashboard',
            get_template_directory_uri() . '/js/dashboard.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Localize script with REST API data
        wp_localize_script('surajx-gii-dashboard', 'giiData', array(
            'restUrl'   => rest_url('gii-saas/v1/'),
            'nonce'     => wp_create_nonce('wp_rest'),
            'userId'    => get_current_user_id(),
        ));
    }

    // Google Sign-In
    wp_enqueue_script('google-platform', 'https://accounts.google.com/gsi/client', array(), null, false);
}
add_action('wp_enqueue_scripts', 'surajx_gii_scripts');

/**
 * Customer Dashboard Shortcode
 */
function surajx_gii_customer_dashboard_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '<div class="alert alert-info">' .
               __('Please log in to access your dashboard.', 'surajx-gii-theme') .
               ' <a href="' . esc_url(wp_login_url()) . '">' . __('Login', 'surajx-gii-theme') . '</a></div>';
    }

    ob_start();
    ?>
    <div class="dashboard-container">
        <div class="dashboard-tabs">
            <button class="dashboard-tab active" data-tab="products">
                <?php _e('Products', 'surajx-gii-theme'); ?>
            </button>
            <button class="dashboard-tab" data-tab="invoices">
                <?php _e('Invoices', 'surajx-gii-theme'); ?>
            </button>
            <button class="dashboard-tab" data-tab="account">
                <?php _e('Account', 'surajx-gii-theme'); ?>
            </button>
        </div>

        <div class="dashboard-content">
            <!-- Products Tab -->
            <div class="tab-panel active" id="products-panel">
                <div class="panel-header">
                    <h2><?php _e('Products', 'surajx-gii-theme'); ?></h2>
                    <button class="btn btn-primary" id="add-product-btn">
                        <?php _e('Add Product', 'surajx-gii-theme'); ?>
                    </button>
                </div>
                <div id="products-list">
                    <div class="spinner"></div>
                </div>
            </div>

            <!-- Invoices Tab -->
            <div class="tab-panel" id="invoices-panel">
                <div class="panel-header">
                    <h2><?php _e('Invoices', 'surajx-gii-theme'); ?></h2>
                    <button class="btn btn-primary" id="create-invoice-btn">
                        <?php _e('Create Invoice', 'surajx-gii-theme'); ?>
                    </button>
                </div>
                <div id="invoices-list">
                    <div class="spinner"></div>
                </div>
            </div>

            <!-- Account Tab -->
            <div class="tab-panel" id="account-panel">
                <h2><?php _e('Account Settings', 'surajx-gii-theme'); ?></h2>
                <div id="account-details">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gii_customer_dashboard', 'surajx_gii_customer_dashboard_shortcode');

/**
 * Invoice Builder Shortcode
 */
function surajx_gii_invoice_builder_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '<div class="alert alert-info">' .
               __('Please log in to create invoices.', 'surajx-gii-theme') .
               '</div>';
    }

    ob_start();
    ?>
    <div class="invoice-builder">
        <div class="invoice-header">
            <div>
                <h2><?php _e('Create Invoice', 'surajx-gii-theme'); ?></h2>
                <p><?php _e('GST Compliant Invoice Generator', 'surajx-gii-theme'); ?></p>
            </div>
            <div>
                <strong><?php _e('Invoice #:', 'surajx-gii-theme'); ?></strong>
                <span id="invoice-number">INV-<?php echo date('Ymd') . '-' . rand(1000, 9999); ?></span>
            </div>
        </div>

        <form id="invoice-form">
            <div class="form-group">
                <label><?php _e('Customer Name', 'surajx-gii-theme'); ?></label>
                <input type="text" name="customer_name" required>
            </div>

            <div class="form-group">
                <label><?php _e('Customer GST Number', 'surajx-gii-theme'); ?></label>
                <input type="text" name="customer_gst" pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}">
            </div>

            <div class="invoice-items">
                <h3><?php _e('Invoice Items', 'surajx-gii-theme'); ?></h3>
                <div id="items-container">
                    <div class="item-row">
                        <select name="product_id[]" required>
                            <option value=""><?php _e('Select Product', 'surajx-gii-theme'); ?></option>
                        </select>
                        <input type="number" name="quantity[]" placeholder="<?php _e('Qty', 'surajx-gii-theme'); ?>" min="1" required>
                        <input type="number" name="rate[]" placeholder="<?php _e('Rate', 'surajx-gii-theme'); ?>" step="0.01" required>
                        <button type="button" class="btn btn-secondary remove-item"><?php _e('Remove', 'surajx-gii-theme'); ?></button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" id="add-item-btn">
                    <?php _e('Add Item', 'surajx-gii-theme'); ?>
                </button>
            </div>

            <div class="invoice-total">
                <div><?php _e('Subtotal:', 'surajx-gii-theme'); ?> <span id="subtotal">₹0.00</span></div>
                <div><?php _e('GST (18%):', 'surajx-gii-theme'); ?> <span id="gst">₹0.00</span></div>
                <div><?php _e('Total:', 'surajx-gii-theme'); ?> <span id="total">₹0.00</span></div>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php _e('Generate Invoice', 'surajx-gii-theme'); ?>
            </button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gii_invoice_builder', 'surajx_gii_invoice_builder_shortcode');

/**
 * Google Sign-In Button
 */
function surajx_gii_google_signin_button() {
    if (is_user_logged_in()) {
        return '';
    }

    $client_id = get_option('gii_google_client_id', '');
    if (empty($client_id)) {
        return '<p>' . __('Google Sign-In not configured.', 'surajx-gii-theme') . '</p>';
    }

    ob_start();
    ?>
    <div id="g_id_onload"
         data-client_id="<?php echo esc_attr($client_id); ?>"
         data-callback="handleGoogleSignIn"
         data-auto_prompt="false">
    </div>
    <div class="g_id_signin"
         data-type="standard"
         data-size="large"
         data-theme="outline"
         data-text="sign_in_with"
         data-shape="rectangular"
         data-logo_alignment="left">
    </div>

    <script>
    function handleGoogleSignIn(response) {
        fetch('<?php echo esc_url(rest_url('gii-saas/v1/auth/google')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            },
            body: JSON.stringify({
                credential: response.credential
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?php echo esc_url(home_url('/account')); ?>';
            } else {
                alert('<?php _e('Sign in failed. Please try again.', 'surajx-gii-theme'); ?>');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('<?php _e('An error occurred. Please try again.', 'surajx-gii-theme'); ?>');
        });
    }
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('gii_google_signin', 'surajx_gii_google_signin_button');

/**
 * Custom body classes
 */
function surajx_gii_body_classes($classes) {
    if (!is_user_logged_in()) {
        $classes[] = 'logged-out';
    } else {
        $classes[] = 'logged-in';
    }
    return $classes;
}
add_filter('body_class', 'surajx_gii_body_classes');

/**
 * Redirect after login
 */
function surajx_gii_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('subscriber', $user->roles)) {
            return home_url('/account');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'surajx_gii_login_redirect', 10, 3);
