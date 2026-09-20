<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // is_active and categorie_id are already indexed. 
            // We just add created_at as requested.
            $table->index('created_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            // Composite Index: ممتاز جداً لترتيب الرسائل داخل محادثة محددة زمنياً
            $table->index(['conversation_id', 'created_at'], 'msg_conv_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('msg_conv_created_idx');
        });
    }
};
