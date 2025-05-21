<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'review_id';

    protected $fillable = [
        'order_id',
        'rating',
        'comment',
        // 'created_at' tidak perlu di fillable jika diatur via ->useCurrent()
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Karena hanya ada created_at di skema
    const CREATED_AT = 'created_at'; // Beritahu Eloquent nama kolom created_at
    // const UPDATED_AT = null;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}