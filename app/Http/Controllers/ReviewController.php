<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['order.user'])->latest('created_at'); // Eager load order dan user

        if ($request->has('order_id') && $request->order_id != '') {
            $query->where('order_id', $request->order_id);
        }
        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(15);
        $orders = Order::orderBy('order_id', 'desc')->get(); // Untuk filter

        return view('reviews.index', compact('reviews', 'orders'));
    }

    /**
     * Show the form for creating a new resource.
     * Review biasanya dibuat dari sisi pengguna setelah order selesai.
     * Untuk admin, ini mungkin tidak terlalu umum, tapi kita buatkan.
     */
    public function create(Request $request)
    {
        // Hanya order yang sudah selesai dan belum ada review
        $orders = Order::where('status', Order::STATUS_COMPLETED)
                        ->whereDoesntHave('review')
                        ->orderBy('order_id', 'desc')->get();
        $selectedOrderId = $request->input('order_id');

        return view('reviews.create', compact('orders', 'selectedOrderId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id|unique:reviews,order_id', // Satu review per order
            'rating' => 'required|integer|min:1|max:5', // Asumsi rating 1-5
            'comment' => 'nullable|string|max:1000',
        ], [
            'order_id.unique' => 'Order ini sudah memiliki review.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('reviews.create', ['order_id' => $request->order_id])
                        ->withErrors($validator)
                        ->withInput();
        }

        Review::create($request->all());

        return redirect()->route('reviews.index')->with('success', 'Review berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review) // Route model binding
    {
        $review->load(['order.user']);
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $review->load('order');
        // Order ID biasanya tidak diubah
        $orders = Order::where('order_id', $review->order_id)->get();

        return view('reviews.edit', compact('review', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validator = Validator::make($request->all(), [
            // order_id tidak boleh diubah
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->route('reviews.edit', $review->review_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $review->update($request->only(['rating', 'comment']));

        return redirect()->route('reviews.index')->with('success', 'Review berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Review berhasil dihapus.');
    }
}