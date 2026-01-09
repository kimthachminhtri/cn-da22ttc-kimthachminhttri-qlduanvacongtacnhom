<?php
/**
 * API Response Helper - Standardize API responses
 * Sử dụng để đảm bảo tất cả API endpoints trả về format nhất quán
 */

namespace Core;

class ApiResponse
{
    /**
     * Trả về response thành công
     */
    public static function success(mixed $data = null, string $message = 'Success', int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => true,
            'message' => $message,
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Trả về response lỗi
     */
    public static function error(string $message, int $statusCode = 400, ?array $errors = null): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => false,
            'error' => $message,
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Lỗi validation
     */
    public static function validationError(array $errors): void
    {
        self::error('Dữ liệu không hợp lệ', 422, $errors);
    }

    /**
     * Lỗi không tìm thấy
     */
    public static function notFound(string $message = 'Không tìm thấy'): void
    {
        self::error($message, 404);
    }

    /**
     * Lỗi không có quyền
     */
    public static function forbidden(string $message = 'Bạn không có quyền thực hiện hành động này'): void
    {
        self::error($message, 403);
    }

    /**
     * Lỗi chưa đăng nhập
     */
    public static function unauthorized(string $message = 'Vui lòng đăng nhập'): void
    {
        self::error($message, 401);
    }

    /**
     * Lỗi method không được phép
     */
    public static function methodNotAllowed(): void
    {
        self::error('Method not allowed', 405);
    }

    /**
     * Lỗi conflict (optimistic locking)
     */
    public static function conflict(string $message, array $data = []): void
    {
        http_response_code(409);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => false,
            'error' => $message,
            'conflict' => true,
            ...$data,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Lỗi server
     */
    public static function serverError(string $message = 'Đã xảy ra lỗi, vui lòng thử lại'): void
    {
        self::error($message, 500);
    }
}
