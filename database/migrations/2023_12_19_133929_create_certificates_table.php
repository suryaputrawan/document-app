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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('certificate_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('employee_name');
            $table->foreignId('certificate_type_id')->constrained('certificate_types')->restrictOnDelete();
            $table->text('file')->nullable();
            $table->foreignId('hospital_id')->constrained('hospitals')->restrictOnDelete();
            $table->foreignId('user_created')->constrained('users')->restrictOnDelete();
            $table->tinyInteger('isNotif')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
