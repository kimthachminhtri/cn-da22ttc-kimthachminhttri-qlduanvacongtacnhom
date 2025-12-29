<?php
/**
 * Global Search Modal Component
 */
use Core\View;
?>

<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 z-[100] hidden" x-data="searchModal()" x-show="open" x-cloak>
    <div class="fixed inset-0 bg-black/50" @click="close()"></div>
    <div class="fixed inset-x-4 top-20 md:inset-x-auto md:left-1/2 md:-translate-x-1/2 md:w-full md:max-w-2xl">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden" @click.stop>
            <!-- Search Input -->
            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-200">
                <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                <input type="text" 
                       id="search-input"
                       placeholder="Tìm kiếm dự án, công việc, thành viên..." 
                       class="flex-1 text-lg outline-none placeholder-gray-400"
                       x-model="query"
                       @input.debounce.300ms="search()"
                       @keydown.escape="close()"
                       @keydown.arrow-down.prevent="selectNext()"
                       @keydown.arrow-up.prevent="selectPrev()"
                       @keydown.enter.prevent="openSelected()">
                <kbd class="hidden md:inline-flex items-center gap-1 px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded">
                    ESC
                </kbd>
            </div>
            
            <!-- Search Results -->
            <div class="max-h-[60vh] overflow-y-auto" id="search-results">
                <!-- Loading -->
                <div x-show="loading" class="px-4 py-8 text-center text-gray-500">
                    <i data-lucide="loader-2" class="h-6 w-6 mx-auto mb-2 animate-spin"></i>
                    Đang tìm kiếm...
                </div>
                
                <!-- Empty State -->
                <div x-show="!loading && query && results.length === 0" class="px-4 py-8 text-center text-gray-500">
                    <i data-lucide="search-x" class="h-8 w-8 mx-auto mb-2 text-gray-300"></i>
                    Không tìm thấy kết quả cho "<span x-text="query"></span>"
                </div>
                
                <!-- Initial State -->
                <div x-show="!loading && !query" class="p-4">
                    <p class="text-sm text-gray-500 mb-3">Tìm kiếm nhanh</p>
                    <div class="space-y-1">
                        <a href="/php/projects.php" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <i data-lucide="folder-kanban" class="h-4 w-4 text-blue-500"></i>
                            <span class="text-gray-700">Dự án</span>
                        </a>
                        <a href="/php/tasks.php" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <i data-lucide="check-square" class="h-4 w-4 text-green-500"></i>
                            <span class="text-gray-700">Công việc</span>
                        </a>
                        <a href="/php/team.php" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <i data-lucide="users" class="h-4 w-4 text-purple-500"></i>
                            <span class="text-gray-700">Thành viên</span>
                        </a>
                        <a href="/php/documents.php" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <i data-lucide="file-text" class="h-4 w-4 text-orange-500"></i>
                            <span class="text-gray-700">Tài liệu</span>
                        </a>
                    </div>
                </div>
                
                <!-- Results -->
                <div x-show="!loading && results.length > 0">
                    <!-- Projects -->
                    <template x-if="results.filter(r => r.type === 'project').length > 0">
                        <div class="p-2">
                            <p class="px-2 py-1 text-xs font-medium text-gray-500 uppercase">Dự án</p>
                            <template x-for="(item, index) in results.filter(r => r.type === 'project')" :key="item.id">
                                <a :href="'/php/project-detail.php?id=' + item.id" 
                                   class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100"
                                   :class="{ 'bg-primary/10': selectedIndex === index }">
                                    <div class="h-8 w-8 rounded-lg flex items-center justify-center" 
                                         :style="'background-color: ' + (item.color || '#6366f1') + '20'">
                                        <i data-lucide="folder" class="h-4 w-4" :style="'color: ' + (item.color || '#6366f1')"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate" x-text="item.name"></p>
                                        <p class="text-xs text-gray-500" x-text="item.status"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                    
                    <!-- Tasks -->
                    <template x-if="results.filter(r => r.type === 'task').length > 0">
                        <div class="p-2 border-t border-gray-100">
                            <p class="px-2 py-1 text-xs font-medium text-gray-500 uppercase">Công việc</p>
                            <template x-for="item in results.filter(r => r.type === 'task')" :key="item.id">
                                <a :href="'/php/task-detail.php?id=' + item.id" 
                                   class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                                    <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center">
                                        <i data-lucide="check-square" class="h-4 w-4 text-green-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate" x-text="item.title"></p>
                                        <p class="text-xs text-gray-500" x-text="item.project_name || 'Không có dự án'"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                    
                    <!-- Users -->
                    <template x-if="results.filter(r => r.type === 'user').length > 0">
                        <div class="p-2 border-t border-gray-100">
                            <p class="px-2 py-1 text-xs font-medium text-gray-500 uppercase">Thành viên</p>
                            <template x-for="item in results.filter(r => r.type === 'user')" :key="item.id">
                                <div class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                        <template x-if="item.avatar_url">
                                            <img :src="'/php/' + item.avatar_url" class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="!item.avatar_url">
                                            <span class="text-sm font-medium" x-text="(item.full_name || 'U').charAt(0).toUpperCase()"></span>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate" x-text="item.full_name"></p>
                                        <p class="text-xs text-gray-500" x-text="item.email"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex items-center justify-between px-4 py-2 border-t border-gray-200 bg-gray-50 text-xs text-gray-500">
                <div class="flex items-center gap-4">
                    <span><kbd class="px-1.5 py-0.5 bg-white rounded border">↑↓</kbd> để di chuyển</span>
                    <span><kbd class="px-1.5 py-0.5 bg-white rounded border">Enter</kbd> để mở</span>
                </div>
                <span><kbd class="px-1.5 py-0.5 bg-white rounded border">Esc</kbd> để đóng</span>
            </div>
        </div>
    </div>
</div>

<script>
function searchModal() {
    return {
        open: false,
        query: '',
        results: [],
        loading: false,
        selectedIndex: 0,
        
        init() {
            // Listen for Ctrl+K
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    this.toggle();
                }
            });
        },
        
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    document.getElementById('search-input')?.focus();
                });
            }
        },
        
        close() {
            this.open = false;
            this.query = '';
            this.results = [];
        },
        
        async search() {
            if (!this.query || this.query.length < 2) {
                this.results = [];
                return;
            }
            
            this.loading = true;
            this.selectedIndex = 0;
            
            try {
                const response = await fetch('/php/api/search.php?q=' + encodeURIComponent(this.query));
                const data = await response.json();
                
                if (data.success) {
                    this.results = data.results || [];
                }
            } catch (err) {
                console.error('Search error:', err);
            } finally {
                this.loading = false;
                this.$nextTick(() => lucide.createIcons());
            }
        },
        
        selectNext() {
            if (this.selectedIndex < this.results.length - 1) {
                this.selectedIndex++;
            }
        },
        
        selectPrev() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },
        
        openSelected() {
            const item = this.results[this.selectedIndex];
            if (item) {
                if (item.type === 'project') {
                    window.location.href = '/php/project-detail.php?id=' + item.id;
                } else if (item.type === 'task') {
                    window.location.href = '/php/task-detail.php?id=' + item.id;
                }
            }
        }
    };
}

// Global function to open search modal
function openSearchModal() {
    const modal = document.getElementById('search-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.getElementById('search-input')?.focus();
    }
}
</script>
