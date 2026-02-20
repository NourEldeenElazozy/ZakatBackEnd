<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashPayment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'phone', 'amount', 'status','donation_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function donation()
{
    return $this->belongsTo(Donation::class);
}
}
