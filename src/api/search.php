<?php
/**
 * API: Global Search với Full-text Search
 * Tìm kiếm projects, tasks, documents, users
 * 
 * Hỗ trợ:
 * - Full-text search (MATCH AGAINST) cho kết quả chính xác hơn
 * - Fallback về LIKE search nếu full-text không khả dụng
 * - Relevance scoring để sắp xếp kết quả
 */

require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

use Core\Database;
use Core\Session;
use App\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::handle();

$query = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'all'; // all, projects, tasks, documents, users
$limit = min((int)($_GET['limit'] ?? 10), 50);
$searchMode = $_GET['mode'] ?? 'auto'; // auto, fulltext, like

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

/**
 * Check if FULLTEXT index exists for a table
 */
function hasFulltextIndex($db, $table, $indexName) {
    try {
        $result = $db->fetchOne(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ? AND Index_type = 'FULLTEXT'",
            [$indexName]
        );
        return $result !== null;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Prepare search query for FULLTEXT
 * Thêm + trước mỗi từ để yêu cầu tất cả từ phải có mặt
 */
function prepareFulltextQuery($query) {
    $words = preg_split('/\s+/', trim($query));
    $prepared = [];
    foreach ($words as $word) {
        if (strlen($word) >= 2) {
            // Escape special characters và thêm wildcard
            $word = preg_replace('/[+\-><\(\)~*\"@]+/', '', $word);
            if (!empty($word)) {
                $prepared[] = '+' . $word . '*';
            }
        }
    }
    return implode(' ', $prepared);
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
    $fulltextQuery = prepareFulltextQuery($query);
    
    // Search Projects
    if ($type === 'all' || $type === 'projects') {
        $useFulltext = $searchMode !== 'like' && hasFulltextIndex($db, 'projects', 'ft_projects_search');
        
        if ($useFulltext && !empty($fulltextQuery)) {
            $projects = $db->fetchAll(
                "SELECT id, name, description, color, status, progress,
                        MATCH(name, description) AGAINST(? IN BOOLEAN MODE) as relevance
                 FROM projects 
                 WHERE MATCH(name, description) AGAINST(? IN BOOLEAN MODE)
                 ORDER BY relevance DESC, updated_at DESC
                 LIMIT ?",
                [$fulltextQuery, $fulltextQuery, $limit]
            );
        } else {
            $projects = $db->fetchAll(
                "SELECT id, name, description, color, status, progress, 0 as relevance
                 FROM projects 
                 WHERE name LIKE ? OR description LIKE ?
                 ORDER BY updated_at DESC
                 LIMIT ?",
                [$searchTerm, $searchTerm, $limit]
            );
        }
        
        $results['projects'] = array_map(function($p) {
            return [
                'id' => $p['id'],
                'name' => $p['name'],
                'description' => mb_substr($p['description'] ?? '', 0, 100),
                'color' => $p['color'],
                'status' => $p['status'],
                'progress' => $p['progress'],
                'relevance' => round((float)$p['relevance'], 2),
                'url' => 'project-detail.php?id=' . $p['id'],
            ];
        }, $projects);
    }
    
    // Search Tasks
    if ($type === 'all' || $type === 'tasks') {
        $useFulltext = $searchMode !== 'like' && hasFulltextIndex($db, 'tasks', 'ft_tasks_search');
        
        if ($useFulltext && !empty($fulltextQuery)) {
            $tasks = $db->fetchAll(
                "SELECT t.id, t.title, t.description, t.status, t.priority, 
                        p.name as project_name, p.color as project_color,
                        MATCH(t.title, t.description) AGAINST(? IN BOOLEAN MODE) as relevance
                 FROM tasks t
                 LEFT JOIN projects p ON t.project_id = p.id
                 WHERE MATCH(t.title, t.description) AGAINST(? IN BOOLEAN MODE)
                 ORDER BY relevance DESC, t.updated_at DESC
                 LIMIT ?",
                [$fulltextQuery, $fulltextQuery, $limit]
            );
        } else {
            $tasks = $db->fetchAll(
                "SELECT t.id, t.title, t.description, t.status, t.priority, 
                        p.name as project_name, p.color as project_color, 0 as relevance
                 FROM tasks t
                 LEFT JOIN projects p ON t.project_id = p.id
                 WHERE t.title LIKE ? OR t.description LIKE ?
                 ORDER BY t.updated_at DESC
                 LIMIT ?",
                [$searchTerm, $searchTerm, $limit]
            );
        }
        
        $results['tasks'] = array_map(function($t) {
            return [
                'id' => $t['id'],
                'title' => $t['title'],
                'description' => mb_substr($t['description'] ?? '', 0, 100),
                'status' => $t['status'],
                'priority' => $t['priority'],
                'project_name' => $t['project_name'],
                'project_color' => $t['project_color'],
                'relevance' => round((float)$t['relevance'], 2),
                'url' => 'task-detail.php?id=' . $t['id'],
            ];
        }, $tasks);
    }
    
    // Search Documents
    if ($type === 'all' || $type === 'documents') {
        $useFulltext = $searchMode !== 'like' && hasFulltextIndex($db, 'documents', 'ft_documents_search');
        
        if ($useFulltext && !empty($fulltextQuery)) {
            $documents = $db->fetchAll(
                "SELECT id, name, description, type, mime_type, file_size, updated_at,
                        MATCH(name, description) AGAINST(? IN BOOLEAN MODE) as relevance
                 FROM documents 
                 WHERE MATCH(name, description) AGAINST(? IN BOOLEAN MODE)
                 ORDER BY relevance DESC, updated_at DESC
                 LIMIT ?",
                [$fulltextQuery, $fulltextQuery, $limit]
            );
        } else {
            $documents = $db->fetchAll(
                "SELECT id, name, description, type, mime_type, file_size, updated_at, 0 as relevance
                 FROM documents 
                 WHERE name LIKE ? OR description LIKE ?
                 ORDER BY updated_at DESC
                 LIMIT ?",
                [$searchTerm, $searchTerm, $limit]
            );
        }
        
        $results['documents'] = array_map(function($d) {
            return [
                'id' => $d['id'],
                'name' => $d['name'],
                'type' => $d['type'],
                'mime_type' => $d['mime_type'],
                'file_size' => $d['file_size'],
                'relevance' => round((float)$d['relevance'], 2),
                'url' => 'documents.php?folder=' . $d['id'],
            ];
        }, $documents);
    }
    
    // Search Users
    if ($type === 'all' || $type === 'users') {
        $useFulltext = $searchMode !== 'like' && hasFulltextIndex($db, 'users', 'ft_users_search');
        
        if ($useFulltext && !empty($fulltextQuery)) {
            $users = $db->fetchAll(
                "SELECT id, full_name, email, avatar_url, position, department, role,
                        MATCH(full_name, email, department, position) AGAINST(? IN BOOLEAN MODE) as relevance
                 FROM users 
                 WHERE MATCH(full_name, email, department, position) AGAINST(? IN BOOLEAN MODE)
                   AND is_active = 1
                 ORDER BY relevance DESC, full_name ASC
                 LIMIT ?",
                [$fulltextQuery, $fulltextQuery, $limit]
            );
        } else {
            $users = $db->fetchAll(
                "SELECT id, full_name, email, avatar_url, position, department, role, 0 as relevance
                 FROM users 
                 WHERE (full_name LIKE ? OR email LIKE ? OR department LIKE ? OR position LIKE ?) 
                   AND is_active = 1
                 ORDER BY full_name ASC
                 LIMIT ?",
                [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]
            );
        }
        
        $results['users'] = array_map(function($u) {
            return [
                'id' => $u['id'],
                'name' => $u['full_name'],
                'email' => $u['email'],
                'avatar' => $u['avatar_url'],
                'position' => $u['position'],
                'department' => $u['department'],
                'role' => $u['role'],
                'relevance' => round((float)$u['relevance'], 2),
            ];
        }, $users);
    }
    
    // Calculate total results
    $totalResults = count($results['projects']) + count($results['tasks']) + 
                    count($results['documents']) + count($results['users']);
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'total' => $totalResults,
        'search_mode' => $useFulltext ?? false ? 'fulltext' : 'like',
        'data' => $results,
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
