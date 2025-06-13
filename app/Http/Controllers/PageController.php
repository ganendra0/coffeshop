<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.index');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function menu()
    {
        // Buat file menu.blade.php
        return view('pages.menu');
    }
    
    // Tambahkan method lain untuk services, blog, contact, dll.
}