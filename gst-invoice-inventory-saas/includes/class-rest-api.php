<?php
/**
 * REST API Class
 *
 * @package GII_SaaS
 */

namespace GII_SaaS;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST API endpoints
 */
class REST_API {
    /**
     * API namespace
     */
    const NAMESPACE = 'gii-saas/v1';

    /**
     * Register REST routes
     */
    public static function register_routes() {
        // Authentication endpoints
        register_rest_route(self::NAMESPACE, '/auth/google', array(
            'methods'             => 'POST',
            'callback'            => array(__CLASS__, 'google_auth'),
            'permission_callback' => '__return_true',
        ));

        // Products endpoints
        register_rest_route(self::NAMESPACE, '/products', array(
            'methods'             => 'GET',
            'callback'            => array(__CLASS__, 'get_products'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/products', array(
            'methods'             => 'POST',
            'callback'            => array(__CLASS__, 'create_product'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/products/(?P<id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array(__CLASS__, 'get_product'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/products/(?P<id>\d+)', array(
            'methods'             => 'PUT',
            'callback'            => array(__CLASS__, 'update_product'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/products/(?P<id>\d+)', array(
            'methods'             => 'DELETE',
            'callback'            => array(__CLASS__, 'delete_product'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        // Invoices endpoints
        register_rest_route(self::NAMESPACE, '/invoices', array(
            'methods'             => 'GET',
            'callback'            => array(__CLASS__, 'get_invoices'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/invoices', array(
            'methods'             => 'POST',
            'callback'            => array(__CLASS__, 'create_invoice'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/invoices/(?P<id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array(__CLASS__, 'get_invoice'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/invoices/(?P<id>\d+)', array(
            'methods'             => 'PUT',
            'callback'            => array(__CLASS__, 'update_invoice'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/invoices/(?P<id>\d+)', array(
            'methods'             => 'DELETE',
            'callback'            => array(__CLASS__, 'delete_invoice'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        // Account endpoint
        register_rest_route(self::NAMESPACE, '/account', array(
            'methods'             => 'GET',
            'callback'            => array(__CLASS__, 'get_account'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));

        register_rest_route(self::NAMESPACE, '/account', array(
            'methods'             => 'PUT',
            'callback'            => array(__CLASS__, 'update_account'),
            'permission_callback' => array(__CLASS__, 'check_user_logged_in'),
        ));
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public static function check_user_logged_in() {
        return is_user_logged_in();
    }

    /**
     * Google authentication
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function google_auth($request) {
        $credential = $request->get_param('credential');

        if (empty($credential)) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => __('Credential is required', 'gst-invoice-inventory-saas'),
            ), 400);
        }

        $user_id = Google_Auth::authenticate($credential);

        if (!$user_id) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => __('Authentication failed', 'gst-invoice-inventory-saas'),
            ), 401);
        }

        return new \WP_REST_Response(array(
            'success' => true,
            'user_id' => $user_id,
            'message' => __('Authentication successful', 'gst-invoice-inventory-saas'),
        ), 200);
    }

    /**
     * Get products
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function get_products($request) {
        $products = Product::get_all();

        return new \WP_REST_Response($products, 200);
    }

    /**
     * Get single product
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function get_product($request) {
        $product_id = $request->get_param('id');
        $product = Product::get($product_id);

        if (!$product) {
            return new \WP_REST_Response(array(
                'message' => __('Product not found', 'gst-invoice-inventory-saas'),
            ), 404);
        }

        return new \WP_REST_Response($product, 200);
    }

    /**
     * Create product
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function create_product($request) {
        $data = array(
            'name'        => $request->get_param('name'),
            'description' => $request->get_param('description'),
            'sku'         => $request->get_param('sku'),
            'hsn_sac'     => $request->get_param('hsn_sac'),
            'price'       => $request->get_param('price'),
            'tax_rate'    => $request->get_param('tax_rate'),
            'stock'       => $request->get_param('stock'),
            'unit'        => $request->get_param('unit'),
            'status'      => $request->get_param('status'),
        );

        $product_id = Product::create($data);

        if (!$product_id) {
            return new \WP_REST_Response(array(
                'message' => __('Failed to create product', 'gst-invoice-inventory-saas'),
            ), 500);
        }

        $product = Product::get($product_id);

        return new \WP_REST_Response($product, 201);
    }

    /**
     * Update product
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function update_product($request) {
        $product_id = $request->get_param('id');
        $data = $request->get_json_params();

        $success = Product::update($product_id, $data);

        if (!$success) {
            return new \WP_REST_Response(array(
                'message' => __('Failed to update product', 'gst-invoice-inventory-saas'),
            ), 500);
        }

        $product = Product::get($product_id);

        return new \WP_REST_Response($product, 200);
    }

    /**
     * Delete product
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function delete_product($request) {
        $product_id = $request->get_param('id');

        $success = Product::delete($product_id);

        if (!$success) {
            return new \WP_REST_Response(array(
                'message' => __('Failed to delete product', 'gst-invoice-inventory-saas'),
            ), 500);
        }

        return new \WP_REST_Response(array(
            'message' => __('Product deleted successfully', 'gst-invoice-inventory-saas'),
        ), 200);
    }

    /**
     * Get invoices
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function get_invoices($request) {
        $invoices = Invoice::get_all();

        return new \WP_REST_Response($invoices, 200);
    }

    /**
     * Get single invoice
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function get_invoice($request) {
        $invoice_id = $request->get_param('id');
        $invoice = Invoice::get($invoice_id);

        if (!$invoice) {
            return new \WP_REST_Response(array(
                'message' => __('Invoice not found', 'gst-invoice-inventory-saas'),
            ), 404);
        }

        return new \WP_REST_Response($invoice, 200);
    }

    /**
     * Create invoice
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function create_invoice($request) {
        $data = $request->get_json_params();

        $invoice_id = Invoice::create($data);

        if (!$invoice_id) {
            return new \WP_REST_Response(array(
                'message' => __('Failed to create invoice', 'gst-invoice-inventory-saas'),
            ), 500);
        }

        // Recalculate totals
        Invoice::recalculate_totals($invoice_id);

        $invoice = Invoice::get($invoice_id);

        return new \WP_REST_Response($invoice, 201);
    }

    /**
     * Update invoice
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function update_invoice($request) {
        $invoice_id = $request->get_param('id');
        $data = $request->get_json_params();

        $success = Invoice::update($invoice_id, $data);

        if (!$success) {
            return new \WP_REST_Response(array(
                'message' => __('Failed to update invoice', 'gst-invoice-inventory-saas'),
            ), 500);
        }

        $invoice = Invoice::get($invoice_id);

        return new \WP_REST_Response($invoice, 200);
    }

    /**
     * Delete invoice
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function delete_invoice($request) {
        $invoice_id = $request->get_param('id');

        $success = Invoice::delete($invoice_id);

        if (!$success) {
            return new \WP_REST_Response(array(
                'message' => __('Failed to delete invoice', 'gst-invoice-inventory-saas'),
            ), 500);
        }

        return new \WP_REST_Response(array(
            'message' => __('Invoice deleted successfully', 'gst-invoice-inventory-saas'),
        ), 200);
    }

    /**
     * Get account details
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function get_account($request) {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        $customer = Database::get_row('customers', array('user_id' => $user_id));

        return new \WP_REST_Response(array(
            'name'          => $user->display_name,
            'email'         => $user->user_email,
            'business_name' => $customer->business_name ?? '',
            'gst_number'    => $customer->gst_number ?? '',
            'pan_number'    => $customer->pan_number ?? '',
            'address'       => $customer->address ?? '',
            'city'          => $customer->city ?? '',
            'state'         => $customer->state ?? '',
            'pincode'       => $customer->pincode ?? '',
            'phone'         => $customer->phone ?? '',
        ), 200);
    }

    /**
     * Update account details
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public static function update_account($request) {
        $user_id = get_current_user_id();
        $data = $request->get_json_params();

        $customer_data = array(
            'user_id'       => $user_id,
            'business_name' => sanitize_text_field($data['business_name'] ?? ''),
            'gst_number'    => sanitize_text_field($data['gst_number'] ?? ''),
            'pan_number'    => sanitize_text_field($data['pan_number'] ?? ''),
            'address'       => sanitize_textarea_field($data['address'] ?? ''),
            'city'          => sanitize_text_field($data['city'] ?? ''),
            'state'         => sanitize_text_field($data['state'] ?? ''),
            'pincode'       => sanitize_text_field($data['pincode'] ?? ''),
            'phone'         => sanitize_text_field($data['phone'] ?? ''),
        );

        // Check if customer record exists
        $existing = Database::get_row('customers', array('user_id' => $user_id));

        if ($existing) {
            Database::update('customers', $customer_data, array('user_id' => $user_id));
        } else {
            Database::insert('customers', $customer_data);
        }

        return new \WP_REST_Response(array(
            'message' => __('Account updated successfully', 'gst-invoice-inventory-saas'),
        ), 200);
    }
}
