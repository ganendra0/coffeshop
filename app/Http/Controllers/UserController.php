<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        // Path view sudah benar
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // DIPERBAIKI: Mengarahkan ke view admin
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // DIPERBAIKI: Validasi role disesuaikan dengan enum di migrasi
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => ['nullable', 'string'],
            'role' => ['required', Rule::in(['customer', 'cashier', 'admin'])],
        ]);

        if ($validator->fails()) {
            // DIPERBAIKI: Mengarahkan ke route admin
            return redirect()->route('admin.users.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) // DIPERBAIKI: Menggunakan variabel $user
    {
        // DIPERBAIKI: Mengarahkan ke view admin dan menggunakan variabel $user
        return view('admin.users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user) // DIPERBAIKI: Menggunakan variabel $user
    {
        // DIPERBAIKI: Mengarahkan ke view admin dan menggunakan variabel $user
        return view('admin.users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user) // DIPERBAIKI: Menggunakan variabel $user
    {
        // DIPERBAIKI: Validasi disesuaikan dengan skema baru dan pengecualian unik
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'address' => ['nullable', 'string'],
            'role' => ['required', Rule::in(['customer', 'cashier', 'admin'])],
        ]);

         if ($validator->fails()) {
             // DIPERBAIKI: Mengarahkan ke route admin dan menggunakan ID dari objek $user
            return redirect()->route('admin.users.edit', $user->user_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $dataToUpdate = $request->only(['name', 'email', 'phone', 'address', 'role']);

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // DIPERBAIKI: Menggunakan objek $user untuk update
        $user->update($dataToUpdate);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) // DIPERBAIKI: Menggunakan variabel $user
    {
        try {
            // DIPERBAIKI: Menggunakan objek $user untuk delete
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error jika user masih memiliki order/review/payment
            return redirect()->route('admin.users.index')->with('error', 'Gagal menghapus pengguna. Pengguna ini masih terkait dengan data pesanan atau ulasan.');
        }
    }
}