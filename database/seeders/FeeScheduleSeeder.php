<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\FeeSchedule;
use App\Models\School;
use Illuminate\Database\Seeder;

class FeeScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $schoolsById = School::all()->keyBy('code');

        if (!$currentYear) {
            $this->command->warn('Aucune année académique courante trouvée. Seeder ignoré.');
            return;
        }

        // Tarifs par école, statut et type de frais (montants indicatifs INSFS 2025-2026)
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

        $count = 0;
        foreach ($feeMatrix as $fee) {
            $school = $schoolsById->get($fee['school']);
            if (!$school) {
                continue;
            }

            FeeSchedule::firstOrCreate(
                [
                    'academic_year_id' => $currentYear->id,
                    'school_id'        => $school->id,
                    'student_status'   => $fee['status'],
                    'fee_type'         => $fee['fee_type'],
                ],
                [
                    'total_amount'     => $fee['amount'],
                    'max_installments' => $fee['install'],
                ]
            );
            $count++;
        }

        $this->command->info("$count tarifs créés/vérifiés pour {$currentYear->label}.");
    }
}
