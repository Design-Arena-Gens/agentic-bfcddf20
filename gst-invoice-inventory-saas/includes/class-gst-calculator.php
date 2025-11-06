<?php
/**
 * GST Calculator Class
 *
 * @package GII_SaaS
 */

namespace GII_SaaS;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * GST calculation utilities
 */
class GST_Calculator {
    /**
     * Calculate tax amount
     *
     * @param float $amount Base amount
     * @param float $tax_rate Tax rate percentage
     * @return float Tax amount
     */
    public static function calculate_tax($amount, $tax_rate) {
        return round(($amount * $tax_rate) / 100, 2);
    }

    /**
     * Calculate CGST and SGST (for intra-state transactions)
     *
     * @param float $amount Base amount
     * @param float $tax_rate Total tax rate percentage
     * @return array Array with cgst and sgst amounts
     */
    public static function calculate_cgst_sgst($amount, $tax_rate) {
        $total_tax = self::calculate_tax($amount, $tax_rate);
        $cgst = round($total_tax / 2, 2);
        $sgst = round($total_tax / 2, 2);

        return array(
            'cgst' => $cgst,
            'sgst' => $sgst,
            'total' => $cgst + $sgst,
        );
    }

    /**
     * Calculate IGST (for inter-state transactions)
     *
     * @param float $amount Base amount
     * @param float $tax_rate Tax rate percentage
     * @return array Array with igst amount
     */
    public static function calculate_igst($amount, $tax_rate) {
        $igst = self::calculate_tax($amount, $tax_rate);

        return array(
            'igst' => $igst,
            'total' => $igst,
        );
    }

    /**
     * Determine if transaction is inter-state
     *
     * @param string $seller_state Seller state code
     * @param string $buyer_state Buyer state code
     * @return bool True if inter-state, false if intra-state
     */
    public static function is_inter_state($seller_state, $buyer_state) {
        return strtoupper($seller_state) !== strtoupper($buyer_state);
    }

    /**
     * Calculate GST breakdown for invoice
     *
     * @param float $amount Base amount
     * @param float $tax_rate Tax rate percentage
     * @param string $seller_state Seller state code
     * @param string $buyer_state Buyer state code
     * @return array GST breakdown
     */
    public static function calculate_gst_breakdown($amount, $tax_rate, $seller_state = '', $buyer_state = '') {
        $breakdown = array(
            'amount' => $amount,
            'tax_rate' => $tax_rate,
        );

        if (self::is_inter_state($seller_state, $buyer_state) && !empty($seller_state) && !empty($buyer_state)) {
            // Inter-state: IGST
            $igst = self::calculate_igst($amount, $tax_rate);
            $breakdown['type'] = 'igst';
            $breakdown['igst'] = $igst['igst'];
            $breakdown['cgst'] = 0;
            $breakdown['sgst'] = 0;
            $breakdown['total_tax'] = $igst['total'];
        } else {
            // Intra-state: CGST + SGST
            $cgst_sgst = self::calculate_cgst_sgst($amount, $tax_rate);
            $breakdown['type'] = 'cgst_sgst';
            $breakdown['cgst'] = $cgst_sgst['cgst'];
            $breakdown['sgst'] = $cgst_sgst['sgst'];
            $breakdown['igst'] = 0;
            $breakdown['total_tax'] = $cgst_sgst['total'];
        }

        $breakdown['total_amount'] = $amount + $breakdown['total_tax'];

        return $breakdown;
    }

    /**
     * Validate GST number format
     *
     * @param string $gst_number GST number to validate
     * @return bool True if valid, false otherwise
     */
    public static function validate_gst_number($gst_number) {
        // GST format: 2 digits state code + 10 alphanumeric PAN + 1 alpha entity code + 1 alpha/digit + Z + 1 alpha/digit
        // Example: 27AAPFU0939F1ZV
        $pattern = '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';

        return preg_match($pattern, strtoupper($gst_number)) === 1;
    }

    /**
     * Get state code from GST number
     *
     * @param string $gst_number GST number
     * @return string State code
     */
    public static function get_state_code_from_gst($gst_number) {
        if (strlen($gst_number) >= 2) {
            return substr($gst_number, 0, 2);
        }
        return '';
    }

    /**
     * Get GST rate slabs
     *
     * @return array Array of GST rate slabs
     */
    public static function get_gst_rates() {
        return array(
            0    => __('Nil Rate - 0%', 'gst-invoice-inventory-saas'),
            0.25 => __('Special Rate - 0.25%', 'gst-invoice-inventory-saas'),
            3    => __('Essential Goods - 3%', 'gst-invoice-inventory-saas'),
            5    => __('Standard Rate 1 - 5%', 'gst-invoice-inventory-saas'),
            12   => __('Standard Rate 2 - 12%', 'gst-invoice-inventory-saas'),
            18   => __('Standard Rate 3 - 18%', 'gst-invoice-inventory-saas'),
            28   => __('Luxury Goods - 28%', 'gst-invoice-inventory-saas'),
        );
    }

    /**
     * Calculate reverse GST (extract base amount from total including GST)
     *
     * @param float $total_amount Total amount including GST
     * @param float $tax_rate Tax rate percentage
     * @return array Array with base amount and tax amount
     */
    public static function reverse_calculate($total_amount, $tax_rate) {
        $base_amount = round($total_amount / (1 + ($tax_rate / 100)), 2);
        $tax_amount = round($total_amount - $base_amount, 2);

        return array(
            'base_amount' => $base_amount,
            'tax_amount' => $tax_amount,
            'tax_rate' => $tax_rate,
        );
    }

    /**
     * Get Indian states with codes
     *
     * @return array Array of state codes and names
     */
    public static function get_indian_states() {
        return array(
            '01' => __('Jammu and Kashmir', 'gst-invoice-inventory-saas'),
            '02' => __('Himachal Pradesh', 'gst-invoice-inventory-saas'),
            '03' => __('Punjab', 'gst-invoice-inventory-saas'),
            '04' => __('Chandigarh', 'gst-invoice-inventory-saas'),
            '05' => __('Uttarakhand', 'gst-invoice-inventory-saas'),
            '06' => __('Haryana', 'gst-invoice-inventory-saas'),
            '07' => __('Delhi', 'gst-invoice-inventory-saas'),
            '08' => __('Rajasthan', 'gst-invoice-inventory-saas'),
            '09' => __('Uttar Pradesh', 'gst-invoice-inventory-saas'),
            '10' => __('Bihar', 'gst-invoice-inventory-saas'),
            '11' => __('Sikkim', 'gst-invoice-inventory-saas'),
            '12' => __('Arunachal Pradesh', 'gst-invoice-inventory-saas'),
            '13' => __('Nagaland', 'gst-invoice-inventory-saas'),
            '14' => __('Manipur', 'gst-invoice-inventory-saas'),
            '15' => __('Mizoram', 'gst-invoice-inventory-saas'),
            '16' => __('Tripura', 'gst-invoice-inventory-saas'),
            '17' => __('Meghalaya', 'gst-invoice-inventory-saas'),
            '18' => __('Assam', 'gst-invoice-inventory-saas'),
            '19' => __('West Bengal', 'gst-invoice-inventory-saas'),
            '20' => __('Jharkhand', 'gst-invoice-inventory-saas'),
            '21' => __('Odisha', 'gst-invoice-inventory-saas'),
            '22' => __('Chhattisgarh', 'gst-invoice-inventory-saas'),
            '23' => __('Madhya Pradesh', 'gst-invoice-inventory-saas'),
            '24' => __('Gujarat', 'gst-invoice-inventory-saas'),
            '26' => __('Dadra and Nagar Haveli and Daman and Diu', 'gst-invoice-inventory-saas'),
            '27' => __('Maharashtra', 'gst-invoice-inventory-saas'),
            '29' => __('Karnataka', 'gst-invoice-inventory-saas'),
            '30' => __('Goa', 'gst-invoice-inventory-saas'),
            '31' => __('Lakshadweep', 'gst-invoice-inventory-saas'),
            '32' => __('Kerala', 'gst-invoice-inventory-saas'),
            '33' => __('Tamil Nadu', 'gst-invoice-inventory-saas'),
            '34' => __('Puducherry', 'gst-invoice-inventory-saas'),
            '35' => __('Andaman and Nicobar Islands', 'gst-invoice-inventory-saas'),
            '36' => __('Telangana', 'gst-invoice-inventory-saas'),
            '37' => __('Andhra Pradesh', 'gst-invoice-inventory-saas'),
            '38' => __('Ladakh', 'gst-invoice-inventory-saas'),
        );
    }
}
