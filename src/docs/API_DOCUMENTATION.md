# TaskFlow API Documentation

## Tổng quan

TaskFlow API sử dụng RESTful conventions với JSON responses.

**Base URL:** `/api/`

**Authentication:** Session-based với CSRF token

**CSRF Token:** Gửi trong header `X-CSRF-Token`

---

## Authentication

### Login
```
POST /api/auth/login
```

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123",
    "remember": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Đăng nhập thành công",
    "user": {
        "id": "uuid",
        "email": "user@example.com",
        "full_name": "Nguyễn Văn A",
        "role": "member"
    }
}
```

### Logout
```
POST /api/auth/logout
```

---

## Projects

### List Projects
```
GET /api/projects.php
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "uuid",
            "name": "Project Name",
            "description": "Description",
            "color": "#3B82F6",
            "status": "active",
            "progress": 45,
            "member_count": 5,
            "task_count": 12
        }
    ]
}
```

### Create Project
```
POST /api/create-project.php
```

**Request Body:**
```json
{
    "name": "New Project",
    "description": "Project description",
    "color": "#3B82F6"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Tạo dự án thành công",
    "project_id": "uuid"
}
```

### Get Project Details
```
GET /api/project-detail.php?id={project_id}
```

### Update Project
```
POST /api/update-project.php
```

**Request Body:**
```json
{
    "project_id": "uuid",
    "name": "Updated Name",
    "description": "Updated description",
    "status": "active"
}
```

### Delete Project
```
DELETE /api/delete-project.php?id={project_id}
```

---

## Tasks

### List Tasks by Project
```
GET /api/tasks.php?project_id={project_id}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "uuid",
            "title": "Task Title",
            "description": "Description",
            "status": "todo",
            "priority": "medium",
            "due_date": "2024-12-31",
            "assignees": [
                {
                    "id": "uuid",
                    "full_name": "Nguyễn Văn A",
                    "avatar_url": "/uploads/avatars/..."
                }
            ]
        }
    ]
}
```

### Create Task
```
POST /api/create-task.php
```

**Request Body:**
```json
{
    "title": "New Task",
    "description": "Task description",
    "project_id": "uuid",
    "priority": "medium",
    "due_date": "2024-12-31",
    "assignee_id": "uuid",
    "status": "todo"
}
```

**Validation:**
- `title`: Required, max 255 characters
- `priority`: One of `low`, `medium`, `high`, `urgent`
- `status`: One of `backlog`, `todo`, `in_progress`, `in_review`, `done`
- `due_date`: Format `YYYY-MM-DD`, must be today or future

**Response:**
```json
{
    "success": true,
    "message": "Tạo công việc thành công",
    "task_id": "uuid"
}
```

### Update Task
```
POST /api/update-task.php
```

**Request Body:**
```json
{
    "task_id": "uuid",
    "title": "Updated Title",
    "status": "in_progress",
    "priority": "high",
    "version": 1
}
```

**Note:** `version` field is used for optimistic locking. If version mismatch, returns 409 Conflict.

### Delete Task
```
DELETE /api/update-task.php?id={task_id}
```

**Note:** Cannot delete completed tasks.

---

## Comments

### Get Comments
```
GET /api/comments.php?task_id={task_id}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "uuid",
            "content": "Comment content",
            "created_by": "uuid",
            "full_name": "Nguyễn Văn A",
            "avatar_url": "/uploads/avatars/...",
            "created_at": "2024-12-29 10:30:00",
            "time_ago": "5 phút trước"
        }
    ]
}
```

### Add Comment
```
POST /api/comments.php
```

**Request Body:**
```json
{
    "task_id": "uuid",
    "content": "Comment content"
}
```

### Delete Comment
```
DELETE /api/comments.php?id={comment_id}
```

---

## Checklist

### Get Checklist
```
GET /api/checklist.php?task_id={task_id}
```

### Add Checklist Item
```
POST /api/checklist.php
```

**Request Body:**
```json
{
    "task_id": "uuid",
    "title": "Checklist item"
}
```

### Toggle Checklist Item
```
POST /api/checklist.php
```

**Request Body:**
```json
{
    "action": "toggle",
    "item_id": "uuid"
}
```

### Delete Checklist Item
```
DELETE /api/checklist.php?id={item_id}
```

---

## Documents

### List Documents
```
GET /api/documents.php?project_id={project_id}&parent_id={folder_id}
```

### Upload Document
```
POST /api/upload-document.php
Content-Type: multipart/form-data
```

**Form Fields:**
- `file`: File to upload (max 10MB)
- `project_id`: Project UUID (optional)
- `parent_id`: Parent folder UUID (optional)

**Allowed Types:**
- Images: jpg, jpeg, png, gif, webp
- Documents: pdf, doc, docx, xls, xlsx, ppt, pptx
- Text: txt, csv, md
- Archives: zip, rar

**Blocked:**
- Executable: php, phtml, phar, exe, bat, sh, etc.

### Create Folder
```
POST /api/create-folder.php
```

**Request Body:**
```json
{
    "name": "Folder Name",
    "project_id": "uuid",
    "parent_id": "uuid"
}
```

### Delete Document
```
DELETE /api/documents.php?id={document_id}
```

### Star/Unstar Document
```
POST /api/documents.php
```

**Request Body:**
```json
{
    "action": "star",
    "document_id": "uuid"
}
```

---

## Notifications

### Get Notifications
```
GET /api/notifications.php
```

**Query Parameters:**
- `unread_only`: `1` to get only unread notifications
- `limit`: Number of notifications (default 20)

### Mark as Read
```
POST /api/notifications.php
```

**Request Body:**
```json
{
    "action": "mark_read",
    "notification_id": "uuid"
}
```

### Mark All as Read
```
POST /api/notifications.php
```

**Request Body:**
```json
{
    "action": "mark_all_read"
}
```

---

## Error Responses

All API endpoints return consistent error format:

```json
{
    "success": false,
    "error": "Error message in Vietnamese"
}
```

**HTTP Status Codes:**
| Code | Meaning |
|------|---------|
| 200 | Success |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Not logged in |
| 403 | Forbidden - No permission |
| 404 | Not Found |
| 409 | Conflict - Version mismatch |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Server Error |

---

## Rate Limiting

- Login: 5 attempts per minute per IP
- API calls: 100 requests per minute per user

---

## Changelog

### v2.0.2 (2024-12-29)
- Added optimistic locking for task updates
- Added CSRF protection for all endpoints
- Improved file upload security
- Added composite database indexes

### v2.0.1 (2024-12-29)
- Security patches
- Bug fixes

### v2.0.0 (2024-12-28)
- Initial release
