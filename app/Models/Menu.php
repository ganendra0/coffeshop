<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    protected $primaryKey = 'menu_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'price',
        'category',
        'stock',
        'is_available',
        'image_url', // Berisi path relatif seperti 'menu_images/namafile.jpg'
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
    protected $appends = ['full_image_url']; // Penting agar accessor bisa diakses seperti field biasa

    /**
     * Get the full URL for the menu image.
     *
     * @return string|null
     */
    public function getFullImageUrlAttribute(): ?string
    {
        // Pastikan image_url tidak kosong DAN file benar-benar ada di disk 'public'
        if ($this->image_url && Storage::disk('public')->exists($this->image_url)) {
            // Storage::disk('public')->url('menu_images/namafile.jpg') akan menghasilkan
            // '/storage/menu_images/namafile.jpg' (setelah php artisan storage:link)
            return Storage::disk('public')->url($this->image_url);
        }
        // Opsional: kembalikan URL placeholder jika tidak ada gambar atau path tidak valid
        // Contoh: return asset('images/default_placeholder.png');
        return null; // Atau URL gambar default jika Anda punya
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_id', 'menu_id');
    }
}