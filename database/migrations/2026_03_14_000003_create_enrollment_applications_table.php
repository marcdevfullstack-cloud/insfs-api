<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('portal_user_id')->constrained('portal_users')->cascadeOnDelete();
            $table->foreignUuid('admitted_student_id')->constrained('admitted_students')->cascadeOnDelete();
            $table->foreignUuid('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignUuid('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignUuid('validated_by')->nullable()->constrained('users')->nullOnDelete();

            // Workflow
            $table->enum('status', [
                'BROUILLON',
                'SOUMIS',
                'EN_TRAITEMENT',
                'CORRECTION_DEMANDEE',
                'VALIDE',
                'REJETE',
                'COMPLET',
            ])->default('BROUILLON');
            $table->text('rejection_reason')->nullable();
            $table->json('correction_fields')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Section I - Identification
            $table->string('last_name');
            $table->string('first_name');
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('nationality', 100)->default('Ivoirienne');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->enum('marital_status', ['Célibataire', 'Marié(e)', 'Veuf(ve)', 'Divorcé(e)'])->nullable();
            $table->integer('children_count')->default(0);
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('photo_url', 500)->nullable();

            // Statut
            $table->enum('status_type', [
                'Fonctionnaire',
                'Boursier national',
                'Boursier étranger',
                'Non-boursier',
            ])->nullable();
            $table->string('matricule_fonctionnaire', 50)->nullable();
            $table->string('emploi', 100)->nullable();
            $table->string('echelon', 50)->nullable();
            $table->string('categorie', 50)->nullable();
            $table->string('classe', 50)->nullable();

            // Section II - Diplômes
            $table->boolean('diploma_cepe')->default(false);
            $table->boolean('diploma_bepc')->default(false);
            $table->boolean('diploma_bac')->default(false);
            $table->string('diploma_bac_serie', 10)->nullable();
            $table->text('other_diplomas')->nullable();

            // Section III - Entrée INSFS (date confirmée par l'étudiant)
            $table->date('entry_date')->nullable();

            // Section IV - Adresse
            $table->string('address_quarter')->nullable();
            $table->string('address_apartment', 50)->nullable();
            $table->string('address_phone', 20)->nullable();
            $table->string('postal_box', 50)->nullable();
            $table->text('vacation_address')->nullable();
            $table->string('tutor_name')->nullable();
            $table->string('tutor_address')->nullable();
            $table->string('tutor_phone', 20)->nullable();

            // Section V - Santé
            $table->boolean('has_health_issues')->default(false);
            $table->string('health_condition')->nullable();
            $table->text('doctor_info')->nullable();

            // Engagement
            $table->boolean('engagement_signed')->default(false);
            $table->timestamp('engagement_signed_at')->nullable();

            // Documents PDF générés
            $table->string('fiche_identification_path', 500)->nullable();
            $table->string('fiche_inscription_path', 500)->nullable();
            $table->string('certificat_inscription_path', 500)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_applications');
    }
};
