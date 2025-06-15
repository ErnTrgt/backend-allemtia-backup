<div class="brand-logo">
    <a href="#">
        <img src="{{ asset('admin/vendors/images/logo.png') }}" alt="Logo">
    </a>
</div>
<div class="menu-block customscroll">
    <div class="sidebar-menu">
        <ul id="accordion-menu">
            <li>
                <a href="{{ url('/admin/dashboard') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-house"></span>
                    <span class="mtext">Dashboard</span>
                </a>
            </li>
            <li>

                <a href="{{ url('/admin/users') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-house"></span>
                    <span class="mtext">Users</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/products') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-house"></span>
                    <span class="mtext">Products</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/stores') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-house"></span>
                    <span class="mtext">Stores</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/about') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-house"></span>
                    <span class="mtext">Abouts</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/faqs') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-house"></span>
                    <span class="mtext">Faq</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/sliders') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-house"></span>
                    <span class="mtext">Slider</span>
                </a>
            </li>
             <li>
                <a href="{{ url('/admin/coupons') }}" class="dropdown-toggle no-arrow">
                    <span class="micon bi bi-house"></span>
                    <span class="mtext">Coupon</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:;" class="dropdown-toggle">
                    <span class="micon bi bi-gear"></span>
                    <span class="mtext">Settings</span>
                </a>
                <ul class="submenu">
                    <li><a href="{{ url('/admin/profile') }}">Profile</a></li>
                    <li><a href="#">Account</a></li>
                </ul>
                
            </li>
        </ul>
    </div>
</div>
