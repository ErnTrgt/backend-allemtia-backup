/**
 * ALLEMTIA Seller Panel - Modern Layout JavaScript
 * Interactive Features & Animations
 * Version: 1.0.0
 * Author: ALLEMTIA Development Team
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // Global Variables & State
    // ========================================
    let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    let currentTheme = localStorage.getItem('theme') || 'light';
    
    // ========================================
    // Sidebar Management
    // ========================================
    const initializeSidebar = () => {
        const sidebar = document.querySelector('.left-side-bar');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        const body = document.body;
        const overlay = document.querySelector('.mobile-menu-overlay');
        
        // Apply saved state
        if (sidebarCollapsed && window.innerWidth > 992) {
            body.classList.add('sidebar-collapsed');
        }
        
        // Toggle sidebar
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    // Mobile behavior
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                } else {
                    // Desktop behavior
                    body.classList.toggle('sidebar-collapsed');
                    sidebarCollapsed = !sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
                }
            });
        }
        
        // Close mobile menu on overlay click
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
        
        // Handle dropdown menus
        const dropdownToggles = document.querySelectorAll('.nav-link.dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const dropdown = parent.querySelector('.nav-dropdown');
                
                // Close other dropdowns
                document.querySelectorAll('.nav-dropdown.show').forEach(item => {
                    if (item !== dropdown) {
                        item.classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('show');
            });
        });
        
        // Active state management
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
                // Open parent dropdown if exists
                const parentDropdown = link.closest('.nav-dropdown');
                if (parentDropdown) {
                    parentDropdown.classList.add('show');
                }
            }
        });
    };
    
    // ========================================
    // Header Features
    // ========================================
    const initializeHeader = () => {
        // Search functionality
        const searchInput = document.querySelector('.header-search input');
        if (searchInput) {
            searchInput.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            searchInput.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
            
            // Search on enter
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch(this.value);
                }
            });
        }
        
        // Notification dropdown
        const notificationBtn = document.querySelector('.notification-btn');
        if (notificationBtn) {
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown('notification-dropdown');
            });
        }
        
        // User menu dropdown
        const userMenu = document.querySelector('.user-menu');
        if (userMenu) {
            userMenu.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown('user-dropdown');
            });
        }
        
        // Close dropdowns on outside click
        document.addEventListener('click', function() {
            closeAllDropdowns();
        });
    };
    
    // ========================================
    // Glass Morphism Effects
    // ========================================
    const initializeGlassEffects = () => {
        // Add glass shimmer effect on hover
        const glassCards = document.querySelectorAll('.glass-card');
        glassCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('glass-hover');
            });
            
            card.addEventListener('mouseleave', function() {
                this.classList.remove('glass-hover');
            });
        });
        
        // Parallax effect on mouse move
        if (window.innerWidth > 768) {
            document.addEventListener('mousemove', function(e) {
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                
                // Move background gradients slightly
                const gradients = document.querySelectorAll('.glass-gradient');
                gradients.forEach(gradient => {
                    const moveX = (x - 0.5) * 20;
                    const moveY = (y - 0.5) * 20;
                    gradient.style.transform = `translate(${moveX}px, ${moveY}px)`;
                });
            });
        }
    };
    
    // ========================================
    // Form Enhancements
    // ========================================
    const initializeForms = () => {
        // Glass input effects
        const inputs = document.querySelectorAll('.glass-input');
        inputs.forEach(input => {
            // Add wrapper if not exists
            if (!input.parentElement.classList.contains('glass-input-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'glass-input-wrapper';
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);
            }
            
            // Focus effects
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
                if (this.value) {
                    this.parentElement.classList.add('has-value');
                } else {
                    this.parentElement.classList.remove('has-value');
                }
            });
        });
        
        // Button ripple effect
        const buttons = document.querySelectorAll('.btn-glass, .btn-glass-primary');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                ripple.className = 'ripple';
                
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    };
    
    // ========================================
    // Responsive Handling
    // ========================================
    const handleResize = () => {
        const width = window.innerWidth;
        const sidebar = document.querySelector('.left-side-bar');
        const overlay = document.querySelector('.mobile-menu-overlay');
        
        if (width > 992) {
            // Desktop mode
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        } else {
            // Mobile mode
            document.body.classList.remove('sidebar-collapsed');
        }
    };
    
    // ========================================
    // Utility Functions
    // ========================================
    const toggleDropdown = (dropdownClass) => {
        const dropdown = document.querySelector(`.${dropdownClass}`);
        if (dropdown) {
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.remove('show');
                }
            });
            
            dropdown.classList.toggle('show');
        }
    };
    
    const closeAllDropdowns = () => {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    };
    
    const performSearch = (query) => {
        if (query.trim()) {
            console.log('Searching for:', query);
            // Implement search functionality
            // This would typically make an AJAX request to search endpoint
        }
    };
    
    // ========================================
    // Loading States
    // ========================================
    const showLoading = (element) => {
        element.classList.add('glass-loading');
        element.setAttribute('disabled', 'disabled');
    };
    
    const hideLoading = (element) => {
        element.classList.remove('glass-loading');
        element.removeAttribute('disabled');
    };
    
    // ========================================
    // Toast Notifications
    // ========================================
    window.showToast = (message, type = 'info') => {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type} fade-in`;
        toast.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    };
    
    // ========================================
    // Page Transitions
    // ========================================
    const initializePageTransitions = () => {
        // Add fade-in animation to main content
        const mainContainer = document.querySelector('.main-container');
        if (mainContainer) {
            mainContainer.classList.add('fade-in');
        }
        
        // Animate glass cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.glass-card').forEach(card => {
            observer.observe(card);
        });
    };
    
    // ========================================
    // Initialize Everything
    // ========================================
    const init = () => {
        initializeSidebar();
        initializeHeader();
        initializeGlassEffects();
        initializeForms();
        initializePageTransitions();
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(handleResize, 250);
        });
        
        // Remove loading class from body
        document.body.classList.remove('loading');
    };
    
    // Start initialization
    init();
    
    // ========================================
    // Public API
    // ========================================
    window.ModernLayout = {
        showLoading,
        hideLoading,
        showToast,
        toggleSidebar: () => {
            document.querySelector('.sidebar-toggle').click();
        },
        refresh: init
    };
});

// ========================================
// Ripple Effect Styles
// ========================================
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 9999;
        max-width: 400px;
    }
    
    .toast-success {
        border-left: 4px solid var(--success);
    }
    
    .toast-error {
        border-left: 4px solid var(--danger);
    }
    
    .toast-info {
        border-left: 4px solid var(--info);
    }
    
    .fade-out {
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.3s ease;
    }
    
    .glass-input-wrapper.focused {
        transform: scale(1.02);
    }
    
    .glass-card.animate-in {
        animation: slideUp 0.6s ease forwards;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);