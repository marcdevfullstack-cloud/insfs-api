<?php

namespace App\Services;

use App\Models\AdmittedStudent;
use App\Models\School;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AdmittedStudentImportService
{
    private const EXPECTED_HEADERS = [
        'nom', 'prenoms', 'date_naissance', 'email', 'telephone',
        'code_ecole', 'mode_entree', 'annee_cycle',
    ];

    private const VALID_ENTRY_MODES = [
        'Concours direct', 'Analyse de dossier', 'Concours professionnel',
    ];

    public function import(UploadedFile $file, string $academicYearId, string $importedById): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return ['success' => false, 'message' => 'Impossible de lire le fichier.', 'imported' => 0, 'errors' => []];
        }

        // Lire et valider l'en-tête
        $headers = fgetcsv($handle, 0, ',');
        if (!$headers) {
            fclose($handle);
            return ['success' => false, 'message' => 'Fichier CSV vide.', 'imported' => 0, 'errors' => []];
        }

        $headers = array_map(fn($h) => trim(strtolower($h)), $headers);

        foreach (self::EXPECTED_HEADERS as $expected) {
            if (!in_array($expected, $headers)) {
                fclose($handle);
                return [
                    'success' => false,
                    'message' => "Colonne manquante : « {$expected} ». Utilisez le template CSV fourni.",
                    'imported' => 0,
                    'errors'   => [],
                ];
            }
        }

        // Charger les écoles en mémoire
        $schools = School::pluck('id', 'code')->toArray(); // ['EES' => 'uuid', ...]

        $batch    = 'CSV-' . now()->format('Ymd-His') . '-' . Str::random(6);
        $imported = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            $row++;

            // Ignorer les lignes vides
            if (count(array_filter($data)) === 0) {
                continue;
            }

            $mapped = array_combine($headers, array_map('trim', $data));
            $error  = $this->validateRow($mapped, $row, $schools);

            if ($error) {
                $errors[] = $error;
                continue;
            }

            try {
                AdmittedStudent::create([
                    'school_id'        => $schools[strtoupper($mapped['code_ecole'])],
                    'academic_year_id' => $academicYearId,
                    'imported_by'      => $importedById,
                    'last_name'        => strtoupper(trim($mapped['nom'])),
                    'first_name'       => ucwords(strtolower(trim($mapped['prenoms']))),
                    'date_of_birth'    => $mapped['date_naissance'],
                    'email'            => $mapped['email'] ?: null,
                    'phone'            => $mapped['telephone'] ?: null,
                    'entry_mode'       => $mapped['mode_entree'],
                    'year_of_study'    => (int) $mapped['annee_cycle'],
                    'status'           => 'INVITE',
                    'import_batch'     => $batch,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Ligne {$row} : erreur d'enregistrement — " . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'success'      => $imported > 0,
            'message'      => "{$imported} admis importé(s) avec succès.",
            'imported'     => $imported,
            'errors_count' => count($errors),
            'errors'       => $errors,
            'batch'        => $batch,
        ];
    }

    private function validateRow(array $row, int $rowNum, array $schools): ?string
    {
        if (empty($row['nom'])) {
            return "Ligne {$rowNum} : le nom est obligatoire.";
        }

        if (empty($row['prenoms'])) {
            return "Ligne {$rowNum} : les prénoms sont obligatoires.";
        }

        if (empty($row['date_naissance']) || !strtotime($row['date_naissance'])) {
            return "Ligne {$rowNum} : date de naissance invalide (format attendu : AAAA-MM-JJ).";
        }

        if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            return "Ligne {$rowNum} : email invalide.";
        }

        $codeEcole = strtoupper($row['code_ecole'] ?? '');
        if (!isset($schools[$codeEcole])) {
            return "Ligne {$rowNum} : code école « {$row['code_ecole']} » inconnu. Codes valides : " . implode(', ', array_keys($schools)) . '.';
        }

        if (!in_array($row['mode_entree'], self::VALID_ENTRY_MODES)) {
            return "Ligne {$rowNum} : mode d'entrée « {$row['mode_entree']} » invalide. Valeurs acceptées : " . implode(', ', self::VALID_ENTRY_MODES) . '.';
        }

        $year = (int) ($row['annee_cycle'] ?? 0);
        if ($year < 1 || $year > 3) {
            return "Ligne {$rowNum} : l'année de cycle doit être 1, 2 ou 3.";
        }

        return null;
    }
}
