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
        Schema::table('temp_counters', function (Blueprint $table) {
            $table->char('flag',1)->comment('0=palet,1=po')->default(0);
        });
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->char('kit_no',50)->nullable()->after('material_no');
        });
        Schema::table('abnormal_materials', function (Blueprint $table) {
            $table->char('kit_no',50)->nullable()->after('material_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('temp_counters', function (Blueprint $table) {
            $table->dropColumn('flag');
        });
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->dropColumn('kit_no');
        });
        Schema::table('abnormal_materials', function (Blueprint $table) {
            $table->dropColumn('kit_no');
        });
    }
};
