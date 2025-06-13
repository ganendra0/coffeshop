{{-- resources/views/pages/about.blade.php --}}
@extends('layouts.app')

@section('title', 'About Us - Coffee Blend')

@section('content')

{{-- Hero Section --}}
{{-- Kelas 'bread' dan 'breadcrumbs' diasumsikan dikustomisasi di resources/css/app.css --}}
<section class="h-[600px] relative">
    <div class="h-full bg-cover bg-center" style="background-image: url({{ asset('images/bg_3.jpg') }});">
        <div class="absolute inset-0 bg-black opacity-30"></div>
        <div class="container mx-auto h-full px-4">
            <div class="flex h-full items-center justify-center">
                <div class="w-full max-w-3xl text-center text-white">
                    {{-- Menggunakan font-josefin yang sudah di-extend di tailwind.config.js --}}
                    <h1 class="mb-3 mt-5 text-5xl font-bold bread font-josefin">About Us</h1>
                    <p class="breadcrumbs text-sm uppercase tracking-widest font-josefin">
                        {{-- Menggunakan warna primary yang di-extend di tailwind.config.js --}}
                        <span class="mr-2"><a href="{{ route('home') }}" class="text-primary hover:text-white transition-colors duration-300">Home</a></span>
                        <span>About</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Story Section --}}
<section class="md:flex bg-darken"> {{-- Menambahkan bg-darken sesuai tema --}}
    <div class="md:w-1/2 bg-cover bg-center min-h-[300px] md:min-h-0" style="background-image: url({{ asset('images/about.jpg') }});"></div>
    <div class="md:w-1/2 p-8 lg:p-16 flex items-center justify-center"> {{-- Menambahkan flex items-center justify-center untuk konten tengah --}}
        {{-- Overlap on md+ screens --}}
        <div class="bg-black/50 p-10 md:-ml-48 text-white max-w-xl md:max-w-none w-full"> {{-- Menyesuaikan lebar dan warna teks --}}
            <div class="heading-section mb-6"> {{-- Menambahkan margin-bottom --}}
                <span class="font-great-vibes text-5xl text-primary block leading-none -mb-5">Discover</span>
                <h2 class="mb-4 text-4xl font-bold uppercase font-josefin">Our Story</h2> {{-- Menambahkan font-josefin --}}
            </div>
            <div>
                <p class="text-gray-300 leading-relaxed">On her way she met a copy. The copy warned the Little Blind Text, that where it came from it would have been rewritten a thousand times and everything that was left from its origin would be the word "and" and the Little Blind Text should turn around and return to its own, safe country.</p>
            </div>
        </div>
    </div>
</section>

{{-- Testimony Section --}}
<section class="py-28 bg-cover bg-center relative" style="background-image: url({{ asset('images/bg_1.jpg') }});">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="container mx-auto px-4 relative z-10"> {{-- Menambahkan z-10 agar konten di atas overlay --}}
        <div class="max-w-2xl mx-auto text-center mb-16"> {{-- Mengganti row justify-content-center col-md-7 --}}
            <span class="font-great-vibes text-5xl text-primary block leading-none -mb-5">Testimony</span>
            <h2 class="mb-4 text-4xl font-bold uppercase text-white font-josefin">Customers Says</h2> {{-- Menambahkan text-white dan font-josefin --}}
            <p class="text-gray-300">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
        </div>
    </div>
    {{-- Testimony slider content here (Anda perlu mengimplementasikannya dengan JS seperti Swiper.js atau Alpine.js) --}}
</section>

@endsection