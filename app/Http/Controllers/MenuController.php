<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::latest()->paginate(10);
        // DIUBAH: Mengarahkan ke view di dalam folder admin
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // DIUBAH: Mengarahkan ke view di dalam folder admin
        return view('admin.menus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:30',
            'stock' => 'required|integer|min:0',
            'is_available' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('menus.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->only(['name', 'price', 'category', 'stock']);
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_images', 'public');
            $data['image_url'] = $imagePath;
        }

        Menu::create($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu) // Menggunakan variabel $menu
    {
        // DIUBAH: Menggunakan view admin dan variabel $menu
        return view('admin.menus.show', ['menu' => $menu]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu) // Menggunakan variabel $menu
    {
        // DIUBAH: Menggunakan view admin dan variabel $menu
        return view('admin.menus.edit', ['menu' => $menu]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu) // Menggunakan variabel $menu
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:30',
            'stock' => 'required|integer|min:0',
            'is_available' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            // DIUBAH: Menggunakan $menu->menu_id untuk mendapatkan ID
            return redirect()->route('menus.edit', $menu->menu_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->only(['name', 'price', 'category', 'stock']);
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            // DIUBAH: Menggunakan $menu->image_url untuk mengecek dan menghapus gambar lama
            if ($menu->image_url && Storage::disk('public')->exists($menu->image_url)) {
                Storage::disk('public')->delete($menu->image_url);
            }

            $newImagePath = $request->file('image')->store('menu_images', 'public');
            $data['image_url'] = $newImagePath;
        }

        // DIUBAH: Menggunakan variabel $menu untuk update
        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', 'Data menu berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu) // Menggunakan variabel $menu
    {
        // DIUBAH: Menggunakan $menu->image_url untuk mengecek dan menghapus gambar
        if ($menu->image_url && Storage::disk('public')->exists($menu->image_url)) {
            Storage::disk('public')->delete($menu->image_url);
        }

        // DIUBAH: Menggunakan variabel $menu untuk delete
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus.');
    }
}