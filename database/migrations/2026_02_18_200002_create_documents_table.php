<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('enrollment_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('document_type', ['CERTIFICAT_INSCRIPTION', 'FICHE_RENSEIGNEMENT']);
            $table->string('file_path', 500)->nullable();
            $table->text('qr_code_data')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->foreignUuid('generated_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
