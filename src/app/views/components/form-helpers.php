<?php
/**
 * Form Helper Components
 * 
 * Provides reusable form elements with:
 * - Loading states
 * - Inline error messages
 * - Accessibility attributes
 */
?>

<!-- Form Error Display Component -->
<template id="form-error-template">
    <div class="form-error mt-1 flex items-center gap-1 text-sm text-red-600" role="alert">
        <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <span class="error-message"></span>
    </div>
</template>

<!-- Submit Button with Loading State -->
<template id="submit-button-template">
    <button type="submit" class="submit-btn inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
        <span class="btn-text"></span>
        <svg class="btn-spinner hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>
</template>

<script>
/**
 * Enhanced Form Handler
 * - Shows loading state on submit
 * - Displays inline errors
 * - Prevents double submission
 */
class EnhancedForm {
    constructor(form, options = {}) {
        this.form = form;
        this.options = {
            onSuccess: options.onSuccess || null,
            onError: options.onError || null,
            redirectOnSuccess: options.redirectOnSuccess || null,
            submitText: options.submitText || 'Lưu',
            loadingText: options.loadingText || 'Đang xử lý...',
            ...options
        };
        this.submitBtn = form.querySelector('[type="submit"]');
        this.isSubmitting = false;
        
        this.init();
    }
    
    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Clear errors on input
        this.form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        if (this.isSubmitting) return;
        
        this.clearAllErrors();
        this.setLoading(true);
        
        try {
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData.entries());
            
            const response = await fetch(this.form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (this.options.onSuccess) {
                    this.options.onSuccess(result);
                } else if (this.options.redirectOnSuccess) {
                    const redirectUrl = typeof this.options.redirectOnSuccess === 'function' 
                        ? this.options.redirectOnSuccess(result) 
                        : this.options.redirectOnSuccess;
                    window.location.href = redirectUrl;
                } else if (result.redirect) {
                    window.location.href = result.redirect;
                }
                
                // Show success toast
                if (typeof showToast === 'function') {
                    showToast(result.message || 'Thành công!', 'success');
                }
            } else {
                this.handleErrors(result);
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.showGeneralError('Lỗi kết nối. Vui lòng thử lại.');
            
            if (this.options.onError) {
                this.options.onError(error);
            }
        } finally {
            this.setLoading(false);
        }
    }
    
    handleErrors(result) {
        if (result.errors && typeof result.errors === 'object') {
            // Field-specific errors
            Object.entries(result.errors).forEach(([field, message]) => {
                this.showFieldError(field, message);
            });
        } else if (result.error) {
            // General error
            this.showGeneralError(result.error);
        }
        
        if (this.options.onError) {
            this.options.onError(result);
        }
    }
    
    showFieldError(fieldName, message) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        if (!field) {
            this.showGeneralError(message);
            return;
        }
        
        // Add error styling to field
        field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.remove('border-gray-300', 'focus:border-primary', 'focus:ring-primary');
        field.setAttribute('aria-invalid', 'true');
        
        // Create error message element
        const errorId = `${fieldName}-error`;
        field.setAttribute('aria-describedby', errorId);
        
        const errorEl = document.createElement('div');
        errorEl.id = errorId;
        errorEl.className = 'form-field-error mt-1 flex items-center gap-1 text-sm text-red-600';
        errorEl.setAttribute('role', 'alert');
        errorEl.innerHTML = `
            <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>${this.escapeHtml(message)}</span>
        `;
        
        // Insert after field or its parent wrapper
        const wrapper = field.closest('.form-group') || field.parentElement;
        wrapper.appendChild(errorEl);
    }
    
    showGeneralError(message) {
        // Remove existing general error
        this.form.querySelector('.form-general-error')?.remove();
        
        const errorEl = document.createElement('div');
        errorEl.className = 'form-general-error mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3';
        errorEl.setAttribute('role', 'alert');
        errorEl.innerHTML = `
            <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-red-800">Có lỗi xảy ra</p>
                <p class="text-sm text-red-700 mt-1">${this.escapeHtml(message)}</p>
            </div>
        `;
        
        this.form.insertBefore(errorEl, this.form.firstChild);
        
        // Scroll to error
        errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    clearFieldError(field) {
        field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.add('border-gray-300', 'focus:border-primary', 'focus:ring-primary');
        field.removeAttribute('aria-invalid');
        field.removeAttribute('aria-describedby');
        
        const errorEl = field.parentElement.querySelector('.form-field-error');
        errorEl?.remove();
    }
    
    clearAllErrors() {
        this.form.querySelectorAll('.form-field-error, .form-general-error').forEach(el => el.remove());
        this.form.querySelectorAll('[aria-invalid]').forEach(field => {
            this.clearFieldError(field);
        });
    }
    
    setLoading(loading) {
        this.isSubmitting = loading;
        
        if (this.submitBtn) {
            this.submitBtn.disabled = loading;
            
            if (loading) {
                this.submitBtn.dataset.originalText = this.submitBtn.innerHTML;
                this.submitBtn.innerHTML = `
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>${this.options.loadingText}</span>
                `;
            } else if (this.submitBtn.dataset.originalText) {
                this.submitBtn.innerHTML = this.submitBtn.dataset.originalText;
            }
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Auto-initialize forms with data-enhanced attribute
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-enhanced]').forEach(form => {
        new EnhancedForm(form, {
            redirectOnSuccess: form.dataset.redirect || null,
            submitText: form.dataset.submitText || 'Lưu',
            loadingText: form.dataset.loadingText || 'Đang xử lý...'
        });
    });
});

// Export for manual use
window.EnhancedForm = EnhancedForm;
</script>
