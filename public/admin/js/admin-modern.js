/* ALLEMTIA Admin Panel - Modern JavaScript Framework
   ================================================= */

// Global namespace
const AdminPanel = {
    // Configuration
    config: {
        animationDuration: 300,
        alertDuration: 5000,
        transitions: {
            fast: 150,
            base: 300,
            slow: 500
        }
    },

    // Initialize
    init() {
        this.initializeTooltips();
        this.initializeAlerts();
        this.initializeFormValidation();
        this.initializeAnimations();
        this.initializePageTransitions();
        this.bindGlobalEvents();
    },

    // Initialize tooltips
    initializeTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip);
            element.addEventListener('mouseleave', this.hideTooltip);
        });
    },

    // Show tooltip
    showTooltip(e) {
        const text = e.target.getAttribute('data-tooltip');
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = text;
        
        document.body.appendChild(tooltip);
        
        const rect = e.target.getBoundingClientRect();
        tooltip.style.top = `${rect.top - tooltip.offsetHeight - 8}px`;
        tooltip.style.left = `${rect.left + (rect.width - tooltip.offsetWidth) / 2}px`;
        
        setTimeout(() => tooltip.classList.add('show'), 10);
    },

    // Hide tooltip
    hideTooltip() {
        const tooltips = document.querySelectorAll('.tooltip');
        tooltips.forEach(tooltip => {
            tooltip.classList.remove('show');
            setTimeout(() => tooltip.remove(), 300);
        });
    },

    // Initialize alerts
    initializeAlerts() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            // Auto-hide alerts after specified duration
            if (!alert.hasAttribute('data-persist')) {
                setTimeout(() => {
                    this.hideAlert(alert);
                }, this.config.alertDuration);
            }

            // Close button functionality
            const closeBtn = alert.querySelector('.alert-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.hideAlert(alert));
            }
        });
    },

    // Hide alert with animation
    hideAlert(alert) {
        alert.style.transition = `opacity ${this.config.transitions.base}ms ease-out`;
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), this.config.transitions.base);
    },

    // Form validation
    initializeFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });

            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateInput(input));
                input.addEventListener('input', () => this.clearInputError(input));
            });
        });
    },

    // Validate form
    validateForm(form) {
        const inputs = form.querySelectorAll('[required], [data-validate]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateInput(input)) {
                isValid = false;
            }
        });

        return isValid;
    },

    // Validate input
    validateInput(input) {
        let isValid = true;
        const value = input.value.trim();

        // Required validation
        if (input.hasAttribute('required') && !value) {
            this.showInputError(input, 'Bu alan zorunludur');
            isValid = false;
        }

        // Email validation
        if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showInputError(input, 'Geçerli bir e-posta adresi girin');
                isValid = false;
            }
        }

        // Custom validation
        const validateType = input.getAttribute('data-validate');
        if (validateType && value) {
            switch (validateType) {
                case 'phone':
                    const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                    if (!phoneRegex.test(value)) {
                        this.showInputError(input, 'Geçerli bir telefon numarası girin');
                        isValid = false;
                    }
                    break;
                case 'number':
                    if (isNaN(value)) {
                        this.showInputError(input, 'Sadece sayı giriniz');
                        isValid = false;
                    }
                    break;
            }
        }

        if (isValid) {
            this.clearInputError(input);
        }

        return isValid;
    },

    // Show input error
    showInputError(input, message) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        // Remove existing error
        this.clearInputError(input);

        // Add error class
        input.classList.add('is-invalid');

        // Create error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        formGroup.appendChild(errorDiv);
    },

    // Clear input error
    clearInputError(input) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        input.classList.remove('is-invalid');
        const errorDiv = formGroup.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    },

    // Initialize animations
    initializeAnimations() {
        // Intersection Observer for scroll animations
        const animatedElements = document.querySelectorAll('[data-animate]');
        
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const animation = entry.target.getAttribute('data-animate');
                        entry.target.classList.add(`animate-${animation}`);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            animatedElements.forEach(element => {
                observer.observe(element);
            });
        } else {
            // Fallback for older browsers
            animatedElements.forEach(element => {
                const animation = element.getAttribute('data-animate');
                element.classList.add(`animate-${animation}`);
            });
        }
    },

    // Page transitions
    initializePageTransitions() {
        const links = document.querySelectorAll('a[href^="/admin"]:not([data-no-transition])');
        
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                
                e.preventDefault();
                const href = link.getAttribute('href');
                
                // Add transition effect
                document.body.style.opacity = '0';
                document.body.style.transition = `opacity ${this.config.transitions.base}ms ease-out`;
                
                setTimeout(() => {
                    window.location.href = href;
                }, this.config.transitions.base);
            });
        });
    },

    // Bind global events
    bindGlobalEvents() {
        // Escape key to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeActiveModal();
            }
        });

        // Click outside to close dropdowns
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown')) {
                this.closeAllDropdowns();
            }
        });
    },

    // Modal functions
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop';
        backdrop.addEventListener('click', () => this.closeModal(modalId));
        document.body.appendChild(backdrop);
        
        setTimeout(() => backdrop.classList.add('show'), 10);
    },

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
            setTimeout(() => backdrop.remove(), this.config.transitions.base);
        }
    },

    closeActiveModal() {
        const activeModal = document.querySelector('.modal.show');
        if (activeModal) {
            this.closeModal(activeModal.id);
        }
    },

    // Dropdown functions
    toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;

        const isOpen = dropdown.classList.contains('show');
        
        // Close all dropdowns first
        this.closeAllDropdowns();
        
        // Toggle current dropdown
        if (!isOpen) {
            dropdown.classList.add('show');
        }
    },

    closeAllDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    },

    // Utility functions
    showLoading(element) {
        element.classList.add('loading');
        element.disabled = true;
    },

    hideLoading(element) {
        element.classList.remove('loading');
        element.disabled = false;
    },

    // Toast notification
    showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="bi bi-${this.getToastIcon(type)}"></i>
            <span>${message}</span>
        `;
        
        const container = document.querySelector('.toast-container') || this.createToastContainer();
        container.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 10);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), this.config.transitions.base);
        }, duration);
    },

    createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    },

    getToastIcon(type) {
        const icons = {
            success: 'check-circle-fill',
            error: 'x-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };
        return icons[type] || icons.info;
    },

    // Copy to clipboard
    copyToClipboard(text, showNotification = true) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                if (showNotification) {
                    this.showToast('Panoya kopyalandı', 'success');
                }
            }).catch(() => {
                this.fallbackCopyToClipboard(text, showNotification);
            });
        } else {
            this.fallbackCopyToClipboard(text, showNotification);
        }
    },

    fallbackCopyToClipboard(text, showNotification) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            if (showNotification) {
                this.showToast('Panoya kopyalandı', 'success');
            }
        } catch (err) {
            if (showNotification) {
                this.showToast('Kopyalama başarısız', 'error');
            }
        }
        
        document.body.removeChild(textArea);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    AdminPanel.init();
});

// Export for use in other modules
window.AdminPanel = AdminPanel;