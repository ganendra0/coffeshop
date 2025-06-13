<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    {{-- Judul Halaman Dinamis --}}
    <title>@yield('title', config('app.name', 'Coffee Blend'))</title>

    {{-- Google Fonts - Tetap gunakan jika Anda memuatnya secara eksternal --}}
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">

    {{-- Vite CSS & JS (Mengelola Tailwind CSS dan JS kustom Anda) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Anda bisa menambahkan link CSS lainnya di sini jika ada yang tidak dikelola oleh Vite --}}
    {{-- Contoh: Jika Anda punya CSS pihak ketiga yang tidak perlu diproses Tailwind --}}
</head>
{{-- Menggunakan kelas Tailwind untuk styling dasar body --}}
<body class="bg-darken font-poppins text-gray-400">

    {{-- Navigasi Utama --}}
    {{-- Konversi kelas Bootstrap ke Tailwind. Ini hanya contoh, Anda perlu menyesuaikan --}}
    <nav class="relative z-50 py-4" id="main-navbar"> {{-- Contoh: navbar-dark bg-dark ftco-navbar-light jadi Tailwind --}}
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a class="text-white text-2xl font-bold" href="{{ route('home') }}">Coffee<small class="text-sm font-normal">Blend</small></a>
            
            {{-- Tombol Toggler untuk Mobile (perlu JS kustom atau Alpine.js) --}}
            <button class="lg:hidden text-white focus:outline-none" type="button" aria-controls="mobile-nav" aria-expanded="false" aria-label="Toggle navigation">
                {{-- Anda perlu ikon di sini, misalnya dari Font Awesome --}}
                <i class="fas fa-bars text-xl"></i> Menu
            </button>
            
            <div class="hidden lg:flex flex-grow justify-end" id="main-nav-content"> {{-- Contoh: collapse navbar-collapse jadi Tailwind --}}
                <ul class="flex space-x-6"> {{-- Contoh: navbar-nav ml-auto jadi Tailwind --}}
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link text-white hover:text-primary transition duration-300 {{ request()->routeIs('home') ? 'font-bold text-primary' : '' }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('menu') }}" class="nav-link text-white hover:text-primary transition duration-300 {{ request()->routeIs('menu') ? 'font-bold text-primary' : '' }}">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('services') }}" class="nav-link text-white hover:text-primary transition duration-300 {{ request()->routeIs('services') ? 'font-bold text-primary' : '' }}">Services</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('blog') }}" class="nav-link text-white hover:text-primary transition duration-300 {{ request()->routeIs('blog') ? 'font-bold text-primary' : '' }}">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('about') }}" class="nav-link text-white hover:text-primary transition duration-300 {{ request()->routeIs('about') ? 'font-bold text-primary' : '' }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('contact') }}" class="nav-link text-white hover:text-primary transition duration-300 {{ request()->routeIs('contact') ? 'font-bold text-primary' : '' }}">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white hover:text-primary transition duration-300 flex items-center space-x-1">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="bg-primary text-black text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center -mt-2 ml-1">1</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Mobile Nav (Contoh dengan Alpine.js untuk toggle) --}}
            {{-- <div x-data="{ open: false }" class="lg:hidden">
                <button @click="open = !open" class="text-white focus:outline-none">
                    <i class="fas fa-bars text-xl"></i> Menu
                </button>
                <div x-show="open" @click.away="open = false" class="absolute top-16 right-4 bg-darken p-4 rounded shadow-lg">
                    <ul class="flex flex-col space-y-2">
                        <li><a href="{{ route('home') }}" class="block text-white hover:text-primary">Home</a></li>
                        <li><a href="{{ route('menu') }}" class="block text-white hover:text-primary">Menu</a></li>
                        <li><a href="{{ route('services') }}" class="block text-white hover:text-primary">Services</a></li>
                        <li><a href="{{ route('blog') }}" class="block text-white hover:text-primary">Blog</a></li>
                        <li><a href="{{ route('about') }}" class="block text-white hover:text-primary">About</a></li>
                        <li><a href="{{ route('contact') }}" class="block text-white hover:text-primary">Contact</a></li>
                        <li><a href="#" class="block text-white hover:text-primary flex items-center space-x-1"><i class="fas fa-shopping-cart"></i> Cart <span class="bg-primary text-black text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center">1</span></a></li>
                    </ul>
                </div>
            </div> --}}
            
        </div>
    </nav>
    {{-- Konten Utama Halaman --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    {{-- Anda perlu mengkonversi kelas ftco-footer, ftco-section, dll. ke Tailwind --}}
    <footer class="bg-gray-900 py-16 relative"> {{-- Contoh: ftco-footer ftco-section img --}}
        <div class="absolute inset-0 bg-black opacity-70"></div> {{-- Contoh: overlay --}}
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12"> {{-- Contoh: row mb-5 --}}
                <div class="col-span-1"> {{-- Contoh: col-lg-3 col-md-6 mb-5 mb-md-5 --}}
                    <div class="mb-4"> {{-- ftco-footer-widget --}}
                        <h2 class="text-white text-2xl font-bold mb-4">About Us</h2> {{-- ftco-heading-2 --}}
                        <p class="text-gray-400">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
                        <ul class="flex space-x-4 mt-5"> {{-- ftco-footer-social list-unstyled float-md-left float-lft --}}
                            <li><a href="#" class="text-gray-400 hover:text-white transition duration-300"><i class="fab fa-twitter"></i></a></li> {{-- ftco-animate icon-twitter --}}
                            <li><a href="#" class="text-gray-400 hover:text-white transition duration-300"><i class="fab fa-facebook-f"></i></a></li> {{-- icon-facebook --}}
                            <li><a href="#" class="text-gray-400 hover:text-white transition duration-300"><i class="fab fa-instagram"></i></a></li> {{-- icon-instagram --}}
                        </ul>
                    </div>
                </div>
                <div class="col-span-1 lg:col-span-2"> {{-- col-lg-4 col-md-6 mb-5 mb-md-5 --}}
                    <div class="mb-4"> {{-- ftco-footer-widget --}}
                        <h2 class="text-white text-2xl font-bold mb-4">Recent Blog</h2> {{-- ftco-heading-2 --}}
                        <div class="flex mb-4 items-center"> {{-- block-21 mb-4 d-flex --}}
                            <a class="w-16 h-16 mr-4 bg-cover bg-center rounded-sm" style="background-image: url({{ asset('images/image_1.jpg') }});"></a>
                            <div class="flex-1"> {{-- text --}}
                                <h3 class="text-white text-lg font-semibold mb-1"><a href="#" class="hover:text-primary transition duration-300">Even the all-powerful Pointing has no control about</a></h3>
                                <div class="text-gray-500 text-sm flex items-center space-x-3"> {{-- meta --}}
                                    <div><i class="far fa-calendar-alt"></i> Sept 15, 2018</div> {{-- icon-calendar --}}
                                    <div><i class="fas fa-user"></i> Admin</div> {{-- icon-person --}}
                                    <div><i class="far fa-comment"></i> 19</div> {{-- icon-chat --}}
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-4 items-center">
                            <a class="w-16 h-16 mr-4 bg-cover bg-center rounded-sm" style="background-image: url({{ asset('images/image_2.jpg') }});"></a>
                            <div class="flex-1">
                                <h3 class="text-white text-lg font-semibold mb-1"><a href="#" class="hover:text-primary transition duration-300">Even the all-powerful Pointing has no control about</a></h3>
                                <div class="text-gray-500 text-sm flex items-center space-x-3">
                                    <div><i class="far fa-calendar-alt"></i> Sept 15, 2018</div>
                                    <div><i class="fas fa-user"></i> Admin</div>
                                    <div><i class="far fa-comment"></i> 19</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-1"> {{-- col-lg-2 col-md-6 mb-5 mb-md-5 --}}
                    <div class="mb-4 lg:ml-8"> {{-- ftco-footer-widget ml-md-4 --}}
                        <h2 class="text-white text-2xl font-bold mb-4">Services</h2>
                        <ul class="list-none space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white block py-1 transition duration-300">Cooked</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white block py-1 transition duration-300">Deliver</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white block py-1 transition duration-300">Quality Foods</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white block py-1 transition duration-300">Mixed</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-span-1"> {{-- col-lg-3 col-md-6 mb-5 mb-md-5 --}}
                    <div class="mb-4"> {{-- ftco-footer-widget --}}
                        <h2 class="text-white text-2xl font-bold mb-4">Have a Questions?</h2>
                        <div class="text-gray-400 space-y-2"> {{-- block-23 mb-3 --}}
                            <div class="flex items-start">
                                <span class="text-primary mr-3 mt-1"><i class="fas fa-map-marker-alt"></i></span><span class="text-gray-400">203 Fake St. Mountain View, San Francisco, California, USA</span>
                            </div>
                            <div class="flex items-start">
                                <a href="#" class="flex items-center text-gray-400 hover:text-white transition duration-300">
                                    <span class="text-primary mr-3"><i class="fas fa-phone"></i></span><span class="text-gray-400">+2 392 3929 210</span>
                                </a>
                            </div>
                            <div class="flex items-start">
                                <a href="#" class="flex items-center text-gray-400 hover:text-white transition duration-300">
                                    <span class="text-primary mr-3"><i class="fas fa-envelope"></i></span><span class="text-gray-400">info@yourdomain.com</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center text-gray-500 text-sm"> {{-- col-md-12 text-center --}}
                <p>
                    Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fas fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank" class="text-primary hover:text-white">Colorlib</a>
                </p>
            </div>
        </div>
    </footer>

    {{-- Loader --}}
    {{-- Ini adalah animasi loader, Anda mungkin ingin mereplikasi ini dengan Tailwind/JS kustom --}}
    {{-- Untuk sementara bisa di-comment atau dihapus jika tidak kritis --}}
    {{-- <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div> --}}

    {{-- Script JS yang dikelola oleh Vite --}}
    {{-- File JS lama dihapus --}}
    {{-- @yield('scripts') tetap relevan untuk script spesifik halaman --}}
    @yield('scripts')

</body>
</html>