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
            $table->char('setup_by',50)->nullable();
            $table->char('line_c',50)->nullable();
        });
        Schema::table('abnormal_materials', function (Blueprint $table) {
            $table->char('setup_by',50)->nullable();
            $table->char('line_c',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->dropColumn('setup_by');
            $table->dropColumn('line_c');
        });
        Schema::table('abnormal_materials', function (Blueprint $table) {
            $table->dropColumn('setup_by');
            $table->dropColumn('line_c');
        });


    }
};
