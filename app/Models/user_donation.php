<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_donation extends Model
{
    use HasFactory;

    protected $table = 'users_donations'; // تأكد من اسم الجدول الصحيح
    protected $fillable = ['user_id', 'donation_id'];



}
