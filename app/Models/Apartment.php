<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'tenant_name',
        'tenant_phone',
        'tenant_email',
    ];
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
