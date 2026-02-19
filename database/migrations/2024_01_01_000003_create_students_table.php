<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('matricule', 20)->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->enum('gender', ['M', 'F']);
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->string('nationality', 100)->default('Ivoirienne');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->enum('marital_status', ['Célibataire', 'Marié(e)', 'Veuf(ve)', 'Divorcé(e)'])->default('Célibataire');
            $table->integer('children_count')->default(0);
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->enum('status_type', ['Fonctionnaire', 'Boursier national', 'Boursier étranger', 'Non-boursier'])->default('Non-boursier');
            $table->string('matricule_fonctionnaire', 50)->nullable();
            $table->string('emploi', 100)->nullable();
            $table->string('echelon', 50)->nullable();
            $table->string('categorie', 50)->nullable();
            $table->string('classe', 50)->nullable();
            $table->enum('entry_mode', ['Concours direct', 'Analyse de dossier', 'Concours professionnel']);
            $table->boolean('diploma_cepe')->default(false);
            $table->boolean('diploma_bepc')->default(false);
            $table->boolean('diploma_bac')->default(false);
            $table->string('diploma_bac_serie', 10)->nullable();
            $table->text('other_diplomas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
