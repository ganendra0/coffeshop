<?php

namespace App\Http\Controllers;

use App\Models\User; // Pastikan model User di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Untuk validasi
use Illuminate\Validation\Rules; // Untuk aturan password

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10); // Ambil 10 user terbaru dengan pagination
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'], // Sesuai gambar varchar(15)
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // Rules\Password::defaults() -> min 8 karakter
            'address' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            // remember_token, created_at, updated_at akan dihandle Laravel
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user_id) // Route model binding
    {
        // $user_id akan menjadi instance User yang ditemukan berdasarkan ID
        return view('users.show', ['user' => $user_id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user_id) // Route model binding
    {
        return view('users.edit', ['user' => $user_id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user_id) // Route model binding
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$user_id->user_id.',user_id'], // Abaikan email user saat ini
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Password opsional
            'address' => ['nullable', 'string'],
        ]);

         if ($validator->fails()) {
            return redirect()->route('users.edit', $user_id->user_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user_id->update($dataToUpdate);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user_id) // Route model binding
    {
        try {
            $user_id->delete();
            return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            //  Log error atau tampilkan pesan error jika ada foreign key constraint, dll.
            return redirect()->route('users.index')->with('error', 'Gagal menghapus pengguna. Mungkin terkait dengan data lain.');
        }
    }
}