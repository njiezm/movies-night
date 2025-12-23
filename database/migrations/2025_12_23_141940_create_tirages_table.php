<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tirages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('dotation_id')->constrained();
            $table->date('date');
            $table->foreignId('winner_id')->nullable()->constrained('participants');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tirages');
    }
};