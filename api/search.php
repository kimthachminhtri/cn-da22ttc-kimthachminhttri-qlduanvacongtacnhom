<?php
/**
 * API: Global Search
 * Tìm kiếm projects, tasks, documents, users
 */

require_once __DIR__ . '/../bootstrap.php';

use Core\Database;
use Core\Session;
use App\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::handle();

$query = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'all'; // all, projects, tasks, documents, users
$limit = min((int)($_GET['limit'] ?? 10), 50);

// Special case: get all users for member selection
if ($type === 'users' && empty($query)) {
    try {
        $db = \Core\Database::getInstance();
        $users = $db->fetchAll(
            "SELECT id, full_name, email, avatar_url, position, department, role
             FROM users 
             WHERE is_active = 1
             ORDER BY full_name ASC
             LIMIT 100"
        );
        echo json_encode([
            'success' => true,
            'data' => array_map(function($u) {
                return [
                    'id' => $u['id'],
                    'full_name' => $u['full_name'],
                    'email' => $u['email'],
                    'avatar' => $u['avatar_url'],
                    'position' => $u['position'],
                    'department' => $u['department'],
                    'role' => $u['role'],
                ];
            }, $users),
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if (empty($query) || strlen($query) < 2) {
    echo json_encode([
        'success' => true,
        'data' => [
            'projects' => [],
            'tasks' => [],
            'documents' => [],
            'users' => [],
        ]
    ]);
    exit;
}

try {
    $db = \Core\Database::getInstance();
    $results = [
        'projects' => [],
        'tasks' => [],
        'documents' => [],
        'users' => [],
    ];
    
    $searchTerm = '%' . $query . '%';
    
    // Search Projects
    if ($type === 'all' || $type === 'projects') {
        $projects = $db->fetchAll(
            "SELECT id, name, description, color, status, progress 
             FROM projects 
             WHERE name LIKE ? OR description LIKE ?
             ORDER BY updated_at DESC
             LIMIT ?",
            [$searchTerm, $searchTerm, $limit]
        );
        $results['projects'] = array_map(function($p) {
            return [
                'id' => $p['id'],
                'name' => $p['name'],
                'description' => mb_substr($p['description'] ?? '', 0, 100),
                'color' => $p['color'],
                'status' => $p['status'],
                'progress' => $p['progress'],
                'url' => 'project-detail.php?id=' . $p['id'],
            ];
        }, $projects);
    }
    
    // Search Tasks
    if ($type === 'all' || $type === 'tasks') {
        $tasks = $db->fetchAll(
            "SELECT t.id, t.title, t.description, t.status, t.priority, 
                    p.name as project_name, p.color as project_color
             FROM tasks t
             LEFT JOIN projects p ON t.project_id = p.id
             WHERE t.title LIKE ? OR t.description LIKE ?
             ORDER BY t.updated_at DESC
             LIMIT ?",
            [$searchTerm, $searchTerm, $limit]
        );
        $results['tasks'] = array_map(function($t) {
            return [
                'id' => $t['id'],
                'title' => $t['title'],
                'description' => mb_substr($t['description'] ?? '', 0, 100),
                'status' => $t['status'],
                'priority' => $t['priority'],
                'project_name' => $t['project_name'],
                'project_color' => $t['project_color'],
                'url' => 'task-detail.php?id=' . $t['id'],
            ];
        }, $tasks);
    }
    
    // Search Documents
    if ($type === 'all' || $type === 'documents') {
        $documents = $db->fetchAll(
            "SELECT id, name, type, mime_type, file_size, updated_at
             FROM documents 
             WHERE name LIKE ?
             ORDER BY updated_at DESC
             LIMIT ?",
            [$searchTerm, $limit]
        );
        $results['documents'] = array_map(function($d) {
            return [
                'id' => $d['id'],
                'name' => $d['name'],
                'type' => $d['type'],
                'mime_type' => $d['mime_type'],
                'file_size' => $d['file_size'],
                'url' => 'documents.php?folder=' . $d['id'],
            ];
        }, $documents);
    }
    
    // Search Users
    if ($type === 'all' || $type === 'users') {
        $users = $db->fetchAll(
            "SELECT id, full_name, email, avatar_url, position, department, role
             FROM users 
             WHERE (full_name LIKE ? OR email LIKE ?) AND is_active = 1
             ORDER BY full_name ASC
             LIMIT ?",
            [$searchTerm, $searchTerm, $limit]
        );
        $results['users'] = array_map(function($u) {
            return [
                'id' => $u['id'],
                'name' => $u['full_name'],
                'email' => $u['email'],
                'avatar' => $u['avatar_url'],
                'position' => $u['position'],
                'department' => $u['department'],
                'role' => $u['role'],
            ];
        }, $users);
    }
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'data' => $results,
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
