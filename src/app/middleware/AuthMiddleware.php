<?php
/**
 * Authentication Middleware
 */

namespace App\Middleware;

use Core\Session;

class AuthMiddleware
{
    /**
     * Check if user is authenticated
     */
    public static function handle(): bool
    {
        Session::start();
        
        if (!Session::has('user_id')) {
            // Check if this is an API request
            $isApi = self::isApiRequest();
            
            if ($isApi) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Unauthorized. Please login.']);
                exit;
            }
            
            // Store intended URL
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            header('Location: /php/login.php');
            exit;
        }
        
        return true;
    }
    
    /**
     * Check if current request is an API request
     */
    private static function isApiRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        
        // Check if URL contains /api/
        if (strpos($uri, '/api/') !== false) {
            return true;
        }
        
        // Check if request expects JSON
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }
        
        // Check if request sends JSON
        if (strpos($contentType, 'application/json') !== false) {
            return true;
        }
        
        // Check for XHR request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user is guest (not logged in)
     */
    public static function guest(): bool
    {
        Session::start();
        
        if (Session::has('user_id')) {
            header('Location: /php/index.php');
            exit;
        }
        
        return true;
    }

    /**
     * Get current user ID
     */
    public static function userId(): ?string
    {
        Session::start();
        return Session::get('user_id');
    }

    /**
     * Get current user role
     */
    public static function userRole(): string
    {
        Session::start();
        return Session::get('user_role', 'guest');
    }
}
