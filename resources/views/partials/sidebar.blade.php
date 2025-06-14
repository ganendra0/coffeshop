<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Menu Utama</span>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="fas fa-users fa-fw me-2"></i>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('menus.*') ? 'active' : '' }}" href="{{ route('menus.index') }}">
                     <i class="fas fa-utensils fa-fw me-2"></i> {{-- Atau ikon lain seperti fa-list, fa-book-open --}}
                    Menus
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="fas fa-shopping-cart fa-fw me-2"></i> {{-- Atau ikon lain seperti fa-file-invoice-dollar --}}
                    Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('order_items.*') ? 'active' : '' }}" href="{{ route('order_items.index') }}">
                    <i class="fas fa-receipt fa-fw me-2"></i>
                     Order Items
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                    <i class="fas fa-money-check-alt fa-fw me-2"></i>
                    Payments
                </a>
            </li>
            <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('reviews.*') ? 'active' : '' }}" href="{{ route('reviews.index') }}">
        <i class="fas fa-star fa-fw me-2"></i>
        Reviews
    </a>
</li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Lainnya</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-cog fa-fw me-2"></i>
                    Pengaturan
                </a>
            </li>
            <li class="nav-item">
                {{-- Contoh Logout Form (Jika menggunakan default auth Laravel) --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt fa-fw me-2"></i>
                        Logout
                    </a>
                </form>
            </li>
        </ul>
    </div>
</nav>