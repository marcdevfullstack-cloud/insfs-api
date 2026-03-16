<?php

namespace Database\Seeders;

use App\Models\AdmittedStudent;
use App\Models\AcademicYear;
use App\Models\EnrollmentApplication;
use App\Models\PortalUser;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class PortalTestSeeder extends Seeder
{
    public function run(): void
    {
        // ── Nettoyage des anciens enregistrements test ──────────────────────
        $this->command->info('🧹 Nettoyage des anciens comptes test...');

        $oldUser = PortalUser::where('email', 'etudiant@insfs.ci')->first();
        if ($oldUser) {
            EnrollmentApplication::where('portal_user_id', $oldUser->id)->forceDelete();
            $oldUser->tokens()->delete();
            $oldUser->forceDelete();
            $this->command->info('  ✓ Ancien PortalUser supprimé');
        }

        // Supprimer les anciens admis test du batch TEST-2025
        $oldAdmitted = AdmittedStudent::where('import_batch', 'TEST-2025')->get();
        foreach ($oldAdmitted as $old) {
            EnrollmentApplication::where('admitted_student_id', $old->id)->forceDelete();
            $old->forceDelete();
        }
        if ($oldAdmitted->count() > 0) {
            $this->command->info('  ✓ Anciens admis test supprimés ('.$oldAdmitted->count().')');
        }

        // ── Données de référence ────────────────────────────────────────────
        $year   = AcademicYear::where('is_current', true)->firstOrFail();
        $school = School::where('code', 'EES')->firstOrFail();
        $admin  = User::where('role', 'ADMIN')->firstOrFail();

        // ── Admis test 1 — KOUAME Jean-Baptiste (non encore inscrit) ────────
        $admitted1 = AdmittedStudent::create([
            'last_name'        => 'KOUAME',
            'first_name'       => 'Jean-Baptiste',
            'date_of_birth'    => '2000-01-15',
            'school_id'        => $school->id,
            'academic_year_id' => $year->id,
            'entry_mode'       => 'Concours direct',
            'year_of_study'    => 1,
            'imported_by'      => $admin->id,
            'import_batch'     => 'TEST-2025',
            'status'           => 'INVITE',
        ]);

        // ── Admis test 2 — BAMBA Aminata (compte portail déjà créé) ─────────
        $admitted2 = AdmittedStudent::create([
            'last_name'        => 'BAMBA',
            'first_name'       => 'Aminata',
            'date_of_birth'    => '1999-07-22',
            'school_id'        => $school->id,
            'academic_year_id' => $year->id,
            'entry_mode'       => 'Concours direct',
            'year_of_study'    => 1,
            'imported_by'      => $admin->id,
            'import_batch'     => 'TEST-2025',
            'status'           => 'INSCRIT',
        ]);

        // ── Compte portail pour BAMBA Aminata (mot de passe hashé via cast) ─
        $portalUser = PortalUser::create([
            'admitted_student_id' => $admitted2->id,
            'last_name'           => 'BAMBA',
            'first_name'          => 'Aminata',
            'date_of_birth'       => '1999-07-22',
            'email'               => 'etudiant@insfs.ci',
            'phone'               => '0700000000',
            'password'            => 'insfs2026', // ✅ cast 'hashed' s'occupe du bcrypt
        ]);

        // ── Dossier d'inscription (BROUILLON) ────────────────────────────────
        EnrollmentApplication::create([
            'portal_user_id'      => $portalUser->id,
            'admitted_student_id' => $admitted2->id,
            'academic_year_id'    => $year->id,
            'status'              => 'BROUILLON',
            'last_name'           => 'BAMBA',
            'first_name'          => 'Aminata',
            'nationality'         => 'Ivoirienne',
        ]);

        // ── Résumé ───────────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✓ Admis test créés : KOUAME Jean-Baptiste + BAMBA Aminata');
        $this->command->info('✓ Compte portail   : etudiant@insfs.ci / insfs2026');
        $this->command->info('');
        $this->command->info('Test éligibilité :');
        $this->command->info('  Nom      : KOUAME');
        $this->command->info('  Prénom   : Jean-Baptiste');
        $this->command->info('  Naissance: 2000-01-15');
    }
}