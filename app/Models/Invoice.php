<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'meter_reading_id',
        'start_index',
        'end_index',
        'consumption',
        'amount',
        'invoice_date',
        'fixed_fee_used',
        'rate_per_m3_used',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function meterReading()
    {
        return $this->belongsTo(MeterReading::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->total_paid >= $this->amount;
    }
}
