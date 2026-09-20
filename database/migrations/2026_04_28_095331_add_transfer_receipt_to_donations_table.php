<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('transfer_receipt')->nullable()->after('amount'); // يمكنك تغيير مكان العمود
            $table->string('status')->default('pending')->change(); // للتأكد من وجود حالة للتبرع
        });
    }

    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('transfer_receipt');
        });
    }
};