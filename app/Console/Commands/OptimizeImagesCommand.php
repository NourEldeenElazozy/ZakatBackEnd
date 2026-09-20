<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\campaign;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class OptimizeImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate WebP thumbnails for campaigns to improve performance (Non-destructive)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // استخدام مكتبة المعالجة (GD Driver)
        $manager = new ImageManager(new Driver());
        
        // جلب جميع الحملات التي تمتلك صورة
        $campaigns = campaign::whereNotNull('image')->get();
        
        // مسار الصور الأصلية 
        $baseDir = public_path('public/img');
        
        // مسار الصور المصغرة
        $thumbDir = $baseDir . '/thumbnails';

        // إنشاء المجلد إذا لم يكن موجوداً
        if (!File::exists($thumbDir)) {
            File::makeDirectory($thumbDir, 0755, true);
        }

        $this->info("Found {$campaigns->count()} campaigns. Processing...");

        $successCount = 0;
        $skipCount = 0;

        foreach ($campaigns as $campaign) {
            $originalPath = $baseDir . '/' . $campaign->image;
            
            // التأكد من أن الصورة الأصلية موجودة
            if (File::exists($originalPath) && !is_dir($originalPath)) {
                $filenameWithoutExt = pathinfo($campaign->image, PATHINFO_FILENAME);
                $thumbPath = $thumbDir . '/' . $filenameWithoutExt . '.webp';

                // التحقق مما إذا كانت الصورة المصغرة موجودة بالفعل لتجنب التكرار
                if (!File::exists($thumbPath)) {
                    try {
                        // قراءة الصورة الأصلية (دون تعديلها)
                        $image = $manager->read($originalPath);
                        
                        // تغيير الحجم مع الحفاظ على الأبعاد (أقصى عرض 600 بكسل)
                        $image->scale(width: 600);
                        
                        // حفظ الصورة كـ WebP بجودة 80 في مجلد thumbnails
                        $image->toWebp(80)->save($thumbPath);
                        
                        $this->line("Thumbnail created: {$filenameWithoutExt}.webp");
                        $successCount++;
                    } catch (\Exception $e) {
                        $this->error("Failed to process {$campaign->image}: " . $e->getMessage());
                    }
                } else {
                    $skipCount++;
                }
            }
        }

        $this->info("Done! Created: {$successCount}, Skipped (already exist): {$skipCount}");
    }
}
