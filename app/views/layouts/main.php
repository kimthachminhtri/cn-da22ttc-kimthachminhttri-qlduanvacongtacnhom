<?php
use Core\View;
use Core\Session;

// Load user theme setting
$currentTheme = 'system';
if (Session::has('user_id')) {
    $userModel = new \App\Models\User();
    $settings = $userModel->getUserSettings(Session::get('user_id'));
    $currentTheme = $settings['theme'] ?? 'system';
}
?>
<!DOCTYPE html>
<html lang="vi" class="<?= $currentTheme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($pageTitle ?? 'Tổng quan') ?> - TaskFlow</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sidebar: {
                            DEFAULT: '#1e293b',
                            foreground: '#e2e8f0',
                            border: '#334155',
                            accent: '#334155',
                            'accent-foreground': '#f8fafc',
                            primary: '#3b82f6',
                            'primary-foreground': '#ffffff',
                        },
                        primary: {
                            DEFAULT: '#3b82f6',
                            foreground: '#ffffff',
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Skeleton loading */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }
        .dark .skeleton {
            background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
            background-size: 200% 100%;
        }
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Fade in animation */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Dark mode overrides */
        .dark body { background-color: #111827; color: #f9fafb; }
        .dark .bg-white { background-color: #1f2937; }
        .dark .bg-gray-50 { background-color: #111827; }
        .dark .bg-gray-100 { background-color: #1f2937; }
        .dark .text-gray-900 { color: #f9fafb; }
        .dark .text-gray-700 { color: #d1d5db; }
        .dark .text-gray-600 { color: #9ca3af; }
        .dark .text-gray-500 { color: #9ca3af; }
        .dark .border-gray-200 { border-color: #374151; }
        .dark .border-gray-300 { border-color: #4b5563; }
        .dark .hover\:bg-gray-50:hover { background-color: #374151; }
        .dark .hover\:bg-gray-100:hover { background-color: #374151; }
        .dark input, .dark select, .dark textarea {
            background-color: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        .dark input:focus, .dark select:focus, .dark textarea:focus {
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php View::partial('components/sidebar'); ?>
        
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <?php View::partial('components/header'); ?>
            
            
            
            <!-- Main Content -->
            <main class="flex-1 overflow-auto p-6">
                <?= View::yield('content') ?>
            </main>
        </div>
    </div>
    
    <!-- Search Modal -->
    <?php View::partial('components/search-modal'); ?>
    
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    
    <script>
        lucide.createIcons();
        
        // Modal functions
        function openModal(id) {
            document.getElementById(id)?.classList.remove('hidden');
        }
        
        function closeModal(id) {
            document.getElementById(id)?.classList.add('hidden');
        }
        
        // Toast notification system
        function showToast(message, type = 'info', duration = 3000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            const icons = {
                success: 'check-circle',
                error: 'x-circle',
                warning: 'alert-triangle',
                info: 'info'
            };
            
            toast.className = `flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white ${colors[type]} transform transition-all duration-300 translate-x-full`;
            toast.innerHTML = `
                <i data-lucide="${icons[type]}" class="h-5 w-5"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-2 hover:opacity-75">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            `;
            
            container.appendChild(toast);
            lucide.createIcons();
            
            // Animate in
            setTimeout(() => toast.classList.remove('translate-x-full'), 10);
            
            // Auto remove
            if (duration > 0) {
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }
        }
        
        // Dark mode handler
        function initTheme() {
            const theme = '<?= $currentTheme ?>';
            
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (theme === 'system') {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                }
            }
            
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (theme === 'system') {
                    document.documentElement.classList.toggle('dark', e.matches);
                }
            });
        }
        initTheme();
        
        // Show flash messages as toasts
        <?php if ($success = Session::get('success')): ?>
        showToast('<?= addslashes($success) ?>', 'success');
        <?php endif; ?>
        <?php if ($error = Session::get('error')): ?>
        showToast('<?= addslashes($error) ?>', 'error');
        <?php endif; ?>
    </script>
</body>
</html>
