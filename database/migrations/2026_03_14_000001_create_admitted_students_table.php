<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admitted_students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('imported_by')->constrained('users')->restrictOnDelete();
            $table->string('last_name');
            $table->string('first_name');
            $table->date('date_of_birth');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('entry_mode', ['Concours direct', 'Analyse de dossier', 'Concours professionnel']);
            $table->tinyInteger('year_of_study')->default(1);
            $table->enum('status', ['INVITE', 'INSCRIT', 'SOUMIS', 'VALIDE'])->default('INVITE');
            $table->string('import_batch', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admitted_students');
    }
};
