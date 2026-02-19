<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class MatriculeService
{
    public function generate(): string
    {
        return DB::transaction(function () {
            $year = $this->getCurrentYear();

            $lastMatricule = DB::table('students')
                ->where('matricule', 'like', "INSFS-{$year}-%")
                ->lockForUpdate()
                ->orderBy('matricule', 'desc')
                ->value('matricule');

            $sequence = 1;
            if ($lastMatricule) {
                $parts = explode('-', $lastMatricule);
                $sequence = (int) end($parts) + 1;
            }

            return sprintf('INSFS-%s-%04d', $year, $sequence);
        });
    }

    private function getCurrentYear(): string
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        if ($currentYear) {
            return substr($currentYear->label, 0, 4);
        }
        return (string) now()->year;
    }
}
