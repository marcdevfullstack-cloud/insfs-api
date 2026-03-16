<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained('enrollment_applications')->cascadeOnDelete();
            $table->enum('signature_type', [
                'ETUDIANT_RENSEIGNEMENTS',
                'ETUDIANT_ENGAGEMENT',
                'SCOLARITE_INSCRIPTION',
                'CHEF_CERTIFICAT',
            ]);
            $table->longText('signature_image'); // base64 PNG data URI
            $table->string('signer_name');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();

            $table->unique(['application_id', 'signature_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_signatures');
    }
};