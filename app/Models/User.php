<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use  HasFactory, Notifiable;

    protected $primaryKey = 'user_id'; // Tentukan PK

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', // Jika ada field ini
        'password' => 'hashed',
    ];

    // Jika hanya ada 'created_at' dan tidak ada 'updated_at' sesuai skema awal
    // const UPDATED_AT = null;

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(NotificationModel::class, 'user_id', 'user_id');
    }
}

