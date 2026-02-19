<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('school_id');
            $table->uuid('academic_year_id');
            $table->integer('year_of_study')->comment('1, 2 ou 3');
            $table->string('cycle', 50)->nullable();
            $table->enum('quality', ['CD', 'CP', 'FC'])->comment('Concours Direct / Concours Professionnel / Formation Continue');
            $table->date('enrollment_date');
            $table->enum('status', ['EN_COURS', 'VALIDE', 'ANNULE'])->default('EN_COURS');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('restrict');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('restrict');

            $table->unique(['student_id', 'academic_year_id', 'school_id'], 'unique_enrollment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
