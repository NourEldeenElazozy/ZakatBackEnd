<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
           Schema::create('campaigns_donations', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('donation_id')->cascadeOnDelete()->nullable();  
            $table->unsignedBigInteger('campaign_id')->cascadeOnDelete()->nullable();  

            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('campaigns_donations');

    }
};
