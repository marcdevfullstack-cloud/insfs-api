<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        // Étudiants
        $totalStudents = Student::count();

        // Inscriptions par statut
        $enrollmentCounts = Enrollment::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalEnrollments    = $enrollmentCounts->sum();
        $enrollmentsEnCours  = (int) ($enrollmentCounts->get('EN_COURS') ?? 0);
        $enrollmentsValide   = (int) ($enrollmentCounts->get('VALIDE') ?? 0);
        $enrollmentsAnnule   = (int) ($enrollmentCounts->get('ANNULE') ?? 0);

        // Distribution des inscriptions par école
        $schools = School::all()->keyBy('id');
        $bySchoolRaw = Enrollment::selectRaw('school_id, count(*) as count')
            ->groupBy('school_id')
            ->get();
        $bySchool = $bySchoolRaw->map(fn($e) => [
            'school_code' => $schools->get($e->school_id)?->code ?? '?',
            'count'        => (int) $e->count,
        ])->values();

        // Top 5 inscriptions EN_COURS les plus récentes (impayés potentiels)
        $topUnpaid = Enrollment::with(['student', 'school', 'academic_year'])
            ->where('status', 'EN_COURS')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'id'            => $e->id,
                'student'       => $e->student ? [
                    'last_name'  => $e->student->last_name,
                    'first_name' => $e->student->first_name,
                    'matricule'  => $e->student->matricule,
                ] : null,
                'school_code'   => $e->school?->code,
                'academic_year' => $e->academic_year?->label,
            ]);

        // Paiements
        $totalCollected = (float) Payment::sum('amount');

        $collectedThisMonth = (float) Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        // Taux de recouvrement basé sur inscriptions validées vs en cours
        $totalActive = $enrollmentsEnCours + $enrollmentsValide;
        $recoveryRate = $totalActive > 0
            ? round(($enrollmentsValide / $totalActive) * 100, 1)
            : 0.0;

        return response()->json([
            'students' => [
                'total' => $totalStudents,
            ],
            'enrollments' => [
                'total'     => $totalEnrollments,
                'en_cours'  => $enrollmentsEnCours,
                'valide'    => $enrollmentsValide,
                'annule'    => $enrollmentsAnnule,
                'by_school' => $bySchool,
            ],
            'payments' => [
                'total_collected'      => $totalCollected,
                'collected_this_month' => $collectedThisMonth,
                'recovery_rate'        => $recoveryRate,
            ],
            'top_unpaid' => $topUnpaid,
        ]);
    }
}
