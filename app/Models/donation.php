<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class donation extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function campaign()
    {
        return $this->belongsToMany(campaign::class,'campaigns_donations');
    }

       
    public function user()
    {
        return $this->belongsToMany(User::class,'users_donations');
    }

    

}
