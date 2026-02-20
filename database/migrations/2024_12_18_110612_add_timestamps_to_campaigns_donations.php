<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('campaigns_donations', function (Blueprint $table) {
            $table->timestamps(); // إضافة الأعمدة created_at و updated_at
        });
    }

    public function down()
    {
        Schema::table('campaigns_donations', function (Blueprint $table) {
            $table->dropTimestamps(); // حذف الأعمدة
        });
    }

};
