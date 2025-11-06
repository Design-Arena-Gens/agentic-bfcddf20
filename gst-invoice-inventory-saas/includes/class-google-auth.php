<?php
/**
 * Google Authentication Class
 *
 * @package GII_SaaS
 */

namespace GII_SaaS;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Google OAuth authentication
 */
class Google_Auth {
    /**
     * Verify Google credential token
     *
     * @param string $credential JWT credential from Google
     * @return array|false User data or false on failure
     */
    public static function verify_credential($credential) {
        $client_id = get_option('gii_google_client_id', '');

        if (empty($client_id)) {
            return false;
        }

        // Decode JWT without verification (for demo purposes)
        // In production, use Google API Client Library to verify signature
        $parts = explode('.', $credential);

        if (count($parts) !== 3) {
            return false;
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

        if (!$payload || !isset($payload['email'])) {
            return false;
        }

        // Verify audience (client ID)
        if (isset($payload['aud']) && $payload['aud'] !== $client_id) {
            return false;
        }

        // Verify expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return array(
            'email'         => sanitize_email($payload['email']),
            'name'          => sanitize_text_field($payload['name'] ?? ''),
            'given_name'    => sanitize_text_field($payload['given_name'] ?? ''),
            'family_name'   => sanitize_text_field($payload['family_name'] ?? ''),
            'picture'       => esc_url_raw($payload['picture'] ?? ''),
            'google_id'     => sanitize_text_field($payload['sub'] ?? ''),
            'email_verified' => isset($payload['email_verified']) && $payload['email_verified'],
        );
    }

    /**
     * Authenticate user with Google credentials
     *
     * @param string $credential JWT credential from Google
     * @return int|false User ID or false on failure
     */
    public static function authenticate($credential) {
        $user_data = self::verify_credential($credential);

        if (!$user_data || !$user_data['email_verified']) {
            return false;
        }

        // Check if user exists
        $user = get_user_by('email', $user_data['email']);

        if ($user) {
            // User exists, update Google ID if not set
            if (empty(get_user_meta($user->ID, 'gii_google_id', true))) {
                update_user_meta($user->ID, 'gii_google_id', $user_data['google_id']);
            }

            // Update last login
            update_user_meta($user->ID, 'gii_last_google_login', current_time('mysql'));

            // Log in user
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true);

            return $user->ID;
        }

        // Create new user
        $username = self::generate_username($user_data['email'], $user_data['name']);

        $user_id = wp_create_user(
            $username,
            wp_generate_password(20, true, true),
            $user_data['email']
        );

        if (is_wp_error($user_id)) {
            return false;
        }

        // Set user role
        $user = new \WP_User($user_id);
        $user->set_role('subscriber');

        // Update user meta
        wp_update_user(array(
            'ID'           => $user_id,
            'display_name' => $user_data['name'],
            'first_name'   => $user_data['given_name'],
            'last_name'    => $user_data['family_name'],
        ));

        // Save Google data
        update_user_meta($user_id, 'gii_google_id', $user_data['google_id']);
        update_user_meta($user_id, 'gii_google_picture', $user_data['picture']);
        update_user_meta($user_id, 'gii_last_google_login', current_time('mysql'));

        // Log in user
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);

        return $user_id;
    }

    /**
     * Generate unique username from email
     *
     * @param string $email Email address
     * @param string $name Full name
     * @return string Username
     */
    private static function generate_username($email, $name = '') {
        // Try using name first
        if (!empty($name)) {
            $username = sanitize_user(strtolower(str_replace(' ', '', $name)));
            if (!username_exists($username)) {
                return $username;
            }
        }

        // Use email prefix
        $email_parts = explode('@', $email);
        $username = sanitize_user($email_parts[0]);

        // If username exists, append number
        if (username_exists($username)) {
            $i = 1;
            while (username_exists($username . $i)) {
                $i++;
            }
            $username = $username . $i;
        }

        return $username;
    }

    /**
     * Disconnect Google account
     *
     * @param int $user_id User ID
     * @return bool True on success, false on failure
     */
    public static function disconnect($user_id) {
        delete_user_meta($user_id, 'gii_google_id');
        delete_user_meta($user_id, 'gii_google_picture');

        return true;
    }

    /**
     * Check if user has Google account connected
     *
     * @param int $user_id User ID
     * @return bool True if connected, false otherwise
     */
    public static function is_connected($user_id) {
        $google_id = get_user_meta($user_id, 'gii_google_id', true);
        return !empty($google_id);
    }
}
