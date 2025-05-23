<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Tambahkan ini

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments'; // Eksplisit nama tabel
    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int'; // Sesuai untuk bigint

    protected $fillable = [
        'order_id',
        'amount',
        'status',
        'payment_proof', // Akan menyimpan path relatif
        'payment_time',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_time' => 'datetime', // Untuk konversi otomatis ke objek Carbon
    ];

    /**
     * Default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending', // Default status dari DB
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // Jika Anda ingin accessor payment_proof_url selalu ada
    // protected $appends = ['payment_proof_url'];


    // Relasi ke Order (Induk)
    public function order()
    {
        // Foreign key 'order_id' di tabel ini, merujuk ke primary key 'order_id' di tabel 'orders'
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Accessor untuk mendapatkan URL lengkap dari payment_proof.
     *
     * @return string|null
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        if ($this->payment_proof && Storage::disk('public')->exists($this->payment_proof)) {
            return Storage::disk('public')->url($this->payment_proof);
        }
        return null; // Atau URL placeholder jika ada
    }

    // Konstanta untuk status payment
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid'; // atau 'completed', 'verified'
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REFUNDED => 'Refunded',
        ];
    }
}