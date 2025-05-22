<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Untuk mengelola file

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        // Pencarian berdasarkan nama
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter berdasarkan kategori
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan ketersediaan
        if ($request->has('is_available')) {
            $query->where('is_available', filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN));
        }

        $menus = $query->latest('menu_id')->paginate(10); // Urutkan berdasarkan menu_id terbaru, 10 item per halaman

        return response()->json([
            'success' => true,
            'message' => 'Daftar Data Menu',
            'data' => $menus
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:100|unique:menus,name',
            'price'        => 'required|numeric|min:0',
            'category'     => 'required|string|max:30',
            'stock'        => 'required|integer|min:0',
            'is_available' => 'sometimes|boolean',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048' // 'image_file' untuk upload
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        $data = $request->except('image_file'); // Ambil semua input kecuali file gambar sementara

        if ($request->hasFile('image_file')) {
            // Simpan file di 'storage/app/public/menus'
            // Path yang disimpan di DB adalah 'menus/namafile.ext'
            $filePath = $request->file('image_file')->store('menus', 'public');
            $data['image_url'] = $filePath;
        }

        // Pastikan 'is_available' memiliki nilai; jika tidak ada, default dari DB akan digunakan
        // Jika ada, konversi ke boolean
        if ($request->filled('is_available')) {
            $data['is_available'] = filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN);
        } else {
            // Jika tidak diisi, biarkan database menggunakan defaultnya (true) atau set manual
            // $data['is_available'] = true; // Jika ingin set eksplisit di sini
        }

        $menu = Menu::create($data);

        // Untuk menampilkan URL lengkap gambar, kita bisa buat accessor di model (lihat bawah)
        // Atau, jika modelnya tidak mengembalikan URL lengkap:
        // if ($menu->image_url) {
        //     $menu->image_url = Storage::disk('public')->url($menu->image_url);
        // }


        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan',
            'data'    => $menu->load([]) // load([]) untuk memastikan accessor dipanggil jika ada
        ], 201); // Created
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Menu $menu) // Route Model Binding
    {
        // Jika ingin URL gambar lengkap (jika belum pakai accessor di model):
        // if ($menu->image_url) {
        //     $menu->image_url = Storage::disk('public')->url($menu->image_url);
        // }

        return response()->json([
            'success' => true,
            'message' => 'Detail Menu',
            'data'    => $menu
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Menu $menu)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'sometimes|required|string|max:100|unique:menus,name,' . $menu->menu_id . ',menu_id', // Abaikan unique untuk record saat ini
            'price'        => 'sometimes|required|numeric|min:0',
            'category'     => 'sometimes|required|string|max:30',
            'stock'        => 'sometimes|required|integer|min:0',
            'is_available' => 'sometimes|boolean',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->except('image_file');

        if ($request->hasFile('image_file')) {
            // Hapus gambar lama jika ada dan file baru diupload
            if ($menu->image_url && Storage::disk('public')->exists($menu->image_url)) {
                Storage::disk('public')->delete($menu->image_url);
            }
            // Simpan gambar baru
            $filePath = $request->file('image_file')->store('menus', 'public');
            $data['image_url'] = $filePath;
        } else if ($request->has('remove_image') && filter_var($request->remove_image, FILTER_VALIDATE_BOOLEAN)) {
            // Jika ada parameter 'remove_image' bernilai true dan tidak ada file baru
            if ($menu->image_url && Storage::disk('public')->exists($menu->image_url)) {
                Storage::disk('public')->delete($menu->image_url);
            }
            $data['image_url'] = null; // Hapus path gambar dari database
        }


        if ($request->filled('is_available')) {
            $data['is_available'] = filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN);
        }

        $menu->update($data);

        // Untuk menampilkan URL lengkap gambar (jika belum pakai accessor di model):
        // if ($menu->image_url) {
        //     $menu->image_url = Storage::disk('public')->url($menu->image_url);
        // }

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui',
            'data'    => $menu->fresh() // Ambil data terbaru dari DB, termasuk accessor
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Menu $menu)
    {
        // Hapus gambar dari storage jika ada
        if ($menu->image_url && Storage::disk('public')->exists($menu->image_url)) {
            Storage::disk('public')->delete($menu->image_url);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus'
        ], 200); // Atau 204 No Content
    }
}