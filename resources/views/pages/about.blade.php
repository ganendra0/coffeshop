@extends('layouts.app')

@section('title', 'About Us - Coffee Blend')

@section('content')

@vite(['resources/css/app.css', 'resources/js/app.js'])


{{-- Hero Section --}}
    @yield('content')

<section class="h-[600px] relative">
    <div class="h-full bg-cover bg-center" style="background-image: url({{ asset('images/bg_3.jpg') }});">
        <div class="absolute inset-0 bg-black opacity-30"></div>
        <div class="container mx-auto h-full px-4">
            <div class="flex h-full items-center justify-center">
                <div class="w-full max-w-3xl text-center text-white">
                    <h1 class="mb-3 mt-5 text-5xl font-bold bread">About Us</h1>
                    <p class="breadcrumbs text-sm uppercase tracking-widest">
                        <span class="mr-2"><a href="{{ route('home') }}">Home</a></span>
                        <span>About</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Story Section --}}
<section class="md:flex">
    <div class="md:w-1/2 bg-cover bg-center min-h-[300px] md:min-h-0" style="background-image: url({{ asset('images/about.jpg') }});"></div>
    <div class="md:w-1/2 p-8 lg:p-16">
        {{-- Overlap on md+ screens --}}
        <div class="bg-black/50 p-10 md:-ml-48">
            <div class="heading-section">
                <span class="font-great-vibes text-5xl text-primary block leading-none -mb-5">Discover</span>
                <h2 class="mb-4 text-4xl font-bold uppercase">Our Story</h2>
            </div>
            <div>
                <p>On her way she met a copy. The copy warned the Little Blind Text, that where it came from it would have been rewritten a thousand times and everything that was left from its origin would be the word "and" and the Little Blind Text should turn around and return to its own, safe country.</p>
            </div>
        </div>
    </div>
</section>

{{-- Testimony Section --}}
<section class="py-28 bg-cover bg-center relative" style="background-image: url({{ asset('images/bg_1.jpg') }});">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="container mx-auto px-4 relative">
        <div class="row justify-content-center mb-5">
            <div class="col-md-7 heading-section text-center">
                <span class="font-great-vibes text-5xl text-primary block leading-none -mb-5">Testimony</span>
                <h2 class="mb-4 text-4xl font-bold uppercase">Customers Says</h2>
                <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
            </div>
        </div>
    </div>
    {{-- Testimony slider content here --}}
</section>

@endsection