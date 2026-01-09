<?php
/**
 * CSRF Protection Helper
 * Bảo vệ chống tấn công Cross-Site Request Forgery
 */

/**
 * Generate CSRF token
 */
function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF input field
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

/**
 * Verify CSRF token
 */
function csrf_verify(?string $token = null): bool
{
    if ($token === null) {
        // Check multiple sources for token
        $token = $_POST['_token'] 
            ?? $_POST['_csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN'] 
            ?? '';
    }
    
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require valid CSRF token or exit
 */
function csrf_require(): void
{
    if (!csrf_verify()) {
        http_response_code(403);
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        } else {
            $_SESSION['error'] = 'Phiên làm việc đã hết hạn. Vui lòng thử lại.';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        }
        exit;
    }
}

/**
 * Regenerate CSRF token (after login, etc.)
 */
function csrf_regenerate(): string
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
