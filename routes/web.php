<?php
/**
 * Web Routes
 */

use Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProjectController;
use App\Controllers\TaskController;
use App\Controllers\DocumentController;
use App\Controllers\TeamController;

// Auth routes
Router::get('/login', [AuthController::class, 'showLogin'], 'login');
Router::post('/login', [AuthController::class, 'login']);
Router::get('/register', [AuthController::class, 'showRegister'], 'register');
Router::post('/register', [AuthController::class, 'register']);
Router::get('/logout', [AuthController::class, 'logout'], 'logout');

// Dashboard
Router::get('/', [DashboardController::class, 'index'], 'dashboard');
Router::get('/dashboard', [DashboardController::class, 'index']);

// Projects
Router::get('/projects', [ProjectController::class, 'index'], 'projects.index');
Router::get('/projects/{id}', [ProjectController::class, 'show'], 'projects.show');
Router::post('/projects', [ProjectController::class, 'create'], 'projects.create');
Router::put('/projects/{id}', [ProjectController::class, 'update'], 'projects.update');
Router::delete('/projects/{id}', [ProjectController::class, 'delete'], 'projects.delete');

// Tasks
Router::get('/tasks', [TaskController::class, 'index'], 'tasks.index');
Router::get('/tasks/{id}', [TaskController::class, 'show'], 'tasks.show');
Router::post('/tasks', [TaskController::class, 'create'], 'tasks.create');
Router::put('/tasks/{id}', [TaskController::class, 'update'], 'tasks.update');
Router::delete('/tasks/{id}', [TaskController::class, 'delete'], 'tasks.delete');

// Documents
Router::get('/documents', [DocumentController::class, 'index'], 'documents.index');
Router::post('/documents/folder', [DocumentController::class, 'createFolder'], 'documents.folder');
Router::post('/documents/upload', [DocumentController::class, 'upload'], 'documents.upload');
Router::delete('/documents/{id}', [DocumentController::class, 'delete'], 'documents.delete');
Router::post('/documents/{id}/star', [DocumentController::class, 'toggleStar'], 'documents.star');

// Team
Router::get('/team', [TeamController::class, 'index'], 'team.index');
Router::post('/team', [TeamController::class, 'create'], 'team.create');
Router::put('/team/{id}', [TeamController::class, 'update'], 'team.update');
Router::delete('/team/{id}', [TeamController::class, 'delete'], 'team.delete');
Router::post('/team/{id}/activate', [TeamController::class, 'activate'], 'team.activate');


// Admin routes
use App\Controllers\AdminController;

Router::get('/admin', [AdminController::class, 'users'], 'admin.index');
Router::get('/admin/users', [AdminController::class, 'users'], 'admin.users');
Router::get('/admin/projects', [AdminController::class, 'projects'], 'admin.projects');
Router::get('/admin/settings', [AdminController::class, 'settings'], 'admin.settings');
Router::put('/admin/users/{id}/role', [AdminController::class, 'updateUserRole'], 'admin.users.role');
Router::post('/admin/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'], 'admin.users.toggle');

// Settings
use App\Controllers\SettingsController;

Router::get('/settings', [SettingsController::class, 'index'], 'settings.index');
Router::post('/settings/profile', [SettingsController::class, 'updateProfile'], 'settings.profile');
Router::post('/settings/password', [SettingsController::class, 'changePassword'], 'settings.password');
Router::post('/settings/notifications', [SettingsController::class, 'updateSettings'], 'settings.notifications');
Router::post('/settings/avatar', [SettingsController::class, 'uploadAvatar'], 'settings.avatar');

// Calendar
use App\Controllers\CalendarController;

Router::get('/calendar', [CalendarController::class, 'index'], 'calendar.index');

// Reports
use App\Controllers\ReportsController;

Router::get('/reports', [ReportsController::class, 'index'], 'reports.index');


// Notifications
use App\Controllers\NotificationController;

Router::get('/notifications', [NotificationController::class, 'index'], 'notifications.index');
Router::put('/notifications/read', [NotificationController::class, 'markAsRead'], 'notifications.read');
Router::get('/notifications/count', [NotificationController::class, 'getUnreadCount'], 'notifications.count');
