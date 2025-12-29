<?php
/**
 * Document Controller
 */

namespace App\Controllers;

use App\Models\Document;
use App\Models\Project;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

class DocumentController extends BaseController
{
    private Document $documentModel;
    private Project $projectModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->documentModel = new Document();
        $this->projectModel = new Project();
    }

    public function index(): void
    {
        $folderId = $_GET['folder'] ?? null;
        $searchQuery = $_GET['q'] ?? '';
        $typeFilter = $_GET['type'] ?? '';
        
        if ($searchQuery) {
            $documents = $this->documentModel->search($searchQuery);
        } elseif ($folderId) {
            $documents = $this->documentModel->getByParent($folderId);
        } else {
            $documents = $this->documentModel->getRootDocuments();
        }
        
        // Filter by type if specified
        if ($typeFilter) {
            $documents = array_filter($documents, fn($d) => $d['type'] === $typeFilter);
        }
        
        $projects = $this->projectModel->getUserProjects($this->userId());
        $breadcrumbs = $folderId ? $this->documentModel->getBreadcrumbs($folderId) : [];
        
        $this->view('documents/index', [
            'documents' => $documents,
            'projects' => $projects,
            'currentFolder' => $folderId,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Tài liệu',
        ]);
    }

    public function createFolder(): void
    {
        if (!PermissionMiddleware::can('documents.create')) {
            return;
        }

        $errors = $this->validate([
            'name' => 'required|min:1',
        ]);

        if (!empty($errors)) {
            $this->error(implode(', ', $errors));
            $this->back();
            return;
        }

        $folderId = $this->documentModel->createFolder([
            'name' => $this->input('name'),
            'parent_id' => $this->input('parent_id') ?: null,
            'project_id' => $this->input('project_id') ?: null,
            'uploaded_by' => $this->userId(),
        ]);

        if ($folderId) {
            $this->success('Tạo thư mục thành công');
        } else {
            $this->error('Có lỗi xảy ra');
        }
        
        $this->back();
    }

    public function upload(): void
    {
        if (!PermissionMiddleware::can('documents.create')) {
            return;
        }

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->error('Vui lòng chọn file để tải lên');
            $this->back();
            return;
        }

        $result = $this->documentModel->uploadFile([
            'parent_id' => $this->input('parent_id') ?: null,
            'project_id' => $this->input('project_id') ?: null,
            'uploaded_by' => $this->userId(),
        ], $_FILES['file']);

        if ($result['success']) {
            $this->success('Tải lên thành công');
        } else {
            $this->error($result['error']);
        }
        
        $this->back();
    }

    public function delete(string $id): void
    {
        $document = $this->documentModel->find($id);
        
        if (!$document) {
            $this->json(['success' => false, 'error' => 'Document not found'], 404);
            return;
        }

        // Check permission
        if (!$this->documentModel->canDelete($id, $this->userId(), $this->userRole())) {
            $this->json(['success' => false, 'error' => 'Bạn không có quyền xóa tài liệu này'], 403);
            return;
        }

        // Delete physical file if exists
        if ($document['type'] === 'file' && !empty($document['file_path'])) {
            $filePath = BASE_PATH . '/public/' . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->documentModel->delete($id);
        $this->json(['success' => true, 'message' => 'Document deleted']);
    }

    public function toggleStar(string $id): void
    {
        $document = $this->documentModel->find($id);
        
        if (!$document) {
            $this->json(['success' => false, 'error' => 'Document not found'], 404);
            return;
        }

        $newStarred = !$document['is_starred'];
        $this->documentModel->update($id, ['is_starred' => $newStarred]);
        
        $this->json([
            'success' => true,
            'starred' => $newStarred,
        ]);
    }
}
