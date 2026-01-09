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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        
        // Insérer les valeurs par défaut
        DB::table('settings')->insert([
            ['key' => 'full_access_code', 'value' => '123456'],
            ['key' => 'limited_access_code', 'value' => '654321'],
            ['key' => 'opening_date', 'value' => now()->format('Y-m-d')],
            ['key' => 'closing_date', 'value' => now()->addMonths(6)->format('Y-m-d')],
            ['key' => 'min_age', 'value' => '16'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};