<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->bigInteger('categorie_id')->unsigned();
            $table->foreign('categorie_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->string('image');
            $table->double('total');
            $table->double('paid_up');
            $table->string('recipient');
            $table->string('state_campaign');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('compaigns');
    }
};
