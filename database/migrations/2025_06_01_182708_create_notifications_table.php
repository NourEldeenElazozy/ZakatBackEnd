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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم، يمكن أن يكون null إذا كان إشعاراً عاماً
            $table->string('title');
            $table->text('body');
            $table->string('target_type')->comment('device, topic'); // نوع الاستهداف (مستخدم، موضوع)
            $table->string('target_value')->nullable(); // قيمة الاستهداف (token الجهاز أو اسم الموضوع)
            $table->boolean('is_read')->default(false); // هل قرأ المستخدم الإشعار؟
            $table->timestamps();

            // إضافة foreign key constraint (اختياري لكن موصى به)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
