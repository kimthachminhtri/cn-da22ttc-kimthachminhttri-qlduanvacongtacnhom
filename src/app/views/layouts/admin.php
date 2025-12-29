<?php
/**
 * Admin Layout - Professional Admin Panel
 */
use Core\View;
use Core\Session;

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$userRole = Session::get('user_role', 'guest');
$userName = Session::get('user_name', 'Admin');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($pageTitle ?? 'Quản trị') ?> - TaskFlow Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        admin: {
                            dark: '#0f172a',
                            darker: '#020617',
                            accent: '#1e293b',
                            border: '#334155',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active { background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%); }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: true, mobileMenu: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-admin-dark text-white transform transition-transform duration-300 lg:relative lg:translate-x-0"
               :class="{ '-translate-x-full': !sidebarOpen && !mobileMenu, 'translate-x-0': mobileMenu }"
               @click.away="mobileMenu = false">
            
            <!-- Logo -->
            <div class="flex h-16 items-center justify-between px-4 bg-admin-darker">
                <a href="/php/admin/index.php" class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg">
                        <i data-lucide="shield-check" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <span class="font-bold text-lg">TaskFlow</span>
                        <span class="block text-xs text-blue-400">Admin Panel</span>
                    </div>
                </a>
                <button @click="mobileMenu = false" class="lg:hidden text-gray-400 hover:text-white">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tổng quan</p>
                
                <a href="/php/admin/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'index' ? 'active' : '' ?>">
                    <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                    Dashboard
                </a>
                
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-3">Quản lý</p>
                
                <a href="/php/admin/users.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'users' ? 'active' : '' ?>">
                    <i data-lucide="users" class="h-5 w-5"></i>
                    Người dùng
                    <span class="ml-auto bg-blue-500/20 text-blue-400 text-xs px-2 py-0.5 rounded-full"><?= $stats['users'] ?? '' ?></span>
                </a>
                
                <a href="/php/admin/projects.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'projects' ? 'active' : '' ?>">
                    <i data-lucide="folder-kanban" class="h-5 w-5"></i>
                    Dự án
                </a>
                
                <a href="/php/admin/tasks.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'tasks' ? 'active' : '' ?>">
                    <i data-lucide="check-square" class="h-5 w-5"></i>
                    Công việc
                </a>
                
                <a href="/php/admin/documents.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'documents' ? 'active' : '' ?>">
                    <i data-lucide="file-text" class="h-5 w-5"></i>
                    Tài liệu
                </a>
                
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-3">Hệ thống</p>
                
                <a href="/php/admin/settings.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'settings' ? 'active' : '' ?>">
                    <i data-lucide="settings" class="h-5 w-5"></i>
                    Cài đặt
                </a>
                
                <a href="/php/admin/logs.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'logs' ? 'active' : '' ?>">
                    <i data-lucide="scroll-text" class="h-5 w-5"></i>
                    Activity Logs
                </a>
                
                <a href="/php/admin/reports.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'reports' ? 'active' : '' ?>">
                    <i data-lucide="bar-chart-3" class="h-5 w-5"></i>
                    Báo cáo & Thống kê
                </a>
                
                <a href="/php/admin/backup.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all hover:bg-admin-accent <?= $currentPage === 'backup' ? 'active' : '' ?>">
                    <i data-lucide="database" class="h-5 w-5"></i>
                    Backup & Restore
                </a>
            </nav>
            
            <!-- Back to App -->
            <div class="p-4 border-t border-admin-border">
                <a href="/php/index.php" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-admin-accent transition-all">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                    Quay lại ứng dụng
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                    
                    <!-- Breadcrumb -->
                    <nav class="hidden sm:flex items-center gap-2 text-sm">
                        <a href="/php/admin/index.php" class="text-gray-500 hover:text-gray-700">Admin</a>
                        <i data-lucide="chevron-right" class="h-4 w-4 text-gray-400"></i>
                        <span class="text-gray-900 font-medium"><?= View::e($pageTitle ?? 'Dashboard') ?></span>
                    </nav>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Global Search -->
                    <div class="relative hidden md:block" x-data="adminSearch()">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                            <input type="text" 
                                   x-model="query"
                                   @input.debounce.300ms="search()"
                                   @focus="showResults = query.length >= 2"
                                   @keydown.escape="showResults = false"
                                   placeholder="Tìm kiếm..." 
                                   class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Search Results Dropdown -->
                        <div x-show="showResults && results" 
                             @click.away="showResults = false"
                             x-cloak
                             class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-200 max-h-96 overflow-y-auto z-50">
                            
                            <template x-if="loading">
                                <div class="p-4 text-center text-gray-500">
                                    <i data-lucide="loader-2" class="h-5 w-5 animate-spin mx-auto"></i>
                                </div>
                            </template>
                            
                            <template x-if="!loading && results">
                                <div>
                                    <!-- Users -->
                                    <template x-if="results.users && results.users.length > 0">
                                        <div class="p-2 border-b border-gray-100">
                                            <p class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase">Người dùng</p>
                                            <template x-for="user in results.users.slice(0, 3)" :key="user.id">
                                                <a :href="'/php/admin/users.php?search=' + encodeURIComponent(user.email)" 
                                                   class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-sm font-medium"
                                                         x-text="user.name.charAt(0).toUpperCase()"></div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900" x-text="user.name"></p>
                                                        <p class="text-xs text-gray-500" x-text="user.email"></p>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- Projects -->
                                    <template x-if="results.projects && results.projects.length > 0">
                                        <div class="p-2 border-b border-gray-100">
                                            <p class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase">Dự án</p>
                                            <template x-for="project in results.projects.slice(0, 3)" :key="project.id">
                                                <a :href="'/php/project-detail.php?id=' + project.id" 
                                                   class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50">
                                                    <div class="h-8 w-8 rounded-lg flex items-center justify-center"
                                                         :style="'background-color: ' + (project.color || '#3b82f6') + '20'">
                                                        <i data-lucide="folder" class="h-4 w-4" :style="'color: ' + (project.color || '#3b82f6')"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900" x-text="project.name"></p>
                                                        <p class="text-xs text-gray-500" x-text="project.status"></p>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- Tasks -->
                                    <template x-if="results.tasks && results.tasks.length > 0">
                                        <div class="p-2">
                                            <p class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase">Công việc</p>
                                            <template x-for="task in results.tasks.slice(0, 3)" :key="task.id">
                                                <a :href="'/php/task-detail.php?id=' + task.id" 
                                                   class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50">
                                                    <div class="h-8 w-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                                                        <i data-lucide="check-square" class="h-4 w-4 text-yellow-600"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900" x-text="task.title"></p>
                                                        <p class="text-xs text-gray-500" x-text="task.project_name || 'Không có dự án'"></p>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- No Results -->
                                    <template x-if="(!results.users || results.users.length === 0) && (!results.projects || results.projects.length === 0) && (!results.tasks || results.tasks.length === 0)">
                                        <div class="p-4 text-center text-gray-500">
                                            <i data-lucide="search-x" class="h-8 w-8 mx-auto mb-2 text-gray-300"></i>
                                            <p class="text-sm">Không tìm thấy kết quả</p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="hidden lg:flex items-center gap-2">
                        <a href="/php/admin/users.php?action=create" 
                           class="flex items-center gap-2 px-3 py-1.5 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                            <i data-lucide="user-plus" class="h-4 w-4"></i>
                            Thêm user
                        </a>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="bell" class="h-5 w-5"></i>
                        <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-sm font-medium">
                                <?= strtoupper(substr($userName, 0, 1)) ?>
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium text-gray-900"><?= View::e($userName) ?></p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                            <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                            <a href="/php/settings.php" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i data-lucide="user" class="h-4 w-4"></i>
                                Hồ sơ cá nhân
                            </a>
                            <a href="/php/admin/settings.php" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i data-lucide="settings" class="h-4 w-4"></i>
                                Cài đặt hệ thống
                            </a>
                            <hr class="my-1">
                            <a href="/php/logout.php" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i data-lucide="log-out" class="h-4 w-4"></i>
                                Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Flash Messages -->
            <?php if ($success = Session::flash('success')): ?>
            <div class="mx-6 mt-4 p-4 bg-green-50 text-green-700 border border-green-200 rounded-xl flex items-center gap-3">
                <i data-lucide="check-circle" class="h-5 w-5 flex-shrink-0"></i>
                <span><?= View::e($success) ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if ($error = Session::flash('error')): ?>
            <div class="mx-6 mt-4 p-4 bg-red-50 text-red-700 border border-red-200 rounded-xl flex items-center gap-3">
                <i data-lucide="alert-circle" class="h-5 w-5 flex-shrink-0"></i>
                <span><?= View::e($error) ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-6 bg-gray-50">
                <?= View::yield('content') ?>
            </main>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

    <script>
        lucide.createIcons();
        
        function showToast(message, type = 'info', duration = 3000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            toast.className = `${colors[type]} text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 transform translate-x-full transition-transform duration-300`;
            toast.innerHTML = `
                <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info'}" class="h-5 w-5"></i>
                <span>${message}</span>
            `;
            
            container.appendChild(toast);
            lucide.createIcons();
            setTimeout(() => toast.classList.remove('translate-x-full'), 10);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
        
        function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }
        
        // Admin Global Search
        function adminSearch() {
            return {
                query: '',
                results: null,
                loading: false,
                showResults: false,
                
                async search() {
                    if (this.query.length < 2) {
                        this.results = null;
                        this.showResults = false;
                        return;
                    }
                    
                    this.loading = true;
                    this.showResults = true;
                    
                    try {
                        const response = await fetch(`/php/api/search.php?q=${encodeURIComponent(this.query)}&limit=5`);
                        const data = await response.json();
                        
                        if (data.success) {
                            this.results = data.data;
                            // Re-init lucide icons for new content
                            setTimeout(() => lucide.createIcons(), 50);
                        }
                    } catch (e) {
                        console.error('Search error:', e);
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
        
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="-modal"]:not(.hidden)').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>
</body>
</html>
