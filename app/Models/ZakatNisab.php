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
    ];

    // إذا كنت تريد تحديد تنسيق التاريخ
    protected $dates = [
        'last_updated',
    ];
}
