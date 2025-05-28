<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'deliveries'; // Eksplisit nama tabel
    protected $primaryKey = 'delivery_id';
    public $incrementing = true;
    protected $keyType = 'int'; // Sesuai untuk bigint

    protected $fillable = [
        'order_id',
        'driver_id',
        'status',
        'delivery_time', // Waktu aktual pengiriman (bisa null jika belum terkirim)
    ];

    protected $casts = [
        'delivery_time' => 'datetime', // Untuk konversi otomatis ke objek Carbon
        // 'order_id' => 'integer', // Opsional
        // 'driver_id' => 'integer', // Opsional
    ];

    /**
     * Default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'assigned', // Sesuai default di DB
    ];

    // Relasi ke Order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // Relasi ke Driver
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'driver_id');
    }

    // Konstanta untuk status delivery
    public const STATUS_ASSIGNED = 'assigned'; // Driver sudah ditugaskan
    public const STATUS_ON_THE_WAY = 'on_the_way'; // Sedang dalam perjalanan
    public const STATUS_DELIVERED = 'delivered'; // Sudah sampai tujuan
    public const STATUS_FAILED = 'failed'; // Gagal kirim
    public const STATUS_RETURNED = 'returned'; // Dikembalikan

    public static function getStatuses()
    {
        return [
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_ON_THE_WAY => 'On The Way',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_RETURNED => 'Returned',
        ];
    }
}