<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('users_donations', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('user_id')->cascadeOnDelete()->nullable();  
            $table->unsignedBigInteger('donation_id')->cascadeOnDelete()->nullable(); 
            
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');

        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('users_donations');

    }
};
