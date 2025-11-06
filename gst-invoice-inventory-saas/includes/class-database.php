<?php
/**
 * Database Class
 *
 * @package GII_SaaS
 */

namespace GII_SaaS;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Database management class
 */
class Database {
    /**
     * Create plugin tables
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Products table
        $products_table = $wpdb->prefix . 'gii_products';
        $products_sql = "CREATE TABLE IF NOT EXISTS $products_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            name varchar(255) NOT NULL,
            description text,
            sku varchar(100) NOT NULL,
            hsn_sac varchar(20),
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            tax_rate decimal(5,2) NOT NULL DEFAULT 18.00,
            stock int(11) NOT NULL DEFAULT 0,
            unit varchar(50) DEFAULT 'piece',
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY sku (sku),
            KEY status (status)
        ) $charset_collate;";

        // Invoices table
        $invoices_table = $wpdb->prefix . 'gii_invoices';
        $invoices_sql = "CREATE TABLE IF NOT EXISTS $invoices_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            invoice_number varchar(100) NOT NULL,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255),
            customer_phone varchar(20),
            customer_gst varchar(20),
            customer_address text,
            subtotal decimal(10,2) NOT NULL DEFAULT 0.00,
            tax_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(20) DEFAULT 'draft',
            payment_status varchar(20) DEFAULT 'pending',
            invoice_date date,
            due_date date,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY invoice_number (invoice_number),
            KEY user_id (user_id),
            KEY status (status),
            KEY invoice_date (invoice_date)
        ) $charset_collate;";

        // Invoice items table
        $invoice_items_table = $wpdb->prefix . 'gii_invoice_items';
        $invoice_items_sql = "CREATE TABLE IF NOT EXISTS $invoice_items_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            invoice_id bigint(20) UNSIGNED NOT NULL,
            product_id bigint(20) UNSIGNED,
            product_name varchar(255) NOT NULL,
            description text,
            hsn_sac varchar(20),
            quantity decimal(10,2) NOT NULL DEFAULT 1.00,
            unit varchar(50) DEFAULT 'piece',
            rate decimal(10,2) NOT NULL DEFAULT 0.00,
            tax_rate decimal(5,2) NOT NULL DEFAULT 18.00,
            tax_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY invoice_id (invoice_id),
            KEY product_id (product_id)
        ) $charset_collate;";

        // Customer details table
        $customers_table = $wpdb->prefix . 'gii_customers';
        $customers_sql = "CREATE TABLE IF NOT EXISTS $customers_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            business_name varchar(255),
            gst_number varchar(20),
            pan_number varchar(20),
            address text,
            city varchar(100),
            state varchar(100),
            pincode varchar(10),
            phone varchar(20),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_id (user_id),
            KEY gst_number (gst_number)
        ) $charset_collate;";

        // Settings table
        $settings_table = $wpdb->prefix . 'gii_settings';
        $settings_sql = "CREATE TABLE IF NOT EXISTS $settings_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            setting_key varchar(100) NOT NULL,
            setting_value longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_setting (user_id, setting_key),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($products_sql);
        dbDelta($invoices_sql);
        dbDelta($invoice_items_sql);
        dbDelta($customers_sql);
        dbDelta($settings_sql);
    }

    /**
     * Get table name
     *
     * @param string $table Table name
     * @return string Full table name with prefix
     */
    public static function get_table_name($table) {
        global $wpdb;
        return $wpdb->prefix . 'gii_' . $table;
    }

    /**
     * Insert record
     *
     * @param string $table Table name
     * @param array $data Data to insert
     * @return int|false Insert ID or false on failure
     */
    public static function insert($table, $data) {
        global $wpdb;

        $table_name = self::get_table_name($table);
        $result = $wpdb->insert($table_name, $data);

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update record
     *
     * @param string $table Table name
     * @param array $data Data to update
     * @param array $where Where conditions
     * @return int|false Number of rows updated or false on failure
     */
    public static function update($table, $data, $where) {
        global $wpdb;

        $table_name = self::get_table_name($table);
        return $wpdb->update($table_name, $data, $where);
    }

    /**
     * Delete record
     *
     * @param string $table Table name
     * @param array $where Where conditions
     * @return int|false Number of rows deleted or false on failure
     */
    public static function delete($table, $where) {
        global $wpdb;

        $table_name = self::get_table_name($table);
        return $wpdb->delete($table_name, $where);
    }

    /**
     * Get single record
     *
     * @param string $table Table name
     * @param array $where Where conditions
     * @return object|null Record object or null
     */
    public static function get_row($table, $where) {
        global $wpdb;

        $table_name = self::get_table_name($table);

        $where_clause = array();
        $where_values = array();

        foreach ($where as $key => $value) {
            $where_clause[] = "$key = %s";
            $where_values[] = $value;
        }

        $where_sql = implode(' AND ', $where_clause);
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE $where_sql", $where_values);

        return $wpdb->get_row($query);
    }

    /**
     * Get multiple records
     *
     * @param string $table Table name
     * @param array $where Where conditions
     * @param array $args Query arguments (limit, offset, orderby, order)
     * @return array Array of record objects
     */
    public static function get_results($table, $where = array(), $args = array()) {
        global $wpdb;

        $table_name = self::get_table_name($table);

        $query = "SELECT * FROM $table_name";
        $where_values = array();

        if (!empty($where)) {
            $where_clause = array();
            foreach ($where as $key => $value) {
                $where_clause[] = "$key = %s";
                $where_values[] = $value;
            }
            $query .= ' WHERE ' . implode(' AND ', $where_clause);
        }

        // Order by
        if (isset($args['orderby'])) {
            $order = isset($args['order']) ? strtoupper($args['order']) : 'DESC';
            $query .= " ORDER BY {$args['orderby']} $order";
        }

        // Limit
        if (isset($args['limit'])) {
            $query .= " LIMIT " . intval($args['limit']);

            if (isset($args['offset'])) {
                $query .= " OFFSET " . intval($args['offset']);
            }
        }

        if (!empty($where_values)) {
            $query = $wpdb->prepare($query, $where_values);
        }

        return $wpdb->get_results($query);
    }
}
