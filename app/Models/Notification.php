<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'target_type',
        'target_value',
        'is_read',
    ];

    // تعريف العلاقة مع جدول المستخدمين
    public function user()
    {
     return $this->belongsTo(User::class);
    }
}