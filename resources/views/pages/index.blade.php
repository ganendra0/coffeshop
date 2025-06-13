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
                    <h1 class="mb-4 text-4xl uppercase tracking-wider">The Best Coffee Testing Experience</h1>
                    <p class="mb-4 md:mb-5 text-lg font-light text-white">A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
                    <p>
                        <a href="#" class="btn btn-primary p-3 px-4">Order Now</a> 
                        <a href="#" class="btn btn-white btn-outline-white p-3 px-4">View Menu</a>
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
        <div class="md:flex items-end">
            <div class="bg-black p-8 md:w-2/3">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="flex items-start">
                        <div class="text-primary text-2xl mr-4"><span class="icon-phone"></span></div>
                        <div>
                            <h3 class="text-white text-base">000 (123) 456 7890</h3>
                            <p>A small river named Duden flows by their place and supplies.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="text-primary text-2xl mr-4"><span class="icon-my_location"></span></div>
                        <div>
                            <h3 class="text-white text-base">198 West 21th Street</h3>
                            <p>203 Fake St. Mountain View, San Francisco, California, USA</p>
                        </div>
                    </div>
                     <div class="flex items-start">
                        <div class="text-primary text-2xl mr-4"><span class="icon-clock-o"></span></div>
                        <div>
                            <h3 class="text-white text-base">Open Monday-Friday</h3>
                            <p>8:00am - 9:00pm</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-primary p-4 md:w-1/3">
                <h3 class="text-black uppercase">Book a Table</h3>
                {{-- Form Booking Table --}}
            </div>
        </div>
    </div>
</section>


{{-- Dan seterusnya untuk section lainnya... --}}

@endsection