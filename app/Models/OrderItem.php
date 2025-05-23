<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items'; // Eksplisit nama tabel
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int'; // Sesuai untuk bigint

    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'notes',
        // Sebaiknya tambahkan 'price_at_order' atau 'unit_price' di sini jika harga menu bisa berubah
        // dan Anda ingin menyimpan harga saat item dipesan. Jika tidak, Anda akan selalu mengambil harga terbaru dari Menu.
    ];

    protected $casts = [
        'quantity' => 'integer',
        // 'order_id' => 'integer', // Opsional
        // 'menu_id' => 'integer', // Opsional
    ];

    // Relasi ke Order (Induk)
    public function order()
    {
        // Foreign key 'order_id' di tabel ini, merujuk ke primary key 'order_id' di tabel 'orders'
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // Relasi ke Menu (Produk yang dipesan)
    public function menu()
    {
        // Foreign key 'menu_id' di tabel ini, merujuk ke primary key 'menu_id' di tabel 'menus'
        return $this->belongsTo(Menu::class, 'menu_id', 'menu_id');
    }

    // Accessor untuk subtotal (opsional, tapi berguna)
    // public function getSubtotalAttribute()
    // {
    //     // Jika Anda menyimpan price_at_order di OrderItem:
    //     // return $this->quantity * $this->price_at_order;

    //     // Jika Anda mengambil harga dari Menu terkait (harga bisa berubah):
    //     if ($this->menu) {
    //         return $this->quantity * $this->menu->price;
    //     }
    //     return 0;
    // }
}