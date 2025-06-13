<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Coffee<small>Blend</small></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="oi oi-menu"></span> Menu
        </button>
        <div class="collapse navbar-collapse" id="ftco-nav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                    <a href="{{ url('/') }}" class="nav-link">Home</a>
                </li>
                <li class="nav-item {{ Request::is('menu') ? 'active' : '' }}">
                    <a href="{{ url('/menu') }}" class="nav-link">Menu</a>
                </li>
                <li class="nav-item {{ Request::is('services') ? 'active' : '' }}">
                    <a href="{{ url('/services') }}" class="nav-link">Services</a>
                </li>
                <li class="nav-item {{ Request::is('blog') || Request::is('blog-single') ? 'active' : '' }}">
                    <a href="{{ url('/blog') }}" class="nav-link">Blog</a>
                </li>
                <li class="nav-item {{ Request::is('about') ? 'active' : '' }}">
                    <a href="{{ url('/about') }}" class="nav-link">About</a>
                </li>
                <li class="nav-item dropdown {{ Request::is('shop') || Request::is('product-single') || Request::is('cart') || Request::is('checkout') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Shop</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown04">
                        <a class="dropdown-item" href="{{ url('/shop') }}">Shop</a>
                        <a class="dropdown-item" href="{{ url('/product-single') }}">Single Product</a>
                        <a class="dropdown-item" href="{{ url('/cart') }}">Cart</a>
                        <a class="dropdown-item" href="{{ url('/checkout') }}">Checkout</a>
                    </div>
                </li>
                <li class="nav-item {{ Request::is('contact') ? 'active' : '' }}">
                    <a href="{{ url('/contact') }}" class="nav-link">Contact</a>
                </li>
                <li class="nav-item cart">
                    <a href="{{ url('/cart') }}" class="nav-link">
                        <span class="icon icon-shopping_cart"></span>
                        <span class="bag d-flex justify-content-center align-items-center"><small>1</small></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
