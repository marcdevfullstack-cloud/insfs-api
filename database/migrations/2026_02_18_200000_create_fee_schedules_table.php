<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('school_id')->constrained()->cascadeOnDelete();
            $table->enum('student_status', ['Fonctionnaire', 'Boursier national', 'Boursier étranger', 'Non-boursier']);
            $table->enum('fee_type', ['FRAIS_INSCRIPTION', 'FRAIS_SCOLARITE']);
            $table->decimal('total_amount', 10, 2);
            $table->integer('max_installments')->default(3);
            $table->timestamps();

            $table->unique(['academic_year_id', 'school_id', 'student_status', 'fee_type'], 'fee_schedule_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_schedules');
    }
};
