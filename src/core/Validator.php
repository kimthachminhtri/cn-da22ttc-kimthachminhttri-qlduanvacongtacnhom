<?php
/**
 * Core Validator Class
 * Validation rules for form data
 */

namespace Core;

class Validator
{
    private array $errors = [];
    private array $data = [];
    
    public function validate(array $data, array $rules): array
    {
        $this->data = $data;
        $this->errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $ruleString);
            
            foreach ($ruleList as $rule) {
                $this->applyRule($field, $value, $rule);
                
                // Stop on first error for this field
                if (isset($this->errors[$field])) {
                    break;
                }
            }
        }
        
        return $this->errors;
    }
    
    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $fieldLabel = $this->getFieldLabel($field);
        
        // Parse rule with parameters (e.g., min:6, max:255)
        $params = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $paramStr] = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
        }
        
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || $value === []) {
                    $this->errors[$field] = "{$fieldLabel} là bắt buộc";
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = "{$fieldLabel} không phải email hợp lệ";
                }
                break;
                
            case 'min':
                $min = (int) ($params[0] ?? 0);
                if (!empty($value) && strlen($value) < $min) {
                    $this->errors[$field] = "{$fieldLabel} phải có ít nhất {$min} ký tự";
                }
                break;
                
            case 'max':
                $max = (int) ($params[0] ?? 255);
                if (!empty($value) && strlen($value) > $max) {
                    $this->errors[$field] = "{$fieldLabel} không được vượt quá {$max} ký tự";
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field] = "{$fieldLabel} phải là số";
                }
                break;
                
            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errors[$field] = "{$fieldLabel} phải là số nguyên";
                }
                break;
                
            case 'alpha':
                if (!empty($value) && !ctype_alpha($value)) {
                    $this->errors[$field] = "{$fieldLabel} chỉ được chứa chữ cái";
                }
                break;
                
            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->errors[$field] = "{$fieldLabel} chỉ được chứa chữ cái và số";
                }
                break;
                
            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->errors[$field] = "{$fieldLabel} không phải URL hợp lệ";
                }
                break;
                
            case 'date':
                if (!empty($value) && strtotime($value) === false) {
                    $this->errors[$field] = "{$fieldLabel} không phải ngày hợp lệ";
                }
                break;
                
            case 'in':
                $allowed = $params;
                if (!empty($value) && !in_array($value, $allowed)) {
                    $this->errors[$field] = "{$fieldLabel} phải là một trong: " . implode(', ', $allowed);
                }
                break;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                $confirmValue = $this->data[$confirmField] ?? null;
                if ($value !== $confirmValue) {
                    $this->errors[$field] = "{$fieldLabel} xác nhận không khớp";
                }
                break;
                
            case 'unique':
                // unique:table,column
                $table = $params[0] ?? '';
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;
                
                if (!empty($value) && $table) {
                    $db = Database::getInstance();
                    $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
                    $sqlParams = [$value];
                    
                    if ($exceptId) {
                        $sql .= " AND id != ?";
                        $sqlParams[] = $exceptId;
                    }
                    
                    $count = $db->fetchColumn($sql, $sqlParams);
                    if ($count > 0) {
                        $this->errors[$field] = "{$fieldLabel} đã tồn tại";
                    }
                }
                break;
                
            case 'exists':
                // exists:table,column
                $table = $params[0] ?? '';
                $column = $params[1] ?? 'id';
                
                if (!empty($value) && $table) {
                    $db = Database::getInstance();
                    $count = $db->fetchColumn(
                        "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?",
                        [$value]
                    );
                    if ($count == 0) {
                        $this->errors[$field] = "{$fieldLabel} không tồn tại";
                    }
                }
                break;
                
            case 'regex':
                $pattern = $params[0] ?? '';
                if (!empty($value) && $pattern && !preg_match($pattern, $value)) {
                    $this->errors[$field] = "{$fieldLabel} không đúng định dạng";
                }
                break;
        }
    }
    
    private function getFieldLabel(string $field): string
    {
        $labels = [
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'full_name' => 'Họ tên',
            'title' => 'Tiêu đề',
            'name' => 'Tên',
            'description' => 'Mô tả',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
        ];
        
        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
    
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    public function getFirstError(): ?string
    {
        return $this->errors ? reset($this->errors) : null;
    }
}
