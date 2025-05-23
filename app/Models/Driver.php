<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers'; // Eksplisit nama tabel
    protected $primaryKey = 'driver_id';
    public $incrementing = true;
    protected $keyType = 'int'; // Sesuai untuk bigint

    protected $fillable = [
        'name',
        'phone',
        'status',
        'vehicle_type',
    ];

    // Tidak ada $casts khusus yang terlihat diperlukan dari struktur tabel,
    // kecuali jika Anda ingin 'status' menjadi enum atau tipe khusus.

    /**
     * Default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'available', // Sesuai default di DB
    ];

    // Relasi ke Deliveries (jika Anda punya model Delivery)
    // Pastikan model Delivery ada dan memiliki foreign key 'driver_id'
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'driver_id', 'driver_id');
    }

    // Konstanta untuk status driver (opsional tapi bagus)
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ON_DELIVERY = 'on_delivery';
    public const STATUS_UNAVAILABLE = 'unavailable';
    // Tambahkan status lain jika perlu

    public static function getStatuses()
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_ON_DELIVERY => 'On Delivery',
            self::STATUS_UNAVAILABLE => 'Unavailable',
        ];
    }

    // Anda juga bisa menambahkan helper untuk vehicle_type jika pilihannya terbatas
    public static function getVehicleTypes()
    {
        return [
            'Motorcycle' => 'Motorcycle',
            'Car' => 'Car',
            'Van' => 'Van',
            // Tambahkan tipe lain
        ];
    }
}