<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders'; // Eksplisit nama tabel
    protected $primaryKey = 'order_id';
    public $incrementing = true;
    protected $keyType = 'int'; // Sesuai untuk bigint

    protected $fillable = [
        'user_id',
        'order_type',
        'status',
        'total_price',
        'payment_method',
        'delivery_address',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        // 'user_id' => 'integer', // Opsional, Eloquent biasanya sudah pintar
    ];

    /**
     * Default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending', // Sesuai default di DB
    ];

    // Konstanta untuk tipe dan status order
    public const TYPE_PICKUP = 'pickup';
    public const TYPE_DELIVERY = 'delivery';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_DELIVERING = 'delivering'; // Tambahan untuk delivery jika ada

    /**
     * Relasi dengan User.
     */
    public function user()
    {
        // Pastikan primary key di model User adalah 'user_id' jika Anda menggunakan 'user_id' sebagai foreign key di sini.
        // Jika primary key di User adalah 'id' (default), maka: return $this->belongsTo(User::class); (Laravel akan menebak foreign key 'user_id')
        // Atau return $this->belongsTo(User::class, 'user_id', 'id'); jika foreign key 'user_id' merujuk ke 'id' di tabel users.
        return $this->belongsTo(User::class, 'user_id', 'user_id'); // Asumsi primary key User adalah user_id
    }

    /**
     * Relasi dengan Item-item Order.
     */
    public function items() // Diubah dari orderItems untuk konsistensi dengan controller
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Relasi dengan Pembayaran.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    /**
     * Relasi dengan Pengiriman.
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'order_id', 'order_id');
    }

    /**
     * Relasi dengan Review.
     */
    public function review()
    {
        return $this->hasOne(Review::class, 'order_id', 'order_id');
    }

    /**
     * Helper untuk mendapatkan daftar tipe order.
     *
     * @return array
     */
    public static function getOrderTypes(): array
    {
        return [
            self::TYPE_PICKUP => 'Pickup',
            self::TYPE_DELIVERY => 'Delivery',
        ];
    }

    /**
     * Helper untuk mendapatkan daftar status order.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_DELIVERING => 'Delivering',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}