<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class campaign extends Model
{
    use HasFactory;

    protected $guarded = [];


     

    
    public function categorie()
    {
        return $this->belongsTo(categorie::class,'categorie_id');
    }
    

    public function donation()
    {
        return $this->belongsToMany(donation::class,'campaigns_donations');
    }


}
