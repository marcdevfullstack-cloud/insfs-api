<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained('enrollment_applications')->cascadeOnDelete();
            $table->enum('document_type', [
                'PHOTO',
                'CNI_PASSEPORT',
                'EXTRAIT_NAISSANCE',
                'DIPLOME_BEPC',
                'DIPLOME_BAC',
                'CERTIFICAT_TRAVAIL',
                'ATTESTATION_BOURSE',
                'AUTRE_DIPLOME',
                'AUTRE',
            ]);
            $table->string('file_path', 500);
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->enum('status', ['SOUMIS', 'VALIDE', 'REJETE'])->default('SOUMIS');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_documents');
    }
};
