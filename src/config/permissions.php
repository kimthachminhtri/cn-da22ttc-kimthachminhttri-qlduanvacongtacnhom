<?php
/**
 * Permissions Configuration
 * Định nghĩa 4 quyền: admin, manager, member, guest
 * 
 * CHÚ Ý: Chỉ admin và manager mới có quyền:
 * - tasks.create, tasks.edit, tasks.delete
 * - projects.create, projects.edit, projects.delete
 */

return [
    // Role definitions
    'roles' => [
        'admin' => [
            'name' => 'Quản trị viên',
            'description' => 'Toàn quyền trong hệ thống',
            'level' => 100,
        ],
        'manager' => [
            'name' => 'Quản lý',
            'description' => 'Quản lý dự án và nhóm',
            'level' => 50,
        ],
        'member' => [
            'name' => 'Thành viên',
            'description' => 'Người dùng thông thường - chỉ xem và thực hiện task được giao',
            'level' => 10,
        ],
        'guest' => [
            'name' => 'Khách',
            'description' => 'Chỉ xem, không chỉnh sửa',
            'level' => 1,
        ],
    ],
    
    // Permission matrix
    'permissions' => [
        'admin' => [
            // Users
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // Projects - full access
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            // Tasks - full access
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            // Documents
            'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
            // Settings
            'settings.view', 'settings.edit',
            // Team
            'team.manage',
            // Reports
            'reports.view', 'reports.export',
            // Admin
            'admin.access',
        ],
        'manager' => [
            // Users
            'users.view',
            // Projects - create, edit (no delete)
            'projects.view', 'projects.create', 'projects.edit',
            // Tasks - full access
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            // Documents
            'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
            // Team
            'team.manage',
            // Reports
            'reports.view', 'reports.export',
        ],
        'member' => [
            // Users - chỉ xem
            'users.view',
            // Projects - chỉ xem (KHÔNG có create, edit, delete)
            'projects.view',
            // Tasks - chỉ xem (KHÔNG có create, edit, delete)
            // Member chỉ có thể cập nhật status task được giao qua project-level permission
            'tasks.view',
            // Documents - xem và upload
            'documents.view', 'documents.create',
        ],
        'guest' => [
            // Chỉ xem
            'projects.view',
            'tasks.view',
            'documents.view',
        ],
    ],
    
    // Project-level roles - quyền trong phạm vi dự án
    // Member có thể được giao quyền cao hơn trong từng dự án cụ thể
    'project_roles' => [
        'owner' => [
            'name' => 'Chủ dự án',
            'permissions' => ['*'], // All permissions within project
        ],
        'manager' => [
            'name' => 'Quản lý dự án',
            'permissions' => [
                'project.edit', 'project.members.manage',
                'tasks.create', 'tasks.edit', 'tasks.delete',
                'documents.create', 'documents.edit', 'documents.delete',
            ],
        ],
        'member' => [
            'name' => 'Thành viên',
            'permissions' => [
                // Member trong dự án có thể cập nhật task được giao
                'tasks.update.assigned', // Chỉ cập nhật task được giao
                'documents.create', 'documents.edit.own',
            ],
        ],
        'viewer' => [
            'name' => 'Người xem',
            'permissions' => ['project.view', 'tasks.view', 'documents.view'],
        ],
    ],
];
