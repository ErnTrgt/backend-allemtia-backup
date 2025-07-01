{{-- Admin Sidebar with Active States --}}
<div class="left-side-bar">
    <div class="brand-logo">
        <a href="#">
            <img src="{{ asset('admin/vendors/images/logo.png') }}" alt="Logo">
        </a>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                    <a href="{{ url('/admin/dashboard') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-speedometer2"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/users') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-people"></span>
                        <span class="mtext">Users</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/products*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/products') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-box-seam"></span>
                        <span class="mtext">Products</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/stores*') || Request::is('admin/store/*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/stores') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-shop"></span>
                        <span class="mtext">Stores</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/orders*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/orders') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-cart-check"></span>
                        <span class="mtext">Orders</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/about*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/about') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-info-circle"></span>
                        <span class="mtext">Abouts</span>
                    </a>
                </li>
                  <li class="{{ Request::is('admin/blogs*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/blogs') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-info-circle"></span>
                        <span class="mtext">Blog</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/faqs*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/faqs') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-question-circle"></span>
                        <span class="mtext">Faq</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/sliders*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/sliders') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-images"></span>
                        <span class="mtext">Slider</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/coupons*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/coupons') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-tag"></span>
                        <span class="mtext">Coupon</span>
                    </a>
                </li>
                <li class="dropdown {{ Request::is('admin/profile*') || Request::is('admin/account*') ? 'active show' : '' }}">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon bi bi-gear"></span>
                        <span class="mtext">Settings</span>
                    </a>
                    <ul class="submenu" style="{{ Request::is('admin/profile*') || Request::is('admin/account*') ? 'display: block;' : '' }}">
                        <li class="{{ Request::is('admin/profile*') ? 'active' : '' }}">
                            <a href="{{ url('/admin/profile') }}">Profile</a>
                        </li>
                        <li class="{{ Request::is('admin/account*') ? 'active' : '' }}">
                            <a href="#">Account</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
/* Sidebar Active and Hover States */
.left-side-bar .sidebar-menu ul li.active > a {
    background: linear-gradient(45deg, #1976d2, #2196f3);
    color: #fff !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

.left-side-bar .sidebar-menu ul li.active > a .micon,
.left-side-bar .sidebar-menu ul li.active > a .mtext {
    color: #fff !important;
}

/* Hover effect for non-active items */
.left-side-bar .sidebar-menu ul li:not(.active) > a:hover {
    background: rgba(25, 118, 210, 0.1);
    color: #1976d2 !important;
    transition: all 0.3s ease;
    padding-left: 22px;
}

.left-side-bar .sidebar-menu ul li:not(.active) > a:hover .micon,
.left-side-bar .sidebar-menu ul li:not(.active) > a:hover .mtext {
    color: #1976d2 !important;
}

/* Submenu active state */
.left-side-bar .sidebar-menu ul li.dropdown.active.show > a {
    background: linear-gradient(45deg, #1976d2, #2196f3);
    color: #fff !important;
}

.left-side-bar .sidebar-menu ul li ul.submenu li.active > a {
    background: rgba(25, 118, 210, 0.15);
    color: #1976d2 !important;
    font-weight: 600;
    padding-left: 60px;
}

.left-side-bar .sidebar-menu ul li ul.submenu li:not(.active) > a:hover {
    background: rgba(25, 118, 210, 0.08);
    color: #1976d2 !important;
    padding-left: 65px;
    transition: all 0.3s ease;
}

/* Active indicator line */
.left-side-bar .sidebar-menu ul li.active > a::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: #fff;
    border-radius: 0 4px 4px 0;
}

/* Icon animation on hover */
.left-side-bar .sidebar-menu ul li > a:hover .micon {
    transform: translateX(70px);
    transition: transform 0.3s ease;
}

/* Smooth transitions */
.left-side-bar .sidebar-menu ul li > a {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

/* Icon container to prevent overflow */
.left-side-bar .sidebar-menu ul li > a .micon {
    display: inline-block;
    transition: transform 0.3s ease;
}

/* Alternative Active State Style (Optional - Comment out above styles and use this for a different look) */
/*
.left-side-bar .sidebar-menu ul li.active > a {
    background: #f7f7f7;
    color: #1976d2 !important;
    border-left: 4px solid #1976d2;
    font-weight: 600;
}

.left-side-bar .sidebar-menu ul li.active > a .micon,
.left-side-bar .sidebar-menu ul li.active > a .mtext {
    color: #1976d2 !important;
}
*/
</style>

<script>
// Optional: Add smooth scrolling to active menu item on page load
document.addEventListener('DOMContentLoaded', function() {
    const activeMenuItem = document.querySelector('.sidebar-menu li.active');
    if (activeMenuItem) {
        activeMenuItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Optional: Add click animation
document.querySelectorAll('.sidebar-menu ul li > a').forEach(link => {
    link.addEventListener('click', function(e) {
        if (!this.classList.contains('dropdown-toggle')) {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        }
    });
});
</script>

{{-- Additional CSS for ripple effect (optional) --}}
<style>
.sidebar-menu ul li > a {
    position: relative;
    overflow: hidden;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
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
</style>