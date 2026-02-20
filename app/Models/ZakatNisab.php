<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZakatNisab extends Model
{
    protected $table = 'zakat_nisab';
    use HasFactory;
    protected $fillable = [
        'nisab_amount',
        'last_updated',
             'nisab_24', 'nisab_21', 'nisab_18',
        'price_24', 'price_21', 'price_18',
        'kaffarat_yameen', // جديد
    'fidyah_siyam'     // جديد
    ];

    // إذا كنت تريد تحديد تنسيق التاريخ
    protected $dates = [
        'last_updated',
    ];
}
