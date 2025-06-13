{{-- resources/views/pages/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Coffee Blend - Home')

@section('content')

{{-- Konversi dari home-slider & slider-item --}}
<section class="relative h-[750px]">
    {{-- Slider Item 1 --}}
    <div class="h-full bg-cover bg-center" style="background-image: url({{ asset('images/bg_1.jpg') }});">
        <div class="absolute inset-0 bg-black opacity-30"></div>
        <div class="container mx-auto h-full px-4">
            <div class="flex h-full items-center justify-center">
                <div class="w-full max-w-3xl text-center text-white">
                    <span class="font-great-vibes text-3xl text-primary">Welcome</span>
                    <h1 class="mb-4 text-4xl uppercase tracking-wider font-josefin">The Best Coffee Testing Experience</h1> {{-- Menambahkan font-josefin --}}
                    <p class="mb-4 md:mb-5 text-lg font-light text-white leading-relaxed">A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
                    <p class="space-x-4"> {{-- Menambahkan space-x untuk jarak antar tombol --}}
                        {{-- Mengganti btn btn-primary --}}
                        <a href="#" class="bg-primary text-white py-3 px-6 rounded-full hover:bg-opacity-90 transition duration-300 text-lg font-semibold">Order Now</a>
                        {{-- Mengganti btn btn-white btn-outline-white --}}
                        <a href="#" class="border border-white text-white py-3 px-6 rounded-full hover:bg-white hover:text-black transition duration-300 text-lg font-semibold">View Menu</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    {{-- Anda bisa menambahkan item slider lainnya di sini menggunakan library JS seperti Swiper.js atau Tiny-slider --}}
</section>

{{-- Konversi dari ftco-intro --}}
<section class="-mt-[130px] relative z-20">
    <div class="container mx-auto px-4">
        <div class="md:flex items-end shadow-lg rounded-lg overflow-hidden"> {{-- Menambahkan shadow dan rounded untuk kontainer --}}
            <div class="bg-black p-8 md:w-2/3 text-gray-400"> {{-- Menambahkan warna teks --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-start space-x-4"> {{-- Menambahkan space-x --}}
                        <div class="text-primary text-3xl"><i class="fas fa-phone"></i></div> {{-- Mengganti icon-phone dengan Font Awesome --}}
                        <div>
                            <h3 class="text-white text-lg font-semibold">000 (123) 456 7890</h3>
                            <p class="text-sm">A small river named Duden flows by their place and supplies.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="text-primary text-3xl"><i class="fas fa-map-marker-alt"></i></div> {{-- Mengganti icon-my_location dengan Font Awesome --}}
                        <div>
                            <h3 class="text-white text-lg font-semibold">198 West 21th Street</h3>
                            <p class="text-sm">203 Fake St. Mountain View, San Francisco, California, USA</p>
                        </div>
                    </div>
                     <div class="flex items-start space-x-4">
                        <div class="text-primary text-3xl"><i class="far fa-clock"></i></div> {{-- Mengganti icon-clock-o dengan Font Awesome --}}
                        <div>
                            <h3 class="text-white text-lg font-semibold">Open Monday-Friday</h3>
                            <p class="text-sm">8:00am - 9:00pm</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-primary p-4 md:w-1/3 flex items-center justify-center py-8"> {{-- Menambahkan flex dan py untuk mengisi ruang --}}
                <h3 class="text-black uppercase text-2xl font-bold">Book a Table</h3>
                {{-- Form Booking Table (Anda perlu mengkonversinya ke Tailwind juga jika ada) --}}
            </div>
        </div>
    </div>
</section>


{{-- Dan seterusnya untuk section lainnya... --}}
{{-- Anda perlu mengkonversi section-section lain di index.blade.php dengan pola yang sama --}}
{{-- Misalnya untuk "Our Story", "Discover Menu", "Testimony", dll. --}}

@endsection