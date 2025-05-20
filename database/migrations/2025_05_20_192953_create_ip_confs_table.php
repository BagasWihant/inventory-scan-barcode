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
        Schema::create('ip_conf', function (Blueprint $table) {
            $table->id();
            $table->string('nik',32);
            $table->string('jabatan',100)->index();
            $table->string('ip',20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_conf');
    }
};
