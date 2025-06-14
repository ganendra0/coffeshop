<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * Primary key untuk model.
     *
     * @var string
     */
    protected $primaryKey = 'order_id';

    /**
     * Menunjukkan jika primary key adalah auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Tipe data dari primary key.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'payment_id', // DITAMBAHKAN: Sesuai skema baru
        'order_type', // DITAMBAHKAN: Sesuai skema baru
        'status',
        'total_price',
        'delivery_address', // DITAMBAHKAN: Sesuai skema baru
        'estimated_delivery_time', // DITAMBAHKAN: Sesuai skema baru
        'notes_for_restaurant', // DITAMBAHKAN: Sesuai skema baru
    ];

    /**
     * Tipe data native untuk atribut.
     *
     * @var array
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'estimated_delivery_time' => 'datetime',
    ];


    // --- RELASI ---

    /**
     * Relasi ke model User.
     * Satu pesanan dimiliki oleh satu pengguna.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke model Payment.
     * Satu pesanan memiliki satu pembayaran.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Relasi ke model OrderItem.
     * Satu pesanan memiliki banyak item pesanan.
     * DIPERBAIKI: Nama method disesuaikan menjadi 'orderItems' agar cocok dengan controller.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Relasi ke model Review.
     * Satu pesanan bisa memiliki banyak ulasan (jika ulasan per item).
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'order_id', 'order_id');
    }


    // --- HELPERS & CONSTANTS ---

    public const TYPE_PICKUP = 'pickup';
    public const TYPE_DELIVERY = 'delivery';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Helper untuk mendapatkan daftar tipe order.
     * @return array
     */
    public static function getOrderTypes(): array
    {
        return [
            self::TYPE_PICKUP => 'Ambil Sendiri (Pickup)',
            self::TYPE_DELIVERY => 'Diantar (Delivery)',
        ];
    }

    /**
     * Helper untuk mendapatkan daftar status order.
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }
}