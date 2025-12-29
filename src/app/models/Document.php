<?php
/**
 * Document Model
 */

namespace App\Models;

class Document extends BaseModel
{
    protected string $table = 'documents';

    public function getAllWithUploader(): array
    {
        $sql = "SELECT d.*, u.full_name as uploader_name, u.avatar_url as uploader_avatar,
                (SELECT COUNT(*) FROM documents WHERE parent_id = d.id) as items_count
                FROM {$this->table} d
                LEFT JOIN users u ON d.uploaded_by = u.id
                ORDER BY d.type = 'folder' DESC, d.updated_at DESC";
        return $this->db->fetchAll($sql);
    }

    public function getByFolder(?string $folderId): array
    {
        if ($folderId) {
            $sql = "SELECT d.*, u.full_name as uploader_name
                    FROM {$this->table} d
                    LEFT JOIN users u ON d.uploaded_by = u.id
                    WHERE d.parent_id = ?
                    ORDER BY d.type = 'folder' DESC, d.name ASC";
            return $this->db->fetchAll($sql, [$folderId]);
        }
        
        $sql = "SELECT d.*, u.full_name as uploader_name
                FROM {$this->table} d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.parent_id IS NULL
                ORDER BY d.type = 'folder' DESC, d.name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getByProject(string $projectId): array
    {
        $sql = "SELECT d.*, u.full_name as uploader_name
                FROM {$this->table} d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.project_id = ?
                ORDER BY d.type = 'folder' DESC, d.updated_at DESC";
        return $this->db->fetchAll($sql, [$projectId]);
    }

    public function createFolder(array $data): string|false
    {
        $data['id'] = $this->generateUUID();
        $data['type'] = 'folder';
        
        $result = $this->create($data);
        return $result !== false ? $data['id'] : false;
    }

    public function uploadFile(array $data, array $file): array
    {
        $config = require BASE_PATH . '/config/app.php';
        
        // Validate file
        if ($file['size'] > $config['upload']['max_size']) {
            return ['success' => false, 'error' => 'File quá lớn'];
        }
        
        if (!in_array($file['type'], $config['upload']['allowed_types'])) {
            return ['success' => false, 'error' => 'Loại file không được hỗ trợ'];
        }
        
        // Create upload directory
        $uploadDir = BASE_PATH . '/public/assets/uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $this->generateUUID() . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'error' => 'Không thể lưu file'];
        }
        
        // Save to database
        $docId = $this->generateUUID();
        $this->create([
            'id' => $docId,
            'name' => $file['name'],
            'type' => 'file',
            'mime_type' => $file['type'],
            'file_size' => $file['size'],
            'file_path' => 'assets/uploads/documents/' . $filename,
            'parent_id' => $data['parent_id'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'uploaded_by' => $data['uploaded_by'],
        ]);
        
        return ['success' => true, 'id' => $docId];
    }

    public function canDelete(string $docId, string $userId, string $userRole): bool
    {
        $doc = $this->find($docId);
        if (!$doc) return false;
        
        // Admin can delete anything
        if ($userRole === 'admin') return true;
        
        // Owner can delete their own
        if ($doc['uploaded_by'] === $userId) return true;
        
        // Manager can delete
        if ($userRole === 'manager') return true;
        
        return false;
    }

    public function getRootDocuments(): array
    {
        // Lấy TẤT CẢ tài liệu ở root (không có parent)
        $sql = "SELECT d.*, u.full_name as uploader_name, p.name as project_name
                FROM {$this->table} d
                LEFT JOIN users u ON d.uploaded_by = u.id
                LEFT JOIN projects p ON d.project_id = p.id
                WHERE d.parent_id IS NULL
                ORDER BY d.type = 'folder' DESC, d.is_starred DESC, d.name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getByParent(string $parentId): array
    {
        $sql = "SELECT d.*, u.full_name as uploader_name
                FROM {$this->table} d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.parent_id = ?
                ORDER BY d.type = 'folder' DESC, d.is_starred DESC, d.name ASC";
        return $this->db->fetchAll($sql, [$parentId]);
    }

    public function search(string $query, ?string $projectId = null): array
    {
        if ($projectId) {
            // Tìm trong dự án cụ thể
            $sql = "SELECT d.*, u.full_name as uploader_name
                    FROM {$this->table} d
                    LEFT JOIN users u ON d.uploaded_by = u.id
                    WHERE d.name LIKE ? AND d.project_id = ?
                    ORDER BY d.type = 'folder' DESC, d.is_starred DESC, d.name ASC";
            return $this->db->fetchAll($sql, ['%' . $query . '%', $projectId]);
        } else {
            // Tìm trong TẤT CẢ tài liệu
            $sql = "SELECT d.*, u.full_name as uploader_name, p.name as project_name
                    FROM {$this->table} d
                    LEFT JOIN users u ON d.uploaded_by = u.id
                    LEFT JOIN projects p ON d.project_id = p.id
                    WHERE d.name LIKE ?
                    ORDER BY d.type = 'folder' DESC, d.is_starred DESC, d.name ASC";
            return $this->db->fetchAll($sql, ['%' . $query . '%']);
        }
    }

    public function getBreadcrumbs(string $folderId): array
    {
        $breadcrumbs = [];
        $currentId = $folderId;
        
        while ($currentId) {
            $folder = $this->find($currentId);
            if (!$folder) break;
            
            array_unshift($breadcrumbs, [
                'id' => $folder['id'],
                'name' => $folder['name']
            ]);
            
            $currentId = $folder['parent_id'];
        }
        
        return $breadcrumbs;
    }
}
