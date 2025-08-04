/* ALLEMTIA Admin Panel - Layout JavaScript
   ======================================= */

const AdminLayout = {
    // State
    state: {
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        mobileMenuOpen: false,
        currentPage: window.location.pathname
    },

    // Initialize
    init() {
        this.initializeSidebar();
        this.initializeMenuToggle();
        this.initializeNavigation();
        this.initializeDropdowns();
        this.initializeMobileHandlers();
        this.setActiveNavItem();
        this.bindWindowResize();
    },

    // Initialize sidebar state
    initializeSidebar() {
        const sidebar = document.querySelector('.admin-sidebar');
        if (!sidebar) return;

        if (this.state.sidebarCollapsed && window.innerWidth >= 1024) {
            sidebar.classList.add('collapsed');
        }
    },

    // Menu toggle functionality
    initializeMenuToggle() {
        const toggleBtn = document.querySelector('.menu-toggle');
        if (!toggleBtn) return;

        toggleBtn.addEventListener('click', () => {
            this.toggleSidebar();
        });
    },

    // Toggle sidebar
    toggleSidebar() {
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.querySelector('.mobile-overlay');
        
        if (!sidebar) return;

        if (window.innerWidth < 1024) {
            // Mobile behavior
            this.state.mobileMenuOpen = !this.state.mobileMenuOpen;
            
            if (this.state.mobileMenuOpen) {
                sidebar.classList.add('show');
                overlay?.classList.add('show');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('show');
                overlay?.classList.remove('show');
                document.body.style.overflow = '';
            }
        } else {
            // Desktop behavior
            this.state.sidebarCollapsed = !this.state.sidebarCollapsed;
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', this.state.sidebarCollapsed);
        }
    },

    // Initialize navigation
    initializeNavigation() {
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            // Smooth page transitions
            if (!link.hasAttribute('data-no-transition')) {
                link.addEventListener('click', (e) => {
                    if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                    if (link.getAttribute('href').startsWith('#')) return;
                    
                    e.preventDefault();
                    const href = link.getAttribute('href');
                    
                    // Add loading state
                    document.body.style.opacity = '0.8';
                    document.body.style.transition = 'opacity 0.3s ease-out';
                    
                    setTimeout(() => {
                        window.location.href = href;
                    }, 300);
                });
            }

            // Tooltip for collapsed sidebar
            if (window.innerWidth >= 1024) {
                link.addEventListener('mouseenter', (e) => {
                    const sidebar = document.querySelector('.admin-sidebar');
                    if (sidebar?.classList.contains('collapsed')) {
                        const text = link.querySelector('.nav-text')?.textContent;
                        if (text) {
                            this.showTooltip(e.target, text);
                        }
                    }
                });

                link.addEventListener('mouseleave', () => {
                    this.hideTooltip();
                });
            }
        });
    },

    // Set active navigation item
    setActiveNavItem() {
        const navLinks = document.querySelectorAll('.nav-link');
        const currentPath = window.location.pathname;
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath || (href !== '/admin' && currentPath.startsWith(href))) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    },

    // Initialize dropdowns
    initializeDropdowns() {
        const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const dropdownId = toggle.getAttribute('data-dropdown-toggle');
                const dropdown = document.getElementById(dropdownId);
                
                if (!dropdown) return;
                
                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdown) {
                        menu.classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('show');
            });
        });

        // Close dropdowns on outside click
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        });
    },

    // Mobile handlers
    initializeMobileHandlers() {
        // Create mobile overlay if not exists
        if (!document.querySelector('.mobile-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'mobile-overlay';
            overlay.addEventListener('click', () => this.toggleSidebar());
            document.body.appendChild(overlay);
        }

        // Close mobile menu on nav click
        if (window.innerWidth < 1024) {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (this.state.mobileMenuOpen) {
                        this.toggleSidebar();
                    }
                });
            });
        }
    },

    // Show tooltip
    showTooltip(element, text) {
        // Remove existing tooltip
        this.hideTooltip();

        const tooltip = document.createElement('div');
        tooltip.className = 'sidebar-tooltip';
        tooltip.textContent = text;
        document.body.appendChild(tooltip);

        const rect = element.getBoundingClientRect();
        tooltip.style.top = `${rect.top + rect.height / 2}px`;
        tooltip.style.left = `${rect.right + 10}px`;
        tooltip.style.transform = 'translateY(-50%)';

        setTimeout(() => tooltip.classList.add('show'), 10);
    },

    // Hide tooltip
    hideTooltip() {
        const tooltip = document.querySelector('.sidebar-tooltip');
        if (tooltip) {
            tooltip.classList.remove('show');
            setTimeout(() => tooltip.remove(), 300);
        }
    },

    // Window resize handler
    bindWindowResize() {
        let resizeTimer;
        
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                // Reset mobile menu on desktop resize
                if (window.innerWidth >= 1024 && this.state.mobileMenuOpen) {
                    this.state.mobileMenuOpen = false;
                    document.querySelector('.admin-sidebar')?.classList.remove('show');
                    document.querySelector('.mobile-overlay')?.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }, 250);
        });
    },

    // Utility functions
    showNotificationBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge && count > 0) {
            badge.style.display = 'block';
        }
    },

    updatePageTitle(title) {
        const headerTitle = document.querySelector('.header-title');
        if (headerTitle) {
            headerTitle.textContent = title;
        }
        document.title = `${title} - ALLEMTIA Admin`;
    },

    // Search functionality
    initializeSearch() {
        const searchInput = document.querySelector('.header-search input');
        if (!searchInput) return;

        let searchTimer;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimer);
            const query = e.target.value.trim();
            
            if (query.length > 2) {
                searchTimer = setTimeout(() => {
                    this.performSearch(query);
                }, 300);
            }
        });
    },

    performSearch(query) {
        // This would typically make an API call
        console.log('Searching for:', query);
        AdminPanel.showToast(`"${query}" için arama yapılıyor...`, 'info');
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    AdminLayout.init();
});

// Add tooltip styles dynamically
const tooltipStyles = `
    .sidebar-tooltip {
        position: fixed;
        background: var(--gray-800);
        color: white;
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-sm);
        font-size: var(--text-sm);
        z-index: var(--z-tooltip);
        opacity: 0;
        transform: translateY(-50%) translateX(-10px);
        transition: all var(--transition-base) var(--ease-out);
        pointer-events: none;
        white-space: nowrap;
    }
    
    .sidebar-tooltip.show {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
    }
    
    .sidebar-tooltip::before {
        content: '';
        position: absolute;
        top: 50%;
        left: -4px;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 4px 4px 4px 0;
        border-color: transparent var(--gray-800) transparent transparent;
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = tooltipStyles;
document.head.appendChild(styleSheet);