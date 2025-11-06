<?php
/**
 * Invoice Class
 *
 * @package GII_SaaS
 */

namespace GII_SaaS;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Invoice management class
 */
class Invoice {
    /**
     * Create invoice
     *
     * @param array $data Invoice data
     * @return int|false Invoice ID or false on failure
     */
    public static function create($data) {
        // Generate invoice number if not provided
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = self::generate_invoice_number();
        }

        // Sanitize invoice data
        $invoice_data = array(
            'user_id'          => get_current_user_id(),
            'invoice_number'   => sanitize_text_field($data['invoice_number']),
            'customer_name'    => sanitize_text_field($data['customer_name']),
            'customer_email'   => sanitize_email($data['customer_email'] ?? ''),
            'customer_phone'   => sanitize_text_field($data['customer_phone'] ?? ''),
            'customer_gst'     => sanitize_text_field($data['customer_gst'] ?? ''),
            'customer_address' => sanitize_textarea_field($data['customer_address'] ?? ''),
            'subtotal'         => floatval($data['subtotal'] ?? 0),
            'tax_amount'       => floatval($data['tax_amount'] ?? 0),
            'total_amount'     => floatval($data['total_amount'] ?? 0),
            'status'           => sanitize_text_field($data['status'] ?? 'draft'),
            'payment_status'   => sanitize_text_field($data['payment_status'] ?? 'pending'),
            'invoice_date'     => sanitize_text_field($data['invoice_date'] ?? current_time('mysql', false)),
            'due_date'         => sanitize_text_field($data['due_date'] ?? ''),
            'notes'            => sanitize_textarea_field($data['notes'] ?? ''),
        );

        // Validate required fields
        if (empty($invoice_data['customer_name'])) {
            return false;
        }

        // Insert invoice
        $invoice_id = Database::insert('invoices', $invoice_data);

        if (!$invoice_id) {
            return false;
        }

        // Insert invoice items
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                self::add_item($invoice_id, $item);
            }
        }

        return $invoice_id;
    }

    /**
     * Update invoice
     *
     * @param int $invoice_id Invoice ID
     * @param array $data Invoice data
     * @return bool True on success, false on failure
     */
    public static function update($invoice_id, $data) {
        $invoice_data = array();

        if (isset($data['customer_name'])) {
            $invoice_data['customer_name'] = sanitize_text_field($data['customer_name']);
        }
        if (isset($data['customer_email'])) {
            $invoice_data['customer_email'] = sanitize_email($data['customer_email']);
        }
        if (isset($data['customer_phone'])) {
            $invoice_data['customer_phone'] = sanitize_text_field($data['customer_phone']);
        }
        if (isset($data['customer_gst'])) {
            $invoice_data['customer_gst'] = sanitize_text_field($data['customer_gst']);
        }
        if (isset($data['customer_address'])) {
            $invoice_data['customer_address'] = sanitize_textarea_field($data['customer_address']);
        }
        if (isset($data['status'])) {
            $invoice_data['status'] = sanitize_text_field($data['status']);
        }
        if (isset($data['payment_status'])) {
            $invoice_data['payment_status'] = sanitize_text_field($data['payment_status']);
        }
        if (isset($data['notes'])) {
            $invoice_data['notes'] = sanitize_textarea_field($data['notes']);
        }

        $where = array(
            'id'      => $invoice_id,
            'user_id' => get_current_user_id(),
        );

        return Database::update('invoices', $invoice_data, $where) !== false;
    }

    /**
     * Delete invoice
     *
     * @param int $invoice_id Invoice ID
     * @return bool True on success, false on failure
     */
    public static function delete($invoice_id) {
        // Delete invoice items first
        Database::delete('invoice_items', array('invoice_id' => $invoice_id));

        // Delete invoice
        $where = array(
            'id'      => $invoice_id,
            'user_id' => get_current_user_id(),
        );

        return Database::delete('invoices', $where) !== false;
    }

    /**
     * Get invoice by ID
     *
     * @param int $invoice_id Invoice ID
     * @return object|null Invoice object with items or null
     */
    public static function get($invoice_id) {
        $where = array(
            'id'      => $invoice_id,
            'user_id' => get_current_user_id(),
        );

        $invoice = Database::get_row('invoices', $where);

        if ($invoice) {
            $invoice->items = self::get_items($invoice_id);
        }

        return $invoice;
    }

    /**
     * Get all invoices for current user
     *
     * @param array $args Query arguments
     * @return array Array of invoice objects
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

        return Database::get_results('invoices', $where, $query_args);
    }

    /**
     * Add item to invoice
     *
     * @param int $invoice_id Invoice ID
     * @param array $item Item data
     * @return int|false Item ID or false on failure
     */
    public static function add_item($invoice_id, $item) {
        // Calculate amounts
        $quantity = floatval($item['quantity']);
        $rate = floatval($item['rate']);
        $tax_rate = floatval($item['tax_rate'] ?? 18.00);

        $amount = $quantity * $rate;
        $tax_amount = GST_Calculator::calculate_tax($amount, $tax_rate);

        $item_data = array(
            'invoice_id'   => $invoice_id,
            'product_id'   => intval($item['product_id'] ?? 0),
            'product_name' => sanitize_text_field($item['product_name']),
            'description'  => sanitize_textarea_field($item['description'] ?? ''),
            'hsn_sac'      => sanitize_text_field($item['hsn_sac'] ?? ''),
            'quantity'     => $quantity,
            'unit'         => sanitize_text_field($item['unit'] ?? 'piece'),
            'rate'         => $rate,
            'tax_rate'     => $tax_rate,
            'tax_amount'   => $tax_amount,
            'amount'       => $amount + $tax_amount,
        );

        return Database::insert('invoice_items', $item_data);
    }

    /**
     * Get invoice items
     *
     * @param int $invoice_id Invoice ID
     * @return array Array of item objects
     */
    public static function get_items($invoice_id) {
        return Database::get_results('invoice_items', array('invoice_id' => $invoice_id));
    }

    /**
     * Recalculate invoice totals
     *
     * @param int $invoice_id Invoice ID
     * @return bool True on success, false on failure
     */
    public static function recalculate_totals($invoice_id) {
        global $wpdb;

        $items_table = Database::get_table_name('invoice_items');

        $query = $wpdb->prepare(
            "SELECT SUM(quantity * rate) as subtotal, SUM(tax_amount) as tax_amount FROM $items_table WHERE invoice_id = %d",
            $invoice_id
        );

        $totals = $wpdb->get_row($query);

        $invoice_data = array(
            'subtotal'     => floatval($totals->subtotal ?? 0),
            'tax_amount'   => floatval($totals->tax_amount ?? 0),
            'total_amount' => floatval($totals->subtotal ?? 0) + floatval($totals->tax_amount ?? 0),
        );

        $where = array(
            'id'      => $invoice_id,
            'user_id' => get_current_user_id(),
        );

        return Database::update('invoices', $invoice_data, $where) !== false;
    }

    /**
     * Generate invoice number
     *
     * @return string Invoice number
     */
    private static function generate_invoice_number() {
        global $wpdb;

        $table_name = Database::get_table_name('invoices');

        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
            get_current_user_id()
        );

        $count = $wpdb->get_var($query);

        $prefix = 'INV';
        $date = date('Ymd');
        $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "$prefix-$date-$number";
    }

    /**
     * Mark invoice as paid
     *
     * @param int $invoice_id Invoice ID
     * @return bool True on success, false on failure
     */
    public static function mark_as_paid($invoice_id) {
        $invoice_data = array(
            'payment_status' => 'paid',
            'status'         => 'completed',
        );

        $where = array(
            'id'      => $invoice_id,
            'user_id' => get_current_user_id(),
        );

        return Database::update('invoices', $invoice_data, $where) !== false;
    }
}
