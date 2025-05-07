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
        Schema::create('retur_qa', function (Blueprint $table) {
            $table->id();
            $table->string('no_retur',100)->index();
            $table->string('material_no');
            $table->string('material_name');
            $table->integer('qty');
            $table->string('surat_jalan',100);
            $table->string('line_c',50);
            $table->date('issue_date');
            $table->char('status',1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_qa');
    }
};
