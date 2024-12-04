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
        Schema::table('material_setup_mst_supplier', function (Blueprint $table) {
            $table->char('being_used',11)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_setup_mst_supplier', function (Blueprint $table) {
            $table->char('being_used',1)->nullable()->change();

        });
    }
};
