<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // DIPERBAIKI: Eager load relasi yang benar (user, menu)
        $query = Review::with(['user', 'menu', 'order'])->latest('created_at');

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // BARU: Kita butuh daftar User, Menu, dan Order untuk form
        $users = User::orderBy('name')->get();
        $menus = Menu::orderBy('name')->get();
        $orders = Order::orderBy('order_id', 'desc')->get();

        // DIPERBAIKI: Mengarahkan ke view admin dan mengirim semua data yang dibutuhkan
        return view('admin.reviews.create', compact('users', 'menus', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // DIPERBAIKI: Validasi disesuaikan dengan skema baru
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
            'user_id' => 'required|exists:users,user_id',
            'menu_id' => 'required|exists:menus,menu_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_anonymous' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            // DIPERBAIKI: Mengarahkan ke route admin
            return redirect()->route('admin.reviews.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->all();
        // Pastikan is_anonymous punya nilai default jika tidak dicentang
        $data['is_anonymous'] = $request->has('is_anonymous') ? 1 : 0;

        Review::create($data);

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review) // Route model binding sudah benar
    {
        // DIPERBAIKI: Eager load relasi yang relevan
        $review->load(['order', 'user', 'menu']);
        // DIPERBAIKI: Mengarahkan ke view admin
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review) // Route model binding sudah benar
    {
        $review->load(['order', 'user', 'menu']);
        
        // BARU: Kirim semua data yang mungkin diubah
        $users = User::orderBy('name')->get();
        $menus = Menu::orderBy('name')->get();
        $orders = Order::orderBy('order_id', 'desc')->get();

        // DIPERBAIKI: Mengarahkan ke view admin
        return view('admin.reviews.edit', compact('review', 'users', 'menus', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review) // Route model binding sudah benar
    {
        // DIPERBAIKI: Validasi disesuaikan dengan skema baru
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
            'user_id' => 'required|exists:users,user_id',
            'menu_id' => 'required|exists:menus,menu_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_anonymous' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            // DIPERBAIKI: Mengarahkan ke route admin
            return redirect()->route('admin.reviews.edit', $review->review_id)
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $data = $request->all();
        $data['is_anonymous'] = $request->has('is_anonymous') ? 1 : 0;

        $review->update($data);

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review) // Route model binding sudah benar
    {
        $review->delete();
        // Route sudah benar
        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil dihapus.');
    }
}