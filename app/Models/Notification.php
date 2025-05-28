<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model // Menggunakan nama 'Notification'
{
    use HasFactory;

    protected $table = 'notifications'; // Nama tabel sudah benar
    protected $primaryKey = 'notif_id'; // Primary key sudah benar
    public $incrementing = true;
    protected $keyType = 'int'; // Sesuai untuk bigint

    protected $fillable = [
        'user_id',
        'message',
        'is_read',
        // 'created_at' tidak di fillable jika dihandle DB atau Eloquent
    ];

    protected $casts = [
        'is_read' => 'boolean', // tinyint(1) ke boolean
        // 'user_id' => 'integer', // Opsional
    ];

    /**
     * Default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_read' => 0, // Default is_read dari DB
    ];

    /**
     * Indicates if the model should be timestamped with created_at and updated_at.
     *
     * @var bool
     */
    public $timestamps = true; // Biarkan Eloquent mengelola created_at (updated_at tidak ada)

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null; // Tidak ada kolom updated_at di tabel

    // Relasi ke User
    public function user()
    {
        // Foreign key 'user_id' di tabel ini, merujuk ke primary key 'user_id' di tabel 'users'
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}