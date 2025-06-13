<?php

namespace App\Http\Controllers;

use App\Models\Notification; // Sesuaikan jika nama model Anda NotificationModel
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Notification::with('user')->latest('created_at'); // Eager load user

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('is_read') && $request->is_read != '') {
            $query->where('is_read', $request->is_read === '1' || $request->is_read === 'true');
        }

        $notifications = $query->paginate(15);
        $users = User::orderBy('name')->get(); // Untuk filter

        return view('notifications.index', compact('notifications', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     * Admin bisa mengirim notifikasi ke user tertentu.
     */
    public function create(Request $request)
    {
        $users = User::orderBy('name')->get();
        $selectedUserId = $request->input('user_id');

        return view('notifications.create', compact('users', 'selectedUserId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'message' => 'required|string',
            'is_read' => 'sometimes|boolean', // Checkbox
        ]);

        if ($validator->fails()) {
            return redirect()->route('notifications.create', ['user_id' => $request->user_id])
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->all();
        $data['is_read'] = $request->has('is_read') ? 1 : 0; // Handle checkbox

        Notification::create($data); // Eloquent akan mengisi created_at

        return redirect()->route('notifications.index')->with('success', 'Notifikasi berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification) // Route model binding
    {
        $notification->load('user');
        return view('notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     * (Mengedit notifikasi mungkin jarang, lebih sering menandai sudah dibaca)
     */
    public function edit(Notification $notification)
    {
        $notification->load('user');
        // User ID biasanya tidak diubah
        $users = User::where('user_id', $notification->user_id)->get();

        return view('notifications.edit', compact('notification', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        $validator = Validator::make($request->all(), [
            // user_id tidak diubah
            'message' => 'required|string',
            'is_read' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('notifications.edit', $notification->notif_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->only(['message', 'is_read']);
        $data['is_read'] = $request->has('is_read') ? 1 : 0; // Handle checkbox

        $notification->update($data); // Eloquent akan mengelola updated_at jika ada

        return redirect()->route('notifications.index')->with('success', 'Notifikasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Mark a notification as read.
     * (Contoh aksi custom jika diperlukan selain CRUD standar)
     */
    // public function markAsRead(Notification $notification)
    // {
    //     $notification->is_read = true;
    //     $notification->save();
    //     return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    // }
}