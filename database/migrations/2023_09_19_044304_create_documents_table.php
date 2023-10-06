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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->date('tgl_surat');
            $table->foreignId('jenis_id')->constrained('jenis')->cascadeOnDelete();
            $table->text('body');
            $table->foreignId('pengirim_diajukan_oleh')->constrained('karyawan')->cascadeOnDelete();
            $table->tinyInteger('status_pengirim_diajukan')->default('0');
            $table->foreignId('pengirim_disetujui_oleh')->constrained('karyawan')->cascadeOnDelete();
            $table->tinyInteger('status_pengirim_disetujui')->default('0');
            $table->foreignId('created_by')->constrained('karyawan')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
