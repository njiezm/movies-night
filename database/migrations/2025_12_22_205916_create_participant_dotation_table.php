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
        Schema::create('participant_dotation', function (Blueprint $table) {
    $table->id();
    $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
    $table->foreignId('dotation_id')->constrained()->cascadeOnDelete();
    $table->timestamps();

    $table->unique(['participant_id', 'dotation_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_dotation');
    }
};
