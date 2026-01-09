<?php
/**
 * Các hàm tiện ích
 */

/**
 * Format ngày tháng
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format tiền tệ VND
 */
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' ₫';
}

/**
 * Lấy initials từ tên
 */
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        $initials .= mb_substr($word, 0, 1, 'UTF-8');
    }
    return mb_strtoupper(mb_substr($initials, 0, 2, 'UTF-8'));
}

/**
 * Escape HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Kiểm tra trang hiện tại
 */
function isActivePage($page) {
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
    return $currentPage === $page;
}

/**
 * Lấy class cho status
 */
function getStatusClass($status) {
    $classes = [
        'backlog' => 'bg-gray-100 text-gray-800',
        'todo' => 'bg-blue-100 text-blue-800',
        'in-progress' => 'bg-yellow-100 text-yellow-800',
        'review' => 'bg-purple-100 text-purple-800',
        'done' => 'bg-green-100 text-green-800',
        'planning' => 'bg-gray-100 text-gray-800',
        'active' => 'bg-green-100 text-green-800',
        'on_hold' => 'bg-orange-100 text-orange-800',
        'completed' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Lấy tên status tiếng Việt
 */
function getStatusName($status) {
    $names = [
        'backlog' => 'Chờ xử lý',
        'todo' => 'Cần làm',
        'in_progress' => 'Đang làm',
        'in-progress' => 'Đang làm',
        'in_review' => 'Đang xem xét',
        'review' => 'Đang xem xét',
        'done' => 'Hoàn thành',
        'planning' => 'Lên kế hoạch',
        'active' => 'Đang hoạt động',
        'on_hold' => 'Tạm dừng',
        'completed' => 'Đã hoàn thành',
        'cancelled' => 'Đã hủy',
    ];
    return $names[$status] ?? $status;
}

/**
 * Lấy class cho priority
 */
function getPriorityClass($priority) {
    $classes = [
        'low' => 'bg-gray-100 text-gray-800',
        'medium' => 'bg-blue-100 text-blue-800',
        'high' => 'bg-orange-100 text-orange-800',
        'urgent' => 'bg-red-100 text-red-800',
    ];
    return $classes[$priority] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Lấy tên priority tiếng Việt
 */
function getPriorityName($priority) {
    $names = [
        'low' => 'Thấp',
        'medium' => 'Trung bình',
        'high' => 'Cao',
        'urgent' => 'Khẩn cấp',
    ];
    return $names[$priority] ?? $priority;
}

/**
 * Tính thời gian tương đối
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
    if ($diff < 2592000) return floor($diff / 604800) . ' tuần trước';
    
    return formatDate($datetime);
}

/**
 * Đếm tasks theo status
 */
function countTasksByStatus($tasks, $status) {
    return count(array_filter($tasks, fn($t) => $t['status'] === $status));
}

/**
 * Lọc tasks theo project
 */
function getTasksByProject($tasks, $projectId) {
    return array_filter($tasks, fn($t) => $t['projectId'] === $projectId);
}


/**
 * Generate UUID v4
 * Sử dụng thay vì copy-paste code trong các API
 * 
 * @return string UUID string (36 characters)
 */
function generateUUID(): string {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Sanitize string input
 * Loại bỏ HTML tags và escape special characters
 * 
 * @param string $input
 * @return string
 */
function sanitizeInput(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate date format (YYYY-MM-DD)
 * 
 * @param string $date
 * @return bool
 */
function isValidDate(string $date): bool {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return false;
    }
    $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
    return $dateTime && $dateTime->format('Y-m-d') === $date;
}

/**
 * Validate datetime format (YYYY-MM-DD HH:MM:SS)
 * 
 * @param string $datetime
 * @return bool
 */
function isValidDateTime(string $datetime): bool {
    $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    return $dateTime && $dateTime->format('Y-m-d H:i:s') === $datetime;
}

/**
 * Format file size to human readable
 * 
 * @param int $bytes
 * @return string
 */
function formatFileSize(int $bytes): string {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Get color class based on percentage
 * 
 * @param int $percentage
 * @return string
 */
function getProgressColor(int $percentage): string {
    if ($percentage >= 80) return 'bg-green-500';
    if ($percentage >= 50) return 'bg-blue-500';
    if ($percentage >= 25) return 'bg-yellow-500';
    return 'bg-red-500';
}
