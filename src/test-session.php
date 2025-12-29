<?php
/**
 * Test Session - Kiểm tra session
 */
require_once __DIR__ . '/bootstrap.php';

use Core\Session;
use Core\Permission;

echo "<h2>Test Session & Permission</h2>";

// Kiểm tra session
echo "<h3>1. Session Data:</h3>";
echo "<pre>";
echo "user_id: " . var_export(Session::get('user_id'), true) . "\n";
echo "user_role: " . var_export(Session::get('user_role'), true) . "\n";
echo "user_name: " . var_export(Session::get('user_name'), true) . "\n";
echo "</pre>";

// Kiểm tra $_SESSION trực tiếp
echo "<h3>2. Raw \$_SESSION:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Kiểm tra permission
$role = Session::get('user_role', 'guest');
echo "<h3>3. Permission Check (role='$role'):</h3>";
echo "<pre>";
echo "projects.create: " . (Permission::can($role, 'projects.create') ? 'YES' : 'NO') . "\n";
echo "tasks.create: " . (Permission::can($role, 'tasks.create') ? 'YES' : 'NO') . "\n";
echo "</pre>";

// Kiểm tra config
echo "<h3>4. Config Permissions for 'member':</h3>";
echo "<pre>";
$config = require BASE_PATH . '/config/permissions.php';
print_r($config['permissions']['member']);
echo "</pre>";

echo "<p><a href='/php/login.php'>Đăng nhập</a> | <a href='/php/projects.php'>Dự án</a> | <a href='/php/tasks.php'>Công việc</a></p>";
