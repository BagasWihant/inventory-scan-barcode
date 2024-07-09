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
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->char('surat_jalan',50)->nullable()->after('material_no');
        });
        Schema::table('abnormal_materials', function (Blueprint $table) {
            $table->char('surat_jalan',50)->nullable()->after('material_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->dropColumn('surat_jalan');
        });
        Schema::table('abnormal_materials', function (Blueprint $table) {
            $table->dropColumn('surat_jalan');
        });
    }
};
