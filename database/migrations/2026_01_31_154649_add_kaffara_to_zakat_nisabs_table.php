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
    Schema::table('zakat_nisab', function (Blueprint $table) {
        $table->decimal('kaffarat_yameen', 8, 2)->nullable()->after('price_18'); // كفارة اليمين
        $table->decimal('fidyah_siyam', 8, 2)->nullable()->after('kaffarat_yameen'); // فدية الصيام
    });
}

public function down()
{
    Schema::table('zakat_nisabs', function (Blueprint $table) {
        $table->dropColumn(['kaffarat_yameen', 'fidyah_siyam']);
    });
}
};
