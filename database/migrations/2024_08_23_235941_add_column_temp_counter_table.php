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
            $table->dateTime('scanned_time')->nullable();
        });

        Schema::table('material_setup_mst_supplier', function (Blueprint $table) {
            $table->dateTime('scanned_time')->nullable();
            $table->char('being_used',1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_counters', function (Blueprint $table) {
            $table->dropColumn('scanned_time')->nullable();
        });

        Schema::table('material_setup_mst_supplier', function (Blueprint $table) {
            $table->dropColumn('scanned_time');
            $table->dropColumn('being_used');
        });
    }
};
