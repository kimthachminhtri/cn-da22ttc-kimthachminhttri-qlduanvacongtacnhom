<?php
/**
 * Base Controller
 */

namespace App\Controllers;

use Core\View;
use Core\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

abstract class BaseController
{
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        View::render($view, $data, $layout);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/php/index.php';
        $this->redirect($referer);
    }

    protected function success(string $message): void
    {
        Session::flash('success', $message);
    }

    protected function error(string $message): void
    {
        Session::flash('error', $message);
    }

    protected function userId(): ?string
    {
        return AuthMiddleware::userId();
    }

    protected function userRole(): string
    {
        return AuthMiddleware::userRole();
    }

    protected function can(string $permission): bool
    {
        return PermissionMiddleware::can($permission);
    }

    protected function isAdmin(): bool
    {
        return $this->userRole() === 'admin';
    }

    protected function isManager(): bool
    {
        return in_array($this->userRole(), ['admin', 'manager']);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function validate(array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $this->input($field);
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $r) {
                if ($r === 'required' && empty($value)) {
                    $errors[$field] = "Trường {$field} là bắt buộc";
                    break;
                }
                
                if (str_starts_with($r, 'min:')) {
                    $min = (int) substr($r, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "Trường {$field} phải có ít nhất {$min} ký tự";
                        break;
                    }
                }
                
                // NEW: Max length validation
                if (str_starts_with($r, 'max:')) {
                    $max = (int) substr($r, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "Trường {$field} không được vượt quá {$max} ký tự";
                        break;
                    }
                }
                
                if ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Email không hợp lệ";
                    break;
                }
                
                // NEW: Date must be today or future
                if ($r === 'date_future' && !empty($value)) {
                    if (strtotime($value) < strtotime('today')) {
                        $errors[$field] = "Ngày phải từ hôm nay trở đi";
                        break;
                    }
                }
                
                // NEW: Date must be in past
                if ($r === 'date_past' && !empty($value)) {
                    if (strtotime($value) > strtotime('today')) {
                        $errors[$field] = "Ngày phải trong quá khứ";
                        break;
                    }
                }
                
                // NEW: Numeric validation
                if ($r === 'numeric' && !empty($value) && !is_numeric($value)) {
                    $errors[$field] = "Trường {$field} phải là số";
                    break;
                }
            }
        }
        
        return $errors;
    }
}
