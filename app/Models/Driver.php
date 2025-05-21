<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $primaryKey = 'driver_id';

    protected $fillable = [
        'name',
        'phone',
        'status',
        'vehicle_type',
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'driver_id', 'driver_id');
    }
}