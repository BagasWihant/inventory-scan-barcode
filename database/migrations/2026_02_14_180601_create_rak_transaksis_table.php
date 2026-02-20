<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rak_transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rak_id');
            $table->unsignedBigInteger('material_id');
            $table->integer('qty');
            $table->integer('user_id')->default(0);

            // misalkan dihapus, di db ini masih datane
            $table->tinyInteger('dihapus')->default(0);

            // i = in, o = out, e = edit, parameter lain nyusul
            $table->string('stat', 1)->comment('i/o');

            // tabel bantu untuk edit, misal untuk simpan old value siapa tau kedepan dibutuhkan
            $table->string('params', 255)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rak_transaksi');
    }
};
