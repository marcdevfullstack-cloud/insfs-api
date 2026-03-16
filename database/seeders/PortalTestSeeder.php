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

        $oldUsers = PortalUser::whereIn('email', [
            'etudiant@insfs.ci',
            'marcdevfullstack@gmail.com',
        ])->get();

        foreach ($oldUsers as $u) {
            EnrollmentApplication::where('portal_user_id', $u->id)->forceDelete();
            $u->tokens()->delete();
            $u->forceDelete();
        }
        if ($oldUsers->count() > 0) {
            $this->command->info('  ✓ Anciens PortalUsers supprimés ('.$oldUsers->count().')');
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
        AdmittedStudent::create([
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

        // ── Admis test 2 — BAMBA Aminata → compte marcdevfullstack@gmail.com ─
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

        $portalUser1 = PortalUser::create([
            'admitted_student_id' => $admitted2->id,
            'last_name'           => 'BAMBA',
            'first_name'          => 'Aminata',
            'date_of_birth'       => '1999-07-22',
            'email'               => 'marcdevfullstack@gmail.com',
            'phone'               => '0700000000',
            'password'            => 'insfs2026',
        ]);

        EnrollmentApplication::create([
            'portal_user_id'      => $portalUser1->id,
            'admitted_student_id' => $admitted2->id,
            'academic_year_id'    => $year->id,
            'status'              => 'BROUILLON',
            'last_name'           => 'BAMBA',
            'first_name'          => 'Aminata',
            'nationality'         => 'Ivoirienne',
        ]);

        // ── Admis test 3 — TRAORE Moussa → compte etudiant@insfs.ci ─────────
        $admitted3 = AdmittedStudent::create([
            'last_name'        => 'TRAORE',
            'first_name'       => 'Moussa',
            'date_of_birth'    => '2001-03-10',
            'school_id'        => $school->id,
            'academic_year_id' => $year->id,
            'entry_mode'       => 'Concours direct',
            'year_of_study'    => 1,
            'imported_by'      => $admin->id,
            'import_batch'     => 'TEST-2025',
            'status'           => 'INSCRIT',
        ]);

        $portalUser2 = PortalUser::create([
            'admitted_student_id' => $admitted3->id,
            'last_name'           => 'TRAORE',
            'first_name'          => 'Moussa',
            'date_of_birth'       => '2001-03-10',
            'email'               => 'etudiant@insfs.ci',
            'phone'               => '0700000001',
            'password'            => 'insfs2026',
        ]);

        EnrollmentApplication::create([
            'portal_user_id'      => $portalUser2->id,
            'admitted_student_id' => $admitted3->id,
            'academic_year_id'    => $year->id,
            'status'              => 'BROUILLON',
            'last_name'           => 'TRAORE',
            'first_name'          => 'Moussa',
            'nationality'         => 'Ivoirienne',
        ]);

        // ── Résumé ───────────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✓ Admis test créés : KOUAME Jean-Baptiste + BAMBA Aminata + TRAORE Moussa');
        $this->command->info('');
        $this->command->info('✓ Compte test 1 : marcdevfullstack@gmail.com / insfs2026');
        $this->command->info('✓ Compte test 2 : etudiant@insfs.ci / insfs2026');
        $this->command->info('');
        $this->command->info('Test éligibilité :');
        $this->command->info('  Nom      : KOUAME');
        $this->command->info('  Prénom   : Jean-Baptiste');
        $this->command->info('  Naissance: 2000-01-15');
    }
}