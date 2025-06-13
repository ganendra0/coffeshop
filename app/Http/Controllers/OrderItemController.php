<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = OrderItem::with(['order', 'menu'])->latest();

        // Filter berdasarkan Order ID jika ada parameter di URL
        if ($request->has('order_id') && $request->order_id != '') {
            $query->where('order_id', $request->order_id);
        }

        $orderItems = $query->paginate(15);
        $orders = Order::orderBy('order_id', 'desc')->get(); // Untuk filter dropdown

        return view('order_items.index', compact('orderItems', 'orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $orders = Order::orderBy('order_id', 'desc')->get(); // Untuk memilih order
        $menus = Menu::where('is_available', true)->orderBy('name')->get(); // Hanya menu yang tersedia
        $selectedOrderId = $request->input('order_id'); // Untuk pre-select jika datang dari halaman order

        return view('order_items.create', compact('orders', 'menus', 'selectedOrderId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
            'menu_id' => 'required|exists:menus,menu_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            // 'price_at_order' => 'required|numeric|min:0', // Jika Anda menambahkan kolom ini
        ]);

        if ($validator->fails()) {
            return redirect()->route('order_items.create', ['order_id' => $request->order_id])
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->all();
        // Jika Anda mau menyimpan harga saat ini dari menu:
        // $menu = Menu::find($request->menu_id);
        // if ($menu) {
        //     $data['price_at_order'] = $menu->price;
        // }

        OrderItem::create($data);

        // Opsional: Update total_price di Order induk setelah item ditambahkan
        // $this->updateOrderTotalPrice($request->order_id);

        return redirect()->route('order_items.index', ['order_id' => $request->order_id])->with('success', 'Item berhasil ditambahkan ke order.');
    }

    /**
     * Display the specified resource.
     * (Biasanya tidak terlalu dibutuhkan untuk OrderItem standalone)
     *
     * @param  \App\Models\OrderItem  $orderItem_id (gunakan nama parameter sesuai route:list)
     * @return \Illuminate\Http\Response
     */
    public function show(OrderItem $order_item) // Ganti $orderItem_id menjadi $order_item jika menggunakan route model binding dengan nama parameter 'order_item'
    {
        $order_item->load(['order', 'menu']);
        return view('order_items.show', compact('order_item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderItem  $orderItem_id
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderItem $order_item)
    {
        $order_item->load('order'); // Perlu order untuk konteks
        $orders = Order::orderBy('order_id', 'desc')->get();
        $menus = Menu::where('is_available', true)->orderBy('name')->get();

        return view('order_items.edit', compact('order_item', 'orders', 'menus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderItem  $orderItem_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderItem $order_item)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id', // Seharusnya tidak diubah, tapi untuk validasi
            'menu_id' => 'required|exists:menus,menu_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            // 'price_at_order' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('order_items.edit', $order_item->item_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->all();
        // Jika Anda mau menyimpan harga saat ini dari menu (jika menu diubah):
        // if ($order_item->menu_id != $request->menu_id || !$order_item->price_at_order) {
        //     $menu = Menu::find($request->menu_id);
        //     if ($menu) {
        //         $data['price_at_order'] = $menu->price;
        //     }
        // }

        $order_item->update($data);

        // Opsional: Update total_price di Order induk setelah item diubah
        // $this->updateOrderTotalPrice($order_item->order_id);


        return redirect()->route('order_items.index', ['order_id' => $order_item->order_id])->with('success', 'Item order berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderItem  $orderItem_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderItem $order_item)
    {
        $orderId = $order_item->order_id; // Simpan order_id sebelum dihapus
        $order_item->delete();

        // Opsional: Update total_price di Order induk setelah item dihapus
        // $this->updateOrderTotalPrice($orderId);

        return redirect()->route('order_items.index', ['order_id' => $orderId])->with('success', 'Item order berhasil dihapus.');
    }

    /**
     * Helper function to update total_price in the parent Order.
     * (Panggil ini setelah store, update, destroy OrderItem jika diperlukan)
     */
    // protected function updateOrderTotalPrice($orderId)
    // {
    //     $order = Order::with('items')->find($orderId);
    //     if ($order) {
    //         $totalPrice = 0;
    //         foreach ($order->items as $item) {
    //             // Asumsi Anda memiliki 'price_at_order' di OrderItem atau mengambil dari Menu
    //             // Jika pakai price_at_order di OrderItem:
    //             // $subtotal = $item->quantity * $item->price_at_order;
    //             // Jika ambil dari Menu (harga bisa berubah):
    //             $subtotal = $item->menu ? ($item->quantity * $item->menu->price) : 0;
    //             $totalPrice += $subtotal;
    //         }
    //         $order->total_price = $totalPrice;
    //         $order->save();
    //     }
    // }
}