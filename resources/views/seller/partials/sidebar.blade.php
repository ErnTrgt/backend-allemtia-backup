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
            <li class="{{ request()->routeIs('seller.products') ? 'active' : '' }}">
                <a href="{{ route('seller.products') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-box-seam"></span>
                    <span class="mtext">Products</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('seller.orders') ? 'active' : '' }}">
                <a href="{{ route('seller.orders') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-cart"></span>
                    <span class="mtext">Orders</span>
                </a>
            </li>

            <!-- Account Settings -->
            <li class="dropdown">
                <a href="javascript:;" class="dropdown-toggle">
                    <span class="micon bi bi-gear"></span>
                    <span class="mtext">Account Settings</span>
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('seller.profile') }}">Profile</a></li>
                    <li><a href="{{ route('seller.password.change') }}">Change Password</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
