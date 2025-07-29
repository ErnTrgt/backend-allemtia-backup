<style>
    /* CSS Variables - Seller Panel Theme */
    :root {
        --color-dark: #0B090A;
        --color-primary: #2B2D42;
        --color-gray: #8D99AE;
        --color-light: #EDF2F4;
        --color-accent: #EF233C;
        --color-accent-dark: #D90429;
        --color-white: #FFFFFF;
        
        /* Layout Variables */
        --header-height: 70px;
        --sidebar-width: 280px;
        --sidebar-collapsed-width: 80px;
        
        /* Transitions */
        --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        
        /* Z-Index */
        --z-dropdown: 100;
        --z-header: 200;
        --z-sidebar: 150;
        --z-mobile-menu: 300;
        --z-modal: 400;
    }

    /* Reset & Base */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--color-light);
        color: var(--color-primary);
        line-height: 1.6;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Layout Structure */
    .app-container {
        min-height: 100vh;
        display: flex;
    }

    /* Header */
    .app-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--header-height);
        background: var(--color-white);
        border-bottom: 1px solid rgba(141, 153, 174, 0.1);
        z-index: var(--z-header);
        transition: all var(--transition-base);
    }

    .header-container {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .sidebar-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: transparent;
        border: none;
        color: var(--color-primary);
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .sidebar-toggle:hover {
        background: var(--color-light);
        color: var(--color-accent);
    }

    .brand-logo-mobile {
        display: none;
        height: 40px;
    }

    .brand-logo-mobile img {
        height: 100%;
        width: auto;
    }

    .search-bar {
        position: relative;
        width: 400px;
    }

    .search-input {
        width: 100%;
        height: 42px;
        padding: 0 1rem 0 3rem;
        border: 2px solid transparent;
        background: var(--color-light);
        border-radius: 10px;
        font-size: 0.875rem;
        color: var(--color-primary);
        transition: all var(--transition-fast);
    }

    .search-input:focus {
        outline: none;
        background: var(--color-white);
        border-color: var(--color-primary);
    }

    .search-input::placeholder {
        color: var(--color-gray);
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--color-gray);
        pointer-events: none;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: transparent;
        border: none;
        color: var(--color-primary);
        cursor: pointer;
        position: relative;
        transition: all var(--transition-fast);
    }

    .header-btn:hover {
        background: var(--color-light);
        color: var(--color-accent);
    }

    .notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: var(--color-accent);
        border-radius: 50%;
        border: 2px solid var(--color-white);
    }

    .user-menu {
        position: relative;
    }

    .user-menu-toggle {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        background: transparent;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .user-menu-toggle:hover {
        background: var(--color-light);
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--color-light);
    }

    .user-info {
        text-align: left;
    }

    .user-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--color-primary);
        line-height: 1.2;
    }

    .user-role {
        font-size: 0.75rem;
        color: var(--color-gray);
    }

    /* Sidebar */
    .app-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: var(--sidebar-width);
        background: var(--color-white);
        border-right: 1px solid rgba(141, 153, 174, 0.1);
        z-index: var(--z-sidebar);
        transition: all var(--transition-base);
        overflow: hidden;
    }

    .app-sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }

    .sidebar-header {
        height: var(--header-height);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 1.5rem;
        border-bottom: 1px solid rgba(141, 153, 174, 0.1);
    }

    .brand-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: var(--color-primary);
    }

    .brand-logo img {
        height: 40px;
        width: auto;
    }

    .brand-text {
        font-size: 1.5rem;
        font-weight: 800;
        font-family: 'Plus Jakarta Sans', sans-serif;
        letter-spacing: -1px;
        transition: opacity var(--transition-base);
    }

    .collapsed .brand-text {
        opacity: 0;
        visibility: hidden;
    }

    .sidebar-content {
        height: calc(100% - var(--header-height));
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1.5rem 0;
    }

    /* Custom Scrollbar for Sidebar */
    .sidebar-content::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-content::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-content::-webkit-scrollbar-thumb {
        background: var(--color-gray);
        border-radius: 3px;
        opacity: 0.3;
    }

    .sidebar-content:hover::-webkit-scrollbar-thumb {
        opacity: 0.5;
    }

    .nav-menu {
        list-style: none;
        padding: 0 1rem;
    }

    .nav-item {
        margin-bottom: 0.25rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1rem;
        color: var(--color-primary);
        text-decoration: none;
        border-radius: 12px;
        transition: all var(--transition-fast);
        position: relative;
        overflow: hidden;
    }

    .nav-link:hover {
        background: var(--color-light);
        color: var(--color-accent);
        transform: translateX(4px);
    }

    .nav-link.active {
        background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);
        color: var(--color-white);
        box-shadow: 0 4px 12px rgba(239, 35, 60, 0.3);
    }

    .nav-link.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        transform: translateX(-100%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        to {
            transform: translateX(200%);
        }
    }

    .nav-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        min-width: 24px;
    }

    .nav-text {
        font-size: 0.9375rem;
        font-weight: 500;
        white-space: nowrap;
        transition: opacity var(--transition-base);
    }

    .collapsed .nav-text {
        opacity: 0;
        visibility: hidden;
    }

    .nav-badge {
        margin-left: auto;
        padding: 0.125rem 0.5rem;
        background: var(--color-accent);
        color: var(--color-white);
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 999px;
        transition: opacity var(--transition-base);
    }

    .collapsed .nav-badge {
        opacity: 0;
        visibility: hidden;
    }

    /* Dropdown Menu */
    .nav-item.has-dropdown .nav-link::after {
        content: '\F282';
        font-family: 'bootstrap-icons';
        margin-left: auto;
        transition: transform var(--transition-fast);
    }

    .nav-item.has-dropdown.open .nav-link::after {
        transform: rotate(90deg);
    }

    .dropdown-menu {
        max-height: 0;
        overflow: hidden;
        transition: max-height var(--transition-base);
    }

    .nav-item.has-dropdown.open .dropdown-menu {
        max-height: 300px;
    }

    .dropdown-menu .nav-link {
        padding-left: 3rem;
        font-size: 0.875rem;
    }

    /* Main Content */
    .app-main {
        flex: 1;
        margin-left: var(--sidebar-width);
        margin-top: var(--header-height);
        min-height: calc(100vh - var(--header-height));
        transition: margin-left var(--transition-base);
        background: var(--color-light);
    }

    .sidebar-collapsed .app-main {
        margin-left: var(--sidebar-collapsed-width);
    }

    .main-content {
        padding: 2rem;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 800;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--color-primary);
        margin-bottom: 0.5rem;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--color-gray);
    }

    .breadcrumb a {
        color: var(--color-gray);
        text-decoration: none;
        transition: color var(--transition-fast);
    }

    .breadcrumb a:hover {
        color: var(--color-accent);
    }

    .breadcrumb-separator {
        color: var(--color-gray);
        opacity: 0.5;
    }

    /* Mobile Overlay */
    .mobile-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(11, 9, 10, 0.5);
        z-index: var(--z-mobile-menu);
        opacity: 0;
        transition: opacity var(--transition-base);
    }

    .mobile-overlay.active {
        display: block;
        opacity: 1;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .search-bar {
            width: 300px;
        }
    }

    @media (max-width: 768px) {
        .app-sidebar {
            transform: translateX(-100%);
        }

        .app-sidebar.mobile-open {
            transform: translateX(0);
        }

        .app-main {
            margin-left: 0;
        }

        .sidebar-collapsed .app-main {
            margin-left: 0;
        }

        .search-bar {
            display: none;
        }

        .user-info {
            display: none;
        }

        .brand-logo-mobile {
            display: block;
        }

        .main-content {
            padding: 1rem;
        }
    }

    /* Dropdown Styles */
    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 0.5rem;
        min-width: 200px;
        background: var(--color-white);
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(11, 9, 10, 0.1);
        border: 1px solid rgba(141, 153, 174, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all var(--transition-fast);
        z-index: var(--z-dropdown);
    }

    .dropdown.show .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--color-primary);
        text-decoration: none;
        transition: all var(--transition-fast);
        font-size: 0.875rem;
    }

    .dropdown-item:hover {
        background: var(--color-light);
        color: var(--color-accent);
    }

    .dropdown-item i {
        font-size: 1.125rem;
        opacity: 0.7;
    }

    .dropdown-divider {
        height: 1px;
        background: var(--color-light);
        margin: 0.5rem 0;
    }

    /* Loading States */
    .skeleton {
        background: linear-gradient(90deg, var(--color-light) 25%, rgba(141, 153, 174, 0.1) 50%, var(--color-light) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    /* Tooltips */
    .tooltip {
        position: absolute;
        background: var(--color-primary);
        color: var(--color-white);
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all var(--transition-fast);
        pointer-events: none;
        z-index: var(--z-modal);
    }

    .tooltip::before {
        content: '';
        position: absolute;
        border: 5px solid transparent;
    }

    .tooltip.top {
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        margin-bottom: 0.5rem;
    }

    .tooltip.top::before {
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-top-color: var(--color-primary);
    }

    .collapsed .nav-link:hover .tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    /* Override old styles */
    .header,
    .left-side-bar,
    .main-container {
        all: unset;
    }
</style>