<?php
/**
 * Plugin Name: GST Invoice Inventory SaaS
 * Plugin URI: https://example.com/gst-invoice-inventory-saas
 * Description: Complete SaaS solution for GST-compliant invoicing and inventory management for Indian businesses. Includes REST API, Google OAuth, multi-language support.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Surajx
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gst-invoice-inventory-saas
 * Domain Path: /languages
 */

namespace GII_SaaS;

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GII_SAAS_VERSION', '1.0.0');
define('GII_SAAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GII_SAAS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GII_SAAS_PLUGIN_FILE', __FILE__);

/**
 * Main Plugin Class
 */
class Plugin {
    /**
     * Plugin instance
     *
     * @var Plugin
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return Plugin
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
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load dependencies
     */
    private function load_dependencies() {
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-database.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-rest-api.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-google-auth.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-invoice.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-product.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-gst-calculator.php';

        if (is_admin()) {
            require_once GII_SAAS_PLUGIN_DIR . 'admin/class-admin.php';
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(GII_SAAS_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(GII_SAAS_PLUGIN_FILE, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    /**
     * Plugin activation
     */
    public function activate() {
        Database::create_tables();
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'gst-invoice-inventory-saas',
            false,
            dirname(plugin_basename(GII_SAAS_PLUGIN_FILE)) . '/languages'
        );
    }

    /**
     * Initialize plugin
     */
    public function init() {
        if (is_admin()) {
            Admin::get_instance();
        }
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        REST_API::register_routes();
    }
}

// Initialize plugin
Plugin::get_instance();
