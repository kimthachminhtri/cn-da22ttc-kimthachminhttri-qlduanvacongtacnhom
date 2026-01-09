<?php
/**
 * API: Upload Document
 */

require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
use Core\Session;
use Core\Permission;

// Enforce CSRF for non-GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

Session::start();

if (!Session::has('user_id')) {
    header('Location: ../login.php');
    exit;
}

// Check permission
$userRole = Session::get('user_role', 'guest');
if (!Permission::can($userRole, 'documents.create')) {
    $_SESSION['error'] = 'Bạn không có quyền tải lên tài liệu';
    header('Location: ../documents.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../documents.php');
    exit;
}

$parentId = $_POST['parent_id'] ?? null;
$projectId = $_POST['project_id'] ?? null;

// Check if files uploaded
if (empty($_FILES['files']) || $_FILES['files']['error'][0] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['error'] = 'Vui lòng chọn file để tải lên';
    header('Location: ../documents.php');
    exit;
}

// Allowed file types with magic bytes validation
$allowedTypes = [
    'application/pdf' => 'pdf',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'doc',
    'application/vnd.ms-excel' => 'xls',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xls',
    'application/vnd.ms-powerpoint' => 'ppt',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'ppt',
    'image/jpeg' => 'image',
    'image/png' => 'image',
    'image/gif' => 'image',
    'image/webp' => 'image',
    'application/zip' => 'zip',
    'application/x-zip-compressed' => 'zip',
    'text/plain' => 'other',
];

// Dangerous extensions that should never be allowed
$dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phar', 'exe', 'sh', 'bat', 'cmd', 'com', 'scr', 'js', 'vbs', 'wsf', 'asp', 'aspx', 'jsp', 'cgi', 'pl', 'py', 'rb', 'htaccess', 'htpasswd'];

/**
 * Validate file is not dangerous
 */
function isFileSafe($filename, $tmpPath, $dangerousExtensions) {
    // Check for double extensions (e.g., file.php.jpg)
    $parts = explode('.', strtolower($filename));
    foreach ($parts as $part) {
        if (in_array($part, $dangerousExtensions)) {
            return false;
        }
    }
    
    // Check file content for PHP code
    $content = file_get_contents($tmpPath, false, null, 0, 1024);
    if ($content !== false) {
        // Check for PHP opening tags
        if (preg_match('/<\?php|<\?=|<\?(?!\s*xml)/i', $content)) {
            return false;
        }
        // Check for script tags
        if (preg_match('/<script/i', $content)) {
            return false;
        }
    }
    
    return true;
}

$maxSize = 50 * 1024 * 1024; // 50MB
$uploadDir = __DIR__ . '/../uploads/documents/';

// Create upload directory if not exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    $db = \Core\Database::getInstance();
    $uploadedCount = 0;
    $errors = [];
    
    $files = $_FILES['files'];
    $fileCount = count($files['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = $files['name'][$i] . ': Upload error';
            continue;
        }
        
        $fileName = $files['name'][$i];
        $fileSize = $files['size'][$i];
        $fileTmp = $files['tmp_name'][$i];
            // Prefer server-detected MIME type to avoid trusting client-provided value
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $fileType = $finfo->file($fileTmp) ?: ($files['type'][$i] ?? 'application/octet-stream');
        
        // Validate size
        if ($fileSize > $maxSize) {
            $errors[] = $fileName . ': File quá lớn (tối đa 50MB)';
            continue;
        }
        
        // Get file extension and determine extension from MIME if necessary
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $docType = $allowedTypes[$fileType] ?? 'other';

        // Map some MIME types to common extensions when client extension is missing or suspicious
        $mimeToExt = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/zip' => 'zip',
            'text/plain' => 'txt',
        ];

        if (empty($ext) || !in_array($fileType, array_keys($allowedTypes))) {
            $ext = $mimeToExt[$fileType] ?? ($ext ?: 'bin');
        }

        // Reject files with disallowed MIME types
        if (!array_key_exists($fileType, $allowedTypes)) {
            $errors[] = $fileName . ': Loại file không được phép';
            continue;
        }
        
        // Security check: validate file is safe
        if (!isFileSafe($fileName, $fileTmp, $dangerousExtensions)) {
            $errors[] = $fileName . ': File chứa nội dung không an toàn';
            continue;
        }
        
        // Generate unique filename
        $docId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        
        $storedName = $docId . '.' . $ext;
        $filePath = $uploadDir . $storedName;
        
        // Move uploaded file to storage and set safe permissions
        if (!move_uploaded_file($fileTmp, $filePath)) {
            $errors[] = $fileName . ': Không thể lưu file';
            continue;
        }
        // Ensure file is not executable
        @chmod($filePath, 0644);
        
        // Insert to database
        $db->insert('documents', [
            'id' => $docId,
            'name' => $fileName,
            'type' => 'file',
            'mime_type' => $fileType,
            'file_size' => $fileSize,
            'file_path' => 'uploads/documents/' . $storedName,
            'parent_id' => $parentId ?: null,
            'project_id' => $projectId ?: null,
            'uploaded_by' => Session::get('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $uploadedCount++;
    }
    
    if ($uploadedCount > 0) {
        $_SESSION['success'] = "Đã tải lên $uploadedCount file thành công";
    }
    if (!empty($errors)) {
        $_SESSION['error'] = implode(', ', $errors);
    }
    
    // Redirect back to appropriate page
    if ($projectId) {
        $redirect = '../project-detail.php?id=' . $projectId . '&tab=documents';
    } elseif ($parentId) {
        $redirect = '../documents.php?folder=' . $parentId;
    } else {
        $redirect = '../documents.php';
    }
    header('Location: ' . $redirect);
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    
    if ($projectId) {
        header('Location: ../project-detail.php?id=' . $projectId . '&tab=documents');
    } else {
        header('Location: ../documents.php');
    }
    exit;
}
