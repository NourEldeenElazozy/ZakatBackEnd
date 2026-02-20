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
    Schema::table('users_donations', function (Blueprint $table) {
        $table->timestamps(); // إضافة created_at و updated_at
    });
}

public function down()
{
    Schema::table('users_donations', function (Blueprint $table) {
        $table->dropTimestamps(); // إزالة created_at و updated_at
    });
}

};
