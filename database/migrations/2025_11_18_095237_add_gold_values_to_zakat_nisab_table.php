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
        Schema::table('zakat_nisab', function (Blueprint $table) {
           $table->decimal('nisab_24', 10, 2)->nullable();
        $table->decimal('nisab_21', 10, 2)->nullable();
        $table->decimal('nisab_18', 10, 2)->nullable();

        // سعر لكل عيار
        $table->decimal('price_24', 10, 2)->nullable();
        $table->decimal('price_21', 10, 2)->nullable();
        $table->decimal('price_18', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zakat_nisab', function (Blueprint $table) {
             $table->dropColumn([
            'nisab_24', 'nisab_21', 'nisab_18',
            'price_24', 'price_21', 'price_18'
        ]);
        });
    }
};
