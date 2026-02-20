<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mobipayments extends Model
{
    use HasFactory;
     protected $fillable = [
        'payment_uuid',
        'amount',
        'status',
        'card_number',
        'description',
        'verified_at',
        'response_data',
    ];
}
