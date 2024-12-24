<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class campaign_donation extends Model
{
    use HasFactory;


    protected $table = 'campaigns_donations'; // تأكد من اسم الجدول الصحيح
    protected $guarded = [];




}
