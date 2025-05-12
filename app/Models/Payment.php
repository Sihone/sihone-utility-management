<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'apartment_id',
        'amount',
        'payment_date',
        'notes',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
