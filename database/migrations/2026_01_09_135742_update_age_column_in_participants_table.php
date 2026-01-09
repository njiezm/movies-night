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
            Schema::table('participants', function (Blueprint $table) {
                $table->string('age')->after('source')->default('16-18');
            });

            DB::table('participants')->update([
                'age' => DB::raw("
                    CASE 
                        WHEN age =  '14-18' THEN '16-18'
                        ELSE 'moins_de_16'
                    END
                ")
            ]);

            Schema::table('participants', function (Blueprint $table) {
                $table->dropColumn('is_over_14');
            });
        }


    /**
     * Reverse the migrations.
     */
        public function down(): void
        {
            Schema::table('participants', function (Blueprint $table) {
                $table->boolean('is_over_14')->default(true)->after('optin');
            });

            DB::table('participants')->update([
                'is_over_14' => DB::raw("
                    CASE 
                        WHEN age = 'moins_de_14' THEN FALSE
                        ELSE TRUE
                    END
                ")
            ]);

            Schema::table('participants', function (Blueprint $table) {
                $table->dropColumn('age');
            });
        }

};