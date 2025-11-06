<?php
/**
 * Admin Class
 *
 * @package GII_SaaS
 */

namespace GII_SaaS;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin functionality
 */
class Admin {
    /**
     * Admin instance
     *
     * @var Admin
     */
    private static $instance = null;

    /**
     * Get admin instance
     *
     * @return Admin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('GST Invoice & Inventory', 'gst-invoice-inventory-saas'),
            __('GII SaaS', 'gst-invoice-inventory-saas'),
            'manage_options',
            'gii-saas',
            array($this, 'settings_page'),
            'dashicons-clipboard',
            30
        );

        add_submenu_page(
            'gii-saas',
            __('Settings', 'gst-invoice-inventory-saas'),
            __('Settings', 'gst-invoice-inventory-saas'),
            'manage_options',
            'gii-saas',
            array($this, 'settings_page')
        );

        add_submenu_page(
            'gii-saas',
            __('Products', 'gst-invoice-inventory-saas'),
            __('Products', 'gst-invoice-inventory-saas'),
            'manage_options',
            'gii-products',
            array($this, 'products_page')
        );

        add_submenu_page(
            'gii-saas',
            __('Invoices', 'gst-invoice-inventory-saas'),
            __('Invoices', 'gst-invoice-inventory-saas'),
            'manage_options',
            'gii-invoices',
            array($this, 'invoices_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('gii_saas_settings', 'gii_google_client_id');
        register_setting('gii_saas_settings', 'gii_business_name');
        register_setting('gii_saas_settings', 'gii_gst_number');
        register_setting('gii_saas_settings', 'gii_business_address');
        register_setting('gii_saas_settings', 'gii_business_state');
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'gii-') === false) {
            return;
        }

        wp_enqueue_style(
            'gii-admin-style',
            GII_SAAS_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            GII_SAAS_VERSION
        );
    }

    /**
     * Settings page
     */
    public function settings_page() {
        if (isset($_POST['gii_save_settings']) && check_admin_referer('gii_settings_nonce')) {
            update_option('gii_google_client_id', sanitize_text_field($_POST['gii_google_client_id']));
            update_option('gii_business_name', sanitize_text_field($_POST['gii_business_name']));
            update_option('gii_gst_number', sanitize_text_field($_POST['gii_gst_number']));
            update_option('gii_business_address', sanitize_textarea_field($_POST['gii_business_address']));
            update_option('gii_business_state', sanitize_text_field($_POST['gii_business_state']));

            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'gst-invoice-inventory-saas') . '</p></div>';
        }

        $google_client_id = get_option('gii_google_client_id', '');
        $business_name = get_option('gii_business_name', '');
        $gst_number = get_option('gii_gst_number', '');
        $business_address = get_option('gii_business_address', '');
        $business_state = get_option('gii_business_state', '');
        $states = GST_Calculator::get_indian_states();
        ?>
        <div class="wrap">
            <h1><?php _e('GST Invoice & Inventory SaaS Settings', 'gst-invoice-inventory-saas'); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('gii_settings_nonce'); ?>

                <h2><?php _e('Google OAuth Settings', 'gst-invoice-inventory-saas'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="gii_google_client_id"><?php _e('Google Client ID', 'gst-invoice-inventory-saas'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="gii_google_client_id" id="gii_google_client_id"
                                   value="<?php echo esc_attr($google_client_id); ?>" class="regular-text">
                            <p class="description">
                                <?php _e('Get your Google Client ID from Google Cloud Console', 'gst-invoice-inventory-saas'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('Business Settings', 'gst-invoice-inventory-saas'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="gii_business_name"><?php _e('Business Name', 'gst-invoice-inventory-saas'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="gii_business_name" id="gii_business_name"
                                   value="<?php echo esc_attr($business_name); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gii_gst_number"><?php _e('GST Number', 'gst-invoice-inventory-saas'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="gii_gst_number" id="gii_gst_number"
                                   value="<?php echo esc_attr($gst_number); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gii_business_address"><?php _e('Business Address', 'gst-invoice-inventory-saas'); ?></label>
                        </th>
                        <td>
                            <textarea name="gii_business_address" id="gii_business_address"
                                      rows="3" class="large-text"><?php echo esc_textarea($business_address); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gii_business_state"><?php _e('State', 'gst-invoice-inventory-saas'); ?></label>
                        </th>
                        <td>
                            <select name="gii_business_state" id="gii_business_state">
                                <option value=""><?php _e('Select State', 'gst-invoice-inventory-saas'); ?></option>
                                <?php foreach ($states as $code => $name) : ?>
                                    <option value="<?php echo esc_attr($code); ?>" <?php selected($business_state, $code); ?>>
                                        <?php echo esc_html($name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" name="gii_save_settings" class="button button-primary">
                        <?php _e('Save Settings', 'gst-invoice-inventory-saas'); ?>
                    </button>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * Products page
     */
    public function products_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Products', 'gst-invoice-inventory-saas'); ?></h1>
            <p><?php _e('Products are managed by individual users through their dashboard.', 'gst-invoice-inventory-saas'); ?></p>
            <p><?php _e('To view all products, use the REST API:', 'gst-invoice-inventory-saas'); ?>
               <code><?php echo esc_url(rest_url('gii-saas/v1/products')); ?></code>
            </p>
        </div>
        <?php
    }

    /**
     * Invoices page
     */
    public function invoices_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Invoices', 'gst-invoice-inventory-saas'); ?></h1>
            <p><?php _e('Invoices are managed by individual users through their dashboard.', 'gst-invoice-inventory-saas'); ?></p>
            <p><?php _e('To view all invoices, use the REST API:', 'gst-invoice-inventory-saas'); ?>
               <code><?php echo esc_url(rest_url('gii-saas/v1/invoices')); ?></code>
            </p>
        </div>
        <?php
    }
}
