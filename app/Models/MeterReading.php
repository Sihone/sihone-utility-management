<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'reading_date',
        'meter_index',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
