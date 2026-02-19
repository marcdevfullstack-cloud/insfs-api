<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\FeeSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Utilisateurs de test ---
        User::create([
            'email' => 'admin@insfs.ci',
            'password' => Hash::make('insfs2026'),
            'full_name' => 'Administrateur INSFS',
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        User::create([
            'email' => 'scolarite@insfs.ci',
            'password' => Hash::make('insfs2026'),
            'full_name' => 'Service Scolarité',
            'role' => 'SCOLARITE',
            'is_active' => true,
        ]);

        User::create([
            'email' => 'comptabilite@insfs.ci',
            'password' => Hash::make('insfs2026'),
            'full_name' => 'Service Comptabilité',
            'role' => 'COMPTABILITE',
            'is_active' => true,
        ]);

        // --- Établissements INSFS ---
        $schools = [
            [
                'code' => 'EES',
                'name' => 'École des Éducateurs Spécialisés',
                'description' => 'Formation des éducateurs spécialisés en travail social',
                'is_active' => true,
            ],
            [
                'code' => 'EEP',
                'name' => 'École des Éducateurs Préscolaires',
                'description' => 'Formation des éducateurs en milieu préscolaire',
                'is_active' => true,
            ],
            [
                'code' => 'EAS',
                'name' => 'École des Assistants Sociaux',
                'description' => 'Formation des assistants sociaux',
                'is_active' => true,
            ],
            [
                'code' => 'CPPE',
                'name' => 'CPPE-PILOTE',
                'description' => 'Centre Pilote de Protection de l\'Enfance',
                'is_active' => true,
            ],
        ];

        foreach ($schools as $school) {
            School::create($school);
        }

        // --- Années académiques ---
        AcademicYear::create([
            'label' => '2024-2025',
            'start_date' => '2024-10-01',
            'end_date' => '2025-07-31',
            'is_current' => false,
        ]);

        AcademicYear::create([
            'label' => '2025-2026',
            'start_date' => '2025-10-01',
            'end_date' => '2026-07-31',
            'is_current' => true,
        ]);

        // --- Grille tarifaire 2025-2026 ---
        $currentYear = AcademicYear::where('is_current', true)->first();
        $schoolsById = School::all()->keyBy('code');

        // Tarifs par école, statut et type de frais
        // (montants indicatifs — à valider avec l'INSFS)
        $feeMatrix = [
            // EES
            ['school' => 'EES', 'status' => 'Fonctionnaire',      'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EES', 'status' => 'Fonctionnaire',      'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 150000, 'install' => 3],
            ['school' => 'EES', 'status' => 'Boursier national',  'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EES', 'status' => 'Boursier national',  'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 100000, 'install' => 2],
            ['school' => 'EES', 'status' => 'Boursier étranger',  'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EES', 'status' => 'Boursier étranger',  'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 150000, 'install' => 3],
            ['school' => 'EES', 'status' => 'Non-boursier',       'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EES', 'status' => 'Non-boursier',       'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 200000, 'install' => 3],
            // EEP
            ['school' => 'EEP', 'status' => 'Fonctionnaire',      'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EEP', 'status' => 'Fonctionnaire',      'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 150000, 'install' => 3],
            ['school' => 'EEP', 'status' => 'Boursier national',  'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EEP', 'status' => 'Boursier national',  'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 100000, 'install' => 2],
            ['school' => 'EEP', 'status' => 'Boursier étranger',  'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EEP', 'status' => 'Boursier étranger',  'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 150000, 'install' => 3],
            ['school' => 'EEP', 'status' => 'Non-boursier',       'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EEP', 'status' => 'Non-boursier',       'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 200000, 'install' => 3],
            // EAS
            ['school' => 'EAS', 'status' => 'Fonctionnaire',      'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EAS', 'status' => 'Fonctionnaire',      'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 150000, 'install' => 3],
            ['school' => 'EAS', 'status' => 'Boursier national',  'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EAS', 'status' => 'Boursier national',  'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 100000, 'install' => 2],
            ['school' => 'EAS', 'status' => 'Boursier étranger',  'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EAS', 'status' => 'Boursier étranger',  'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 150000, 'install' => 3],
            ['school' => 'EAS', 'status' => 'Non-boursier',       'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'EAS', 'status' => 'Non-boursier',       'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 200000, 'install' => 3],
            // CPPE
            ['school' => 'CPPE', 'status' => 'Fonctionnaire',     'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'CPPE', 'status' => 'Fonctionnaire',     'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 150000, 'install' => 3],
            ['school' => 'CPPE', 'status' => 'Boursier national', 'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'CPPE', 'status' => 'Boursier national', 'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 100000, 'install' => 2],
            ['school' => 'CPPE', 'status' => 'Boursier étranger', 'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'CPPE', 'status' => 'Boursier étranger', 'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 150000, 'install' => 3],
            ['school' => 'CPPE', 'status' => 'Non-boursier',      'fee_type' => 'FRAIS_INSCRIPTION', 'amount' => 30000,  'install' => 1],
            ['school' => 'CPPE', 'status' => 'Non-boursier',      'fee_type' => 'FRAIS_SCOLARITE',   'amount' => 200000, 'install' => 3],
        ];

        foreach ($feeMatrix as $fee) {
            $school = $schoolsById->get($fee['school']);
            if (!$school || !$currentYear) {
                continue;
            }
            FeeSchedule::create([
                'academic_year_id' => $currentYear->id,
                'school_id'        => $school->id,
                'student_status'   => $fee['status'],
                'fee_type'         => $fee['fee_type'],
                'total_amount'     => $fee['amount'],
                'max_installments' => $fee['install'],
            ]);
        }
    }
}