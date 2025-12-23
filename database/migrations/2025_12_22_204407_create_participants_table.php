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
        Schema::create('participants', function (Blueprint $table) {
    $table->id();
    $table->string('lastname');
    $table->string('firstname');
    $table->string('email')->nullable();
    $table->string('telephone')->nullable();
    $table->string('zipcode')->nullable();
    $table->boolean('optin')->default(false);
    $table->boolean('bysms')->default(false);
    $table->boolean('byemail')->default(false);
    $table->string('slug')->unique();
    $table->string('source')->nullable();
    $table->timestamps();

    $table->unique('email');
    $table->unique('telephone');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
