<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EzonpayYusser extends Model
{
    use HasFactory;

    // 🔴 ضروري جداً: تحديد اسم الجدول لأن فيه شرطة (-)
   protected $table = 'ezonpay_yusser';

    protected $fillable = [
        'order_ref',
        'amount',
        'status',
        'payment_link',
        'response_data',
    ];

    protected $casts = [
        'response_data' => 'array', // لتحويل JSON تلقائياً لمصفوفة
    ];
}