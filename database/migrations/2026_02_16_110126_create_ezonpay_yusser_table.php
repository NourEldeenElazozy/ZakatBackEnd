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
        Schema::create('ezonpay_yusser', function (Blueprint $table) {
            $table->id();
            
            // البيانات الأساسية
            $table->string('order_ref')->index(); // رقم الطلب للمطابقة
            $table->decimal('amount', 10, 2);
            
            // الحالة (pending, success, failed)
            $table->string('status')->default('pending');
            
            // لحفظ الرابط المولد
            $table->text('payment_link')->nullable();
            
            // لحفظ بيانات الاستجابة كاملة (للتتبع وحل المشاكل)
            $table->json('response_data')->nullable(); // الرد الكامل من يسر باي
            
            // تواريخ الإنشاء والتحديث
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ezonpay_yusser');
    }
};
