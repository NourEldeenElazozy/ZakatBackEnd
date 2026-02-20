<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة عمود device_token
            // يمكن أن يكون string لأنه قد يحتوي على أحرف وأرقام ورموز
            // يمكن أن يكون nullable في البداية لأن بعض المستخدمين قد لا يمتلكون رمزًا بعد
            // يمكن أن يكون unique لضمان عدم تكرار نفس الرمز لأكثر من مستخدم (لكن هذا قد يحد من قدرة المستخدم الواحد على تسجيل الدخول من عدة أجهزة)
            // الأفضل أن يكون غير فريد (ليس unique) إذا كان المستخدم يمكن أن يسجل الدخول من عدة أجهزة،
            // وفي هذه الحالة، يمكن إنشاء جدول منفصل for device_tokens كما ذكرنا سابقاً للحلول الأكثر تعقيداً.
            // ولكن الآن، سنبدأ بعمود بسيط في جدول المستخدمين.
            $table->string('device_token')->nullable()->after('password'); // ضعها بعد كلمة المرور مثلاً
            $table->index('device_token'); // إضافة فهرس لتحسين أداء البحث إذا كنت ستبحث به
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropIndex(['device_token']); // حذف الفهرس أولاً
            $table->dropColumn('device_token'); // حذف العمود
        });
    }
};
