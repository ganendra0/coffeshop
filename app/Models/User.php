<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens; // <--- IMPORT NAMESPACE YANG BENAR
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <--- GUNAKAN TRAITNYA

    protected $primaryKey = 'user_id';

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
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(NotificationModel::class, 'user_id', 'user_id');
    }
}