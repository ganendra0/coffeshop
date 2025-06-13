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
        return view('menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menus.create');
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi untuk file gambar
        ]);

        if ($validator->fails()) {
            return redirect()->route('menus.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->only(['name', 'price', 'category', 'stock']);
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            // Simpan file ke 'storage/app/public/menu_images'
            // dan dapatkan path relatif seperti 'menu_images/namafile.jpg'
            $imagePath = $request->file('image')->store('menu_images', 'public');
            $data['image_url'] = $imagePath; // Simpan path relatif ini ke database
        } else {
            $data['image_url'] = null; // Atau string kosong jika Anda mau
        }

        Menu::create($data);

        return redirect()->route('menus.index')->with('success', 'Menu baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu_id) // Route Model Binding berdasarkan primary key model
    {
        return view('menus.show', ['menu' => $menu_id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu_id) // Route Model Binding
    {
        return view('menus.edit', ['menu' => $menu_id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu_id) // Route Model Binding
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
            return redirect()->route('menus.edit', $menu_id->menu_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->only(['name', 'price', 'category', 'stock']);
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada dan pathnya tersimpan di DB
            // $menu_id->image_url akan berisi path relatif seperti 'menu_images/namafile.jpg'
            if ($menu_id->image_url && Storage::disk('public')->exists($menu_id->image_url)) {
                Storage::disk('public')->delete($menu_id->image_url);
            }

            // Simpan file baru dan dapatkan path relatifnya
            $newImagePath = $request->file('image')->store('menu_images', 'public');
            $data['image_url'] = $newImagePath; // Simpan path relatif baru ke database
        }
        // Jika tidak ada file gambar baru yang diupload, kita tidak mengubah $data['image_url']
        // sehingga nilai lama tetap di database, kecuali jika Anda ingin ada opsi menghapus gambar tanpa mengganti.

        $menu_id->update($data);

        return redirect()->route('menus.index')->with('success', 'Data menu berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu_id) // Route Model Binding
    {
        // Hapus gambar terkait jika ada dan pathnya tersimpan di DB
        // $menu_id->image_url akan berisi path relatif seperti 'menu_images/namafile.jpg'
        if ($menu_id->image_url && Storage::disk('public')->exists($menu_id->image_url)) {
            Storage::disk('public')->delete($menu_id->image_url);
        }

        $menu_id->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus.');
    }
}