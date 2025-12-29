<?php
/**
 * Fix password hashes trong database
 * Truy cập: http://localhost/php/database/fix-passwords.php
 */

require_once __DIR__ . '/../includes/config.php';

$message = '';
$error = '';

// Password mặc định
$defaultPassword = 'password123';
$hash = password_hash($defaultPassword, PASSWORD_DEFAULT);

echo "<!DOCTYPE html><html><head><title>Fix Passwords</title>";
echo "<script src='https://cdn.tailwindcss.com'></script></head>";
echo "<body class='bg-gray-100 p-8'>";
echo "<div class='max-w-2xl mx-auto bg-white rounded-xl shadow p-6'>";
echo "<h1 class='text-2xl font-bold mb-4'>Fix Password Hashes</h1>";

echo "<div class='bg-gray-50 p-4 rounded mb-4'>";
echo "<p class='text-sm text-gray-600'>Password: <strong>$defaultPassword</strong></p>";
echo "<p class='text-sm text-gray-600 break-all'>Hash: <code class='bg-gray-200 px-1'>$hash</code></p>";
echo "</div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance();
        
        // Update all users with new password hash
        $stmt = $db->query(
            "UPDATE users SET password_hash = ?",
            [$hash]
        );
        
        $affected = $stmt->rowCount();
        echo "<div class='bg-green-50 border border-green-200 text-green-700 p-4 rounded mb-4'>";
        echo "✓ Đã cập nhật password cho $affected users!";
        echo "</div>";
        
        // Show users
        $users = $db->fetchAll("SELECT id, email, full_name FROM users LIMIT 10");
        echo "<div class='bg-blue-50 p-4 rounded'>";
        echo "<p class='font-semibold mb-2'>Tài khoản có thể đăng nhập:</p>";
        echo "<ul class='text-sm space-y-1'>";
        foreach ($users as $u) {
            echo "<li>• {$u['email']}</li>";
        }
        echo "</ul>";
        echo "<p class='mt-2 text-sm'>Password: <strong>$defaultPassword</strong></p>";
        echo "<a href='../login.php' class='inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700'>Đăng nhập →</a>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='bg-red-50 border border-red-200 text-red-700 p-4 rounded mb-4'>";
        echo "Lỗi: " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
} else {
    // Check database
    try {
        $db = Database::getInstance();
        $count = $db->fetchColumn("SELECT COUNT(*) FROM users");
        echo "<p class='mb-4'>Tìm thấy <strong>$count</strong> users trong database.</p>";
        
        if ($count > 0) {
            echo "<form method='POST'>";
            echo "<button type='submit' class='px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700'>";
            echo "Cập nhật password cho tất cả users";
            echo "</button>";
            echo "</form>";
        } else {
            echo "<p class='text-red-600'>Chưa có user nào. Hãy import seed.sql trước.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='text-red-600'>Lỗi kết nối: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "</div></body></html>";
