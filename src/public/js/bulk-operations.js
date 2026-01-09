/**
 * Bulk Operations JavaScript Helper
 * Hỗ trợ thao tác hàng loạt cho Tasks, Projects, Users, Documents
 */

class BulkOperations {
    constructor(options = {}) {
        this.entity = options.entity || 'tasks';
        this.selectedIds = new Set();
        this.checkboxSelector = options.checkboxSelector || '.bulk-checkbox';
        this.selectAllSelector = options.selectAllSelector || '#select-all';
        this.toolbarSelector = options.toolbarSelector || '#bulk-toolbar';
        this.countSelector = options.countSelector || '#selected-count';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateToolbar();
    }
    
    bindEvents() {
        // Select all checkbox
        const selectAll = document.querySelector(this.selectAllSelector);
        if (selectAll) {
            selectAll.addEventListener('change', (e) => this.toggleSelectAll(e.target.checked));
        }
        
        // Individual checkboxes
        document.querySelectorAll(this.checkboxSelector).forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.toggleItem(e.target.dataset.id, e.target.checked);
            });
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.clearSelection();
            }
        });
    }
    
    toggleSelectAll(checked) {
        document.querySelectorAll(this.checkboxSelector).forEach(checkbox => {
            checkbox.checked = checked;
            if (checked) {
                this.selectedIds.add(checkbox.dataset.id);
            } else {
                this.selectedIds.delete(checkbox.dataset.id);
            }
        });
        this.updateToolbar();
    }
    
    toggleItem(id, checked) {
        if (checked) {
            this.selectedIds.add(id);
        } else {
            this.selectedIds.delete(id);
        }
        this.updateSelectAllState();
        this.updateToolbar();
    }
    
    updateSelectAllState() {
        const selectAll = document.querySelector(this.selectAllSelector);
        const checkboxes = document.querySelectorAll(this.checkboxSelector);
        const checkedCount = document.querySelectorAll(`${this.checkboxSelector}:checked`).length;
        
        if (selectAll) {
            selectAll.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
        }
    }
    
    updateToolbar() {
        const toolbar = document.querySelector(this.toolbarSelector);
        const countEl = document.querySelector(this.countSelector);
        
        if (toolbar) {
            if (this.selectedIds.size > 0) {
                toolbar.classList.remove('hidden');
                toolbar.classList.add('flex');
            } else {
                toolbar.classList.add('hidden');
                toolbar.classList.remove('flex');
            }
        }
        
        if (countEl) {
            countEl.textContent = this.selectedIds.size;
        }
    }
    
    clearSelection() {
        this.selectedIds.clear();
        document.querySelectorAll(this.checkboxSelector).forEach(cb => cb.checked = false);
        const selectAll = document.querySelector(this.selectAllSelector);
        if (selectAll) selectAll.checked = false;
        this.updateToolbar();
    }
    
    getSelectedIds() {
        return Array.from(this.selectedIds);
    }
    
    async execute(operation, data = {}) {
        const ids = this.getSelectedIds();
        
        if (ids.length === 0) {
            showToast('Vui lòng chọn ít nhất một mục', 'warning');
            return false;
        }
        
        try {
            const response = await fetch('/php/api/bulk-operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': this.csrfToken
                },
                body: JSON.stringify({
                    entity: this.entity,
                    operation: operation,
                    ids: ids,
                    data: data
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                this.clearSelection();
                
                // Reload page or update UI
                if (typeof window.refreshData === 'function') {
                    window.refreshData();
                } else {
                    setTimeout(() => location.reload(), 1000);
                }
                
                return true;
            } else {
                showToast(result.error || 'Có lỗi xảy ra', 'error');
                return false;
            }
        } catch (error) {
            console.error('Bulk operation error:', error);
            showToast('Lỗi kết nối server', 'error');
            return false;
        }
    }
    
    // Convenience methods
    async updateStatus(status) {
        return this.execute('update_status', { status });
    }
    
    async updatePriority(priority) {
        return this.execute('update_priority', { priority });
    }
    
    async assignUser(assigneeId) {
        return this.execute('assign_user', { assignee_id: assigneeId });
    }
    
    async delete() {
        const count = this.selectedIds.size;
        const confirmed = await this.confirm(
            `Xác nhận xóa ${count} mục?`,
            'Hành động này không thể hoàn tác!'
        );
        
        if (confirmed) {
            return this.execute('delete');
        }
        return false;
    }
    
    async archive() {
        return this.execute('archive');
    }
    
    async activate() {
        return this.execute('activate');
    }
    
    async deactivate() {
        return this.execute('deactivate');
    }
    
    async changeRole(role) {
        return this.execute('change_role', { role });
    }
    
    async move(projectId) {
        return this.execute('move', { project_id: projectId });
    }
    
    confirm(title, message) {
        return new Promise((resolve) => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    resolve(result.isConfirmed);
                });
            } else {
                resolve(confirm(`${title}\n${message}`));
            }
        });
    }
}

// Toast notification helper
function showToast(message, type = 'info') {
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    } else if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        alert(message);
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BulkOperations;
}
