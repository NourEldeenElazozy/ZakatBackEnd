<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    public function users()
{
    return $this->belongsToMany(User::class, 'users_donations', 'donation_id', 'user_id');
}

public function campaigns()
{
    return $this->belongsToMany(campaign::class, 'campaigns_donations', 'donation_id', 'campaign_id');
}
    
    

}
