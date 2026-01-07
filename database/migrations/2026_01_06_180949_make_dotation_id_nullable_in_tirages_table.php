<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tirages', function (Blueprint $table) {
            // D'abord, supprimer la contrainte étrangère
            $table->dropForeign(['dotation_id']);
            // Puis rendre la colonne nullable
            $table->foreignId('dotation_id')->nullable()->change();
            // Recréer la contrainte étrangère
            $table->foreign('dotation_id')->references('id')->on('dotations');
        });
    }

    public function down()
    {
        Schema::table('tirages', function (Blueprint $table) {
            // Supprimer les tirages sans dotation_id
            \DB::table('tirages')->whereNull('dotation_id')->delete();
            
            // Supprimer la contrainte étrangère
            $table->dropForeign(['dotation_id']);
            // Rendre la colonne non nullable
            $table->foreignId('dotation_id')->nullable(false)->change();
            // Recréer la contrainte étrangère
            $table->foreign('dotation_id')->references('id')->on('dotations');
        });
    }
};