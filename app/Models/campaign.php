<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    // إضافة حقل 'thumbnail' تلقائياً في ردود الـ JSON
    protected $appends = ['thumbnail'];

    /**
     * مُرجِع للصورة المصغرة (Thumbnail) كبديل للصورة الأصلية
     * يضمن التراجع الآمن (Fallback) للصور القديمة أو المفقودة
     */
    public function getThumbnailAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        $filenameWithoutExt = pathinfo($this->image, PATHINFO_FILENAME);
        $thumbPath = "thumbnails/{$filenameWithoutExt}.webp";
        
        // التحقق مما إذا كانت الصورة المصغرة موجودة فعلياً في السيرفر
        if (file_exists(public_path('public/img/' . $thumbPath))) {
            return $thumbPath;
        }

        // التراجع الآمن: إعادة الصورة الأصلية في حال عدم وجود المصغرة
        return $this->image;
    }

    public function categorie()
    {
        return $this->belongsTo(categorie::class,'categorie_id');
    }
    

    public function donation()
    {
        return $this->belongsToMany(donation::class,'campaigns_donations');
    }
    
    public function donations()
    {
        return $this->belongsToMany(donation::class, 'campaigns_donations', 'campaign_id', 'donation_id');
    }

}
