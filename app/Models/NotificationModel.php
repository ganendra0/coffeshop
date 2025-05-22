<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationModel extends Model 
{
    use HasFactory;

    protected $table = 'notifications';

    protected $primaryKey = 'notif_id';

    protected $fillable = [
        'user_id',
        'message',
        'is_read',
        // 'created_at' tidak perlu di fillable jika diatur via ->useCurrent()
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Karena hanya ada created_at di skema
    const CREATED_AT = 'created_at'; // Beritahu Eloquent nama kolom created_at
    // const UPDATED_AT = null; // Jika ingin Eloquent mengelola created_at tapi tidak updated_at

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}