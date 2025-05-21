<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Import Storage

class Menu extends Model
{
    use HasFactory;

    protected $primaryKey = 'menu_id';

    protected $fillable = [
        'name',
        'price',
        'category',
        'stock',
        'is_available',
        'image_url', // Kolom di DB untuk menyimpan path relatif
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'stock' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_image_url']; // Nama accessor

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_id', 'menu_id');
    }

    /**
     * Get the full URL for the menu image.
     *
     * @return string|null
     */
    public function getFullImageUrlAttribute(): ?string // Nama accessor method
    {
        if ($this->image_url && Storage::disk('public')->exists($this->image_url)) {
            return Storage::disk('public')->url($this->image_url);
        }
        // Opsional: kembalikan URL placeholder jika tidak ada gambar
        // return asset('images/placeholder_menu.png');
        return null;
    }
}