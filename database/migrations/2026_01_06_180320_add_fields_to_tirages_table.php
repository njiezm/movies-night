<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tirages', function (Blueprint $table) {
            $table->foreignId('film_id')->nullable()->after('id')->constrained();
            $table->boolean('conf')->default(false)->after('winner_id');
            $table->text('condition_recuperation')->nullable()->after('conf');
            $table->boolean('is_big_tas')->default(false)->after('condition_recuperation');
        });
    }

    public function down()
    {
        Schema::table('tirages', function (Blueprint $table) {
            $table->dropForeign(['film_id']);
            $table->dropColumn(['film_id', 'conf', 'condition_recuperation', 'is_big_tas']);
        });
    }
};