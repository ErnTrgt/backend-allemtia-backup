{{-- Seller Sidebar with Active States --}}
<div class="left-side-bar">
    <div class="brand-logo">
        <a href="{{ route('seller.dashboard') }}">
            <img src="{{ asset('admin/vendors/images/logo.png') }}" alt="Logo">
        </a>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <!-- Dashboard Link -->
                <li class="{{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('seller.dashboard') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-house"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('seller.products*') ? 'active' : '' }}">
                    <a href="{{ route('seller.products') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-box-seam"></span>
                        <span class="mtext">Products</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('seller.orders*') ? 'active' : '' }}">
                    <a href="{{ route('seller.orders') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-cart"></span>
                        <span class="mtext">Orders</span>
                    </a>
                </li>
                  <li class="{{ request()->routeIs('seller.cart-items') ? 'active' : '' }}">
                    <a href="{{ route('seller.cart-items') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-cart-plus"></span>
                        <span class="mtext">Cart Items</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('seller.wishlist-items') ? 'active' : '' }}">
                    <a href="{{ route('seller.wishlist-items') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-heart"></span>
                        <span class="mtext">Wishlist Items</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('seller.coupons.*') ? 'active' : '' }}">
                    <a href="{{ route('seller.coupons.index') }}" class="dropdown-toggle no-arrow">
                        <span class="micon bi bi-tag"></span>
                        <span class="mtext">Coupons</span>
                    </a>
                </li>
                <!-- Account Settings -->
                <li class="dropdown {{ request()->routeIs('seller.profile') || request()->routeIs('seller.password.change') ? 'active show' : '' }}">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon bi bi-gear"></span>
                        <span class="mtext">Account Settings</span>
                    </a>
                    <ul class="submenu" style="{{ request()->routeIs('seller.profile') || request()->routeIs('seller.password.change') ? 'display: block;' : '' }}">
                        <li class="{{ request()->routeIs('seller.profile') ? 'active' : '' }}">
                            <a href="{{ route('seller.profile') }}">Profile</a>
                        </li>
                        <li class="{{ request()->routeIs('seller.password.change') ? 'active' : '' }}">
                            <a href="{{ route('seller.password.change') }}">Change Password</a>
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

/* Icon animation on hover - reduced movement */
.left-side-bar .sidebar-menu ul li > a:hover .micon {
    transform: translateX(2px);
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