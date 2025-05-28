<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews'; // Eksplisit nama tabel
    protected $primaryKey = 'review_id';
    public $incrementing = true;
    protected $keyType = 'int'; // Sesuai untuk bigint

    protected $fillable = [
        'order_id',
        'rating',
        'comment',
        // 'created_at' tidak perlu di fillable jika diatur oleh DB atau Eloquent via const
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Indicates if the model should be timestamped with created_at and updated_at.
     *
     * @var bool
     */
    public $timestamps = true; // Ubah ini ke true jika Anda ingin Eloquent mengelola created_at dan updated_at (jika ada)

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'created_at'; // Nama kolom created_at

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null; // Tidak ada kolom updated_at di tabel Anda

    // Relasi ke Order (Induk)
    public function order()
    {
        // Foreign key 'order_id' di tabel ini, merujuk ke primary key 'order_id' di tabel 'orders'
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // Relasi ke User melalui Order (untuk menampilkan nama user yang memberi review)
    public function user()
    {
        return $this->hasOneThrough(
            User::class,    // Model tujuan akhir
            Order::class,   // Model perantara
            'order_id',     // Foreign key di tabel Order (merujuk ke Review)
            'user_id',      // Foreign key di tabel User (merujuk ke Order)
            'order_id',     // Local key di tabel Review
            'user_id'       // Local key di tabel Order
        )->withDefault(['name' => 'Guest User']); // Default jika user tidak ditemukan
         // Pastikan primary key di model User adalah 'user_id' dan di Order 'order_id'
    }
}