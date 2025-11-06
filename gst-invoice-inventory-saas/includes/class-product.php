<?php
/**
 * Product Class
 *
 * @package GII_SaaS
 */

namespace GII_SaaS;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product management class
 */
class Product {
    /**
     * Create product
     *
     * @param array $data Product data
     * @return int|false Product ID or false on failure
     */
    public static function create($data) {
        // Sanitize data
        $product_data = array(
            'user_id'     => get_current_user_id(),
            'name'        => sanitize_text_field($data['name']),
            'description' => sanitize_textarea_field($data['description'] ?? ''),
            'sku'         => sanitize_text_field($data['sku']),
            'hsn_sac'     => sanitize_text_field($data['hsn_sac'] ?? ''),
            'price'       => floatval($data['price']),
            'tax_rate'    => floatval($data['tax_rate'] ?? 18.00),
            'stock'       => intval($data['stock'] ?? 0),
            'unit'        => sanitize_text_field($data['unit'] ?? 'piece'),
            'status'      => sanitize_text_field($data['status'] ?? 'active'),
        );

        // Validate required fields
        if (empty($product_data['name']) || empty($product_data['sku'])) {
            return false;
        }

        return Database::insert('products', $product_data);
    }

    /**
     * Update product
     *
     * @param int $product_id Product ID
     * @param array $data Product data
     * @return bool True on success, false on failure
     */
    public static function update($product_id, $data) {
        // Sanitize data
        $product_data = array();

        if (isset($data['name'])) {
            $product_data['name'] = sanitize_text_field($data['name']);
        }
        if (isset($data['description'])) {
            $product_data['description'] = sanitize_textarea_field($data['description']);
        }
        if (isset($data['sku'])) {
            $product_data['sku'] = sanitize_text_field($data['sku']);
        }
        if (isset($data['hsn_sac'])) {
            $product_data['hsn_sac'] = sanitize_text_field($data['hsn_sac']);
        }
        if (isset($data['price'])) {
            $product_data['price'] = floatval($data['price']);
        }
        if (isset($data['tax_rate'])) {
            $product_data['tax_rate'] = floatval($data['tax_rate']);
        }
        if (isset($data['stock'])) {
            $product_data['stock'] = intval($data['stock']);
        }
        if (isset($data['unit'])) {
            $product_data['unit'] = sanitize_text_field($data['unit']);
        }
        if (isset($data['status'])) {
            $product_data['status'] = sanitize_text_field($data['status']);
        }

        $where = array(
            'id'      => $product_id,
            'user_id' => get_current_user_id(),
        );

        return Database::update('products', $product_data, $where) !== false;
    }

    /**
     * Delete product
     *
     * @param int $product_id Product ID
     * @return bool True on success, false on failure
     */
    public static function delete($product_id) {
        $where = array(
            'id'      => $product_id,
            'user_id' => get_current_user_id(),
        );

        return Database::delete('products', $where) !== false;
    }

    /**
     * Get product by ID
     *
     * @param int $product_id Product ID
     * @return object|null Product object or null
     */
    public static function get($product_id) {
        $where = array(
            'id'      => $product_id,
            'user_id' => get_current_user_id(),
        );

        return Database::get_row('products', $where);
    }

    /**
     * Get all products for current user
     *
     * @param array $args Query arguments
     * @return array Array of product objects
     */
    public static function get_all($args = array()) {
        $where = array(
            'user_id' => get_current_user_id(),
        );

        if (isset($args['status'])) {
            $where['status'] = $args['status'];
        }

        $query_args = array(
            'orderby' => $args['orderby'] ?? 'created_at',
            'order'   => $args['order'] ?? 'DESC',
        );

        if (isset($args['limit'])) {
            $query_args['limit'] = $args['limit'];
        }

        if (isset($args['offset'])) {
            $query_args['offset'] = $args['offset'];
        }

        return Database::get_results('products', $where, $query_args);
    }

    /**
     * Update product stock
     *
     * @param int $product_id Product ID
     * @param int $quantity Quantity to add/subtract (negative for subtract)
     * @return bool True on success, false on failure
     */
    public static function update_stock($product_id, $quantity) {
        global $wpdb;

        $table_name = Database::get_table_name('products');

        $query = $wpdb->prepare(
            "UPDATE $table_name SET stock = stock + %d WHERE id = %d AND user_id = %d",
            $quantity,
            $product_id,
            get_current_user_id()
        );

        return $wpdb->query($query) !== false;
    }

    /**
     * Check if SKU exists
     *
     * @param string $sku SKU to check
     * @param int|null $exclude_id Product ID to exclude from check
     * @return bool True if exists, false otherwise
     */
    public static function sku_exists($sku, $exclude_id = null) {
        global $wpdb;

        $table_name = Database::get_table_name('products');

        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE sku = %s AND user_id = %d",
            $sku,
            get_current_user_id()
        );

        if ($exclude_id) {
            $query .= $wpdb->prepare(" AND id != %d", $exclude_id);
        }

        return $wpdb->get_var($query) > 0;
    }

    /**
     * Get low stock products
     *
     * @param int $threshold Stock threshold
     * @return array Array of product objects
     */
    public static function get_low_stock($threshold = 10) {
        global $wpdb;

        $table_name = Database::get_table_name('products');

        $query = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND stock <= %d AND status = 'active' ORDER BY stock ASC",
            get_current_user_id(),
            $threshold
        );

        return $wpdb->get_results($query);
    }
}
