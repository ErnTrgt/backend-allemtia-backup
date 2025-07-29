<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const appMain = document.querySelector('.app-main');
        
        // Check if sidebar state is saved
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed && window.innerWidth > 768) {
            sidebar.classList.add('collapsed');
            appMain.classList.add('sidebar-collapsed');
        }
        
        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                mobileOverlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                appMain.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        });
        
        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
        });
        
        // Dropdown Menus
        const dropdowns = document.querySelectorAll('.dropdown');
        
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('[data-toggle="dropdown"]');
            
            toggle?.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Close other dropdowns
                dropdowns.forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('show');
                    }
                });
                
                dropdown.classList.toggle('show');
            });
        });
        
        // Close dropdowns on outside click
        document.addEventListener('click', function() {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        });
        
        // Sidebar Dropdown Menus
        const navDropdowns = document.querySelectorAll('.nav-item.has-dropdown');
        
        navDropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('.nav-link');
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('open');
            });
        });
        
        // Add tooltips for collapsed sidebar
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const text = link.querySelector('.nav-text')?.textContent;
            if (text) {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip top';
                tooltip.textContent = text;
                link.appendChild(tooltip);
            }
        });
        
        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    // Implement search functionality
                    console.log('Search:', this.value);
                }
            });
        }
        
        // Active menu item scroll into view
        const activeMenuItem = document.querySelector('.nav-link.active');
        if (activeMenuItem) {
            activeMenuItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Notification handling
        const notificationBtn = document.getElementById('notificationBtn');
        if (notificationBtn) {
            notificationBtn.addEventListener('click', function() {
                // Implement notification dropdown
                console.log('Show notifications');
            });
        }
        
        // Message handling
        const messageBtn = document.getElementById('messageBtn');
        if (messageBtn) {
            messageBtn.addEventListener('click', function() {
                // Implement message dropdown
                console.log('Show messages');
            });
        }
    });
</script>

<!-- SweetAlert2 Integration -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            });
        @endif
    });
</script>