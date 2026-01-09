<?php
/**
 * CSRF Protection
 * Prevents Cross-Site Request Forgery attacks
 */

namespace Core;

class CSRF
{
    private static string $tokenKey = 'csrf_token';
    
    /**
     * Generate CSRF token
     */
    public static function generate(): string
    {
        if (!Session::has(self::$tokenKey)) {
            Session::set(self::$tokenKey, bin2hex(random_bytes(32)));
        }
        return Session::get(self::$tokenKey);
    }
    
    /**
     * Verify CSRF token
     */
    public static function verify(?string $token): bool
    {
        $storedToken = Session::get(self::$tokenKey, '');
        if (empty($storedToken) || empty($token)) {
            return false;
        }
        return hash_equals($storedToken, $token);
    }
    
    /**
     * Get hidden input field with CSRF token
     */
    public static function input(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(self::generate()) . '">';
    }
    
    /**
     * Get token for JavaScript usage
     */
    public static function token(): string
    {
        return self::generate();
    }
    
    /**
     * Regenerate token (after successful form submission)
     */
    public static function regenerate(): string
    {
        Session::set(self::$tokenKey, bin2hex(random_bytes(32)));
        return Session::get(self::$tokenKey);
    }
    
    /**
     * Get meta tag for AJAX requests
     */
    public static function meta(): string
    {
        return '<meta name="csrf-token" content="' . htmlspecialchars(self::generate()) . '">';
    }
}
