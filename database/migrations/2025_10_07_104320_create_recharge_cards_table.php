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
        Schema::create('recharge_cards', function (Blueprint $table) {
      $table->id();
            $table->string('code')->unique(); // رقم الكارت
            $table->decimal('value', 10, 2); // قيمة الشحن
            $table->boolean('used')->default(false); // هل تم استخدامه
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharge_cards');
    }
};
