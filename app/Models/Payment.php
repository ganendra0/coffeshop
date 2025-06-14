<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * Primary key untuk model.
     *
     * @var string
     */
    protected $primaryKey = 'payment_id';

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
        'order_id',
        'user_id', // DITAMBAHKAN: Sesuai skema baru
        'amount',
        'payment_method', // DITAMBAHKAN: Sesuai skema baru
        'payment_gateway_reference', // DITAMBAHKAN: Sesuai skema baru
        'status',
        'payment_time',
    ];

    /**
     * Tipe data native untuk atribut.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_time' => 'datetime',
    ];

    /**
     * Nilai default untuk atribut.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending', // Default status saat record baru dibuat
    ];

    // --- RELASI ---

    /**
     * Relasi ke model Order.
     * Satu pembayaran dimiliki oleh satu order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Relasi ke model User.
     * Satu pembayaran dimiliki oleh satu user (yang melakukan pembayaran).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }


    // --- HELPERS & CONSTANTS ---

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';

    /**
     * Method statis untuk mengambil semua status pembayaran yang valid.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_FAILED => 'Gagal',
        ];
    }
}