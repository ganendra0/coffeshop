<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Menampilkan halaman beranda.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        return view('pages.index');
    }

    /**
     * Menampilkan halaman About Us.
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Menampilkan halaman Menu.
     *
     * @return \Illuminate\View\View
     */
    public function menu()
    {
        return view('pages.menu');
    }

    /**
     * Menampilkan halaman Services.
     *
     * @return \Illuminate\View\View
     */
    public function services()
    {
        return view('pages.services');
    }

    /**
     * Menampilkan halaman Blog.
     *
     * @return \Illuminate\View\View
     */
    public function blog()
    {
        return view('pages.blog');
    }

    /**
     * Menampilkan halaman Contact.
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Menampilkan halaman detail artikel blog.
     *
     * @return \Illuminate\View\View
     */
    public function blogSingle()
    {
        return view('pages.blog-single');
    }

    /**
     * Menampilkan halaman keranjang belanja.
     *
     * @return \Illuminate\View\View
     */
    public function cart()
    {
        return view('pages.cart');
    }

    /**
     * Menampilkan halaman checkout.
     *
     * @return \Illuminate\View\View
     */
    public function checkout()
    {
        return view('pages.checkout');
    }

    /**
     * Menampilkan halaman toko/produk.
     *
     * @return \Illuminate\View\View
     */
    public function shop()
    {
        return view('pages.shop');
    }

    /**
     * Menampilkan halaman detail produk tunggal.
     *
     * @return \Illuminate\View\View
     */
    public function productSingle()
    {
        return view('pages.product-single');
    }
}
