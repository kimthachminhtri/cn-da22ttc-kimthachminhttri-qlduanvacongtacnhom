/**
 * TaskFlow - Main Application JavaScript
 * Version: 2.0.2
 * 
 * Bao gồm:
 * - Loading states cho AJAX calls
 * - Confirmation dialogs
 * - Keyboard shortcuts
 * - Error message localization
 */

// ============================================
// LOADING STATES (LOW-001)
// ============================================

const LoadingState = {
    // Hiển thị loading overlay toàn trang
    showFullPage(message = 'Đang xử lý...') {
        if (document.getElementById('loading-overlay')) return;
        
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]';
        overlay.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex flex-col items-center gap-4 shadow-xl">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent"></div>
                <p class="text-gray-700 dark:text-gray-300">${message}</p>
            </div>
        `;
        document.body.appendChild(overlay);
    },

    // Ẩn loading overlay
    hideFullPage() {
        document.getElementById('loading-overlay')?.remove();
    },

    // Hiển thị loading trên button
    showButton(button, text = 'Đang xử lý...') {
        if (!button) return;
        button.dataset.originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `
            <span class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${text}
            </span>
        `;
    },

    // Khôi phục button
    hideButton(button) {
        if (!button || !button.dataset.originalText) return;
        button.disabled = false;
        button.innerHTML = button.dataset.originalText;
        delete button.dataset.originalText;
    },

    // Hiển thị skeleton loading
    showSkeleton(container, count = 3, type = 'card') {
        if (!container) return;
        
        const skeletons = {
            card: `
                <div class="animate-pulse rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="h-10 w-10 rounded-lg bg-gray-200"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3 mb-3"></div>
                    <div class="h-2 bg-gray-200 rounded-full w-full"></div>
                </div>
            `,
            list: `
                <div class="animate-pulse px-6 py-4 border-b border-gray-100">
                    <div class="flex items-start gap-4">
                        <div class="h-4 w-4 rounded bg-gray-200 mt-1"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="flex gap-2">
                                <div class="h-3 bg-gray-200 rounded w-16"></div>
                                <div class="h-3 bg-gray-200 rounded w-24"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            stats: `
                <div class="animate-pulse rounded-xl border border-gray-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="h-3 bg-gray-200 rounded w-20 mb-2"></div>
                            <div class="h-6 bg-gray-200 rounded w-12"></div>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-gray-200"></div>
                    </div>
                </div>
            `
        };
        
        container.innerHTML = Array(count).fill(skeletons[type] || skeletons.card).join('');
    },
    
    // Hiển thị skeleton cho grid
    showGridSkeleton(container, count = 4, columns = 4) {
        if (!container) return;
        container.className = `grid gap-4 sm:grid-cols-2 lg:grid-cols-${columns}`;
        this.showSkeleton(container, count, 'card');
    },
    
    // Hiển thị skeleton cho list
    showListSkeleton(container, count = 5) {
        if (!container) return;
        this.showSkeleton(container, count, 'list');
    }
};

// ============================================
// CONFIRMATION DIALOGS (LOW-002)
// ============================================

const ConfirmDialog = {
    // Hiển thị dialog xác nhận
    show(options = {}) {
        return new Promise((resolve) => {
            const {
                title = 'Xác nhận',
                message = 'Bạn có chắc chắn muốn thực hiện hành động này?',
                confirmText = 'Xác nhận',
                cancelText = 'Hủy',
                type = 'warning', // warning, danger, info
                onConfirm = null,
                onCancel = null
            } = options;

            const colors = {
                warning: 'bg-yellow-500 hover:bg-yellow-600',
                danger: 'bg-red-500 hover:bg-red-600',
                info: 'bg-blue-500 hover:bg-blue-600'
            };

            const icons = {
                warning: `<svg class="h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>`,
                danger: `<svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>`,
                info: `<svg class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`
            };

            // Remove existing dialog
            document.getElementById('confirm-dialog')?.remove();

            const dialog = document.createElement('div');
            dialog.id = 'confirm-dialog';
            dialog.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-[9999] fade-in';
            dialog.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
                    <div class="flex flex-col items-center text-center">
                        ${icons[type]}
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">${title}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">${message}</p>
                    </div>
                    <div class="mt-6 flex gap-3 justify-center">
                        <button id="confirm-cancel" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            ${cancelText}
                        </button>
                        <button id="confirm-ok" class="px-4 py-2 rounded-lg text-white ${colors[type]} transition-colors">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(dialog);

            // Event handlers
            const handleConfirm = () => {
                dialog.remove();
                onConfirm?.();
                resolve(true);
            };

            const handleCancel = () => {
                dialog.remove();
                onCancel?.();
                resolve(false);
            };

            dialog.querySelector('#confirm-ok').addEventListener('click', handleConfirm);
            dialog.querySelector('#confirm-cancel').addEventListener('click', handleCancel);
            dialog.addEventListener('click', (e) => {
                if (e.target === dialog) handleCancel();
            });

            // ESC to cancel
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    handleCancel();
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);
        });
    },

    // Shortcut cho delete confirmation
    async confirmDelete(itemName = 'mục này') {
        return this.show({
            title: 'Xác nhận xóa',
            message: `Bạn có chắc chắn muốn xóa ${itemName}? Hành động này không thể hoàn tác.`,
            confirmText: 'Xóa',
            cancelText: 'Hủy',
            type: 'danger'
        });
    }
};

// ============================================
// ERROR MESSAGES LOCALIZATION (LOW-003)
// ============================================

const ErrorMessages = {
    // Mapping từ tiếng Anh sang tiếng Việt
    translations: {
        // Network errors
        'Network Error': 'Lỗi kết nối mạng',
        'Failed to fetch': 'Không thể kết nối đến server',
        'Request timeout': 'Yêu cầu quá thời gian chờ',
        
        // HTTP errors
        '400': 'Yêu cầu không hợp lệ',
        '401': 'Vui lòng đăng nhập để tiếp tục',
        '403': 'Bạn không có quyền thực hiện hành động này',
        '404': 'Không tìm thấy dữ liệu',
        '405': 'Phương thức không được hỗ trợ',
        '409': 'Dữ liệu đã được cập nhật bởi người khác',
        '422': 'Dữ liệu không hợp lệ',
        '429': 'Quá nhiều yêu cầu, vui lòng thử lại sau',
        '500': 'Lỗi server, vui lòng thử lại sau',
        '502': 'Server tạm thời không khả dụng',
        '503': 'Dịch vụ đang bảo trì',
        
        // Validation errors
        'required': 'Trường này là bắt buộc',
        'email': 'Email không hợp lệ',
        'min': 'Giá trị quá ngắn',
        'max': 'Giá trị quá dài',
        'unique': 'Giá trị đã tồn tại',
        'confirmed': 'Xác nhận không khớp',
        
        // Common errors
        'Something went wrong': 'Đã xảy ra lỗi, vui lòng thử lại',
        'Invalid credentials': 'Email hoặc mật khẩu không đúng',
        'Session expired': 'Phiên đăng nhập đã hết hạn',
        'Permission denied': 'Bạn không có quyền truy cập',
    },

    // Dịch message
    translate(message) {
        if (!message) return 'Đã xảy ra lỗi';
        
        // Check direct translation
        if (this.translations[message]) {
            return this.translations[message];
        }
        
        // Check HTTP status code
        const statusMatch = message.match(/^(\d{3})/);
        if (statusMatch && this.translations[statusMatch[1]]) {
            return this.translations[statusMatch[1]];
        }
        
        // Return original if no translation
        return message;
    },

    // Xử lý error từ API response
    handleApiError(error) {
        if (error.response) {
            const status = error.response.status;
            const data = error.response.data;
            
            if (data?.error) {
                return this.translate(data.error);
            }
            
            return this.translate(String(status));
        }
        
        return this.translate(error.message || 'Something went wrong');
    }
};

// ============================================
// KEYBOARD SHORTCUTS (LOW-004)
// ============================================

const KeyboardShortcuts = {
    shortcuts: [],
    enabled: true,

    // Đăng ký shortcut
    register(key, callback, description = '') {
        this.shortcuts.push({ key, callback, description });
    },

    // Khởi tạo shortcuts mặc định
    init() {
        // Global shortcuts
        this.register('ctrl+k', () => {
            document.getElementById('search-modal')?.classList.remove('hidden');
            document.getElementById('search-input')?.focus();
        }, 'Mở tìm kiếm');

        this.register('ctrl+n', () => {
            window.location.href = '/php/tasks.php?action=new';
        }, 'Tạo task mới');

        this.register('ctrl+shift+p', () => {
            window.location.href = '/php/projects.php?action=new';
        }, 'Tạo project mới');

        this.register('escape', () => {
            // Close any open modal
            document.querySelectorAll('.modal:not(.hidden)').forEach(m => m.classList.add('hidden'));
            document.getElementById('search-modal')?.classList.add('hidden');
            document.getElementById('confirm-dialog')?.remove();
        }, 'Đóng modal/dialog');

        this.register('?', () => {
            this.showHelp();
        }, 'Hiển thị trợ giúp phím tắt');

        // Listen for keyboard events
        document.addEventListener('keydown', (e) => this.handleKeydown(e));
    },

    // Xử lý keydown
    handleKeydown(e) {
        if (!this.enabled) return;
        
        // Ignore if typing in input/textarea
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
            if (e.key !== 'Escape') return;
        }

        const key = this.getKeyString(e);
        const shortcut = this.shortcuts.find(s => s.key === key);
        
        if (shortcut) {
            e.preventDefault();
            shortcut.callback();
        }
    },

    // Chuyển event thành key string
    getKeyString(e) {
        const parts = [];
        if (e.ctrlKey || e.metaKey) parts.push('ctrl');
        if (e.shiftKey) parts.push('shift');
        if (e.altKey) parts.push('alt');
        parts.push(e.key.toLowerCase());
        return parts.join('+');
    },

    // Hiển thị help dialog
    showHelp() {
        document.getElementById('shortcuts-help')?.remove();

        const dialog = document.createElement('div');
        dialog.id = 'shortcuts-help';
        dialog.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-[9999] fade-in';
        dialog.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full mx-4 shadow-xl max-h-[80vh] overflow-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Phím tắt</h3>
                    <button onclick="this.closest('#shortcuts-help').remove()" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="space-y-2">
                    ${this.shortcuts.map(s => `
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">${s.description}</span>
                            <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono">${s.key.replace('ctrl', 'Ctrl').replace('shift', 'Shift').replace('+', ' + ')}</kbd>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        document.body.appendChild(dialog);
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) dialog.remove();
        });
    }
};

// ============================================
// ENHANCED FETCH WITH LOADING & ERROR HANDLING
// ============================================

async function fetchWithUI(url, options = {}, uiOptions = {}) {
    const {
        loadingMessage = 'Đang xử lý...',
        showLoading = true,
        button = null
    } = uiOptions;

    try {
        if (showLoading) {
            if (button) {
                LoadingState.showButton(button, loadingMessage);
            } else {
                LoadingState.showFullPage(loadingMessage);
            }
        }

        const response = await fetch(url, options);
        const data = await response.json();

        if (!response.ok) {
            throw { response: { status: response.status, data } };
        }

        return data;

    } catch (error) {
        const message = ErrorMessages.handleApiError(error);
        showToast(message, 'error');
        throw error;

    } finally {
        if (showLoading) {
            if (button) {
                LoadingState.hideButton(button);
            } else {
                LoadingState.hideFullPage();
            }
        }
    }
}

// ============================================
// DELETE WITH CONFIRMATION
// ============================================

async function deleteWithConfirm(url, itemName, options = {}) {
    const confirmed = await ConfirmDialog.confirmDelete(itemName);
    
    if (!confirmed) return false;

    try {
        LoadingState.showFullPage('Đang xóa...');
        
        const response = await fetch(url, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            ...options
        });

        const data = await response.json();

        if (data.success) {
            showToast('Xóa thành công', 'success');
            return true;
        } else {
            showToast(ErrorMessages.translate(data.error), 'error');
            return false;
        }

    } catch (error) {
        showToast(ErrorMessages.handleApiError(error), 'error');
        return false;

    } finally {
        LoadingState.hideFullPage();
    }
}

// ============================================
// INITIALIZE
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    KeyboardShortcuts.init();
    
    // Add confirmation to all delete buttons
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const url = btn.dataset.url || btn.href;
            const itemName = btn.dataset.itemName || 'mục này';
            
            const deleted = await deleteWithConfirm(url, itemName);
            if (deleted && btn.dataset.redirect) {
                window.location.href = btn.dataset.redirect;
            } else if (deleted) {
                btn.closest('tr, .card, [data-item]')?.remove();
            }
        });
    });
});

// Export for global use
window.LoadingState = LoadingState;
window.ConfirmDialog = ConfirmDialog;
window.ErrorMessages = ErrorMessages;
window.KeyboardShortcuts = KeyboardShortcuts;
window.fetchWithUI = fetchWithUI;
window.deleteWithConfirm = deleteWithConfirm;
