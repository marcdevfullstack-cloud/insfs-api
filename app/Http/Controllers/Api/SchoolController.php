<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;

class SchoolController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(School::where('is_active', true)->orderBy('code')->get());
    }

    public function academicYears(): JsonResponse
    {
        return response()->json(AcademicYear::orderBy('label', 'desc')->get());
    }

    public function currentAcademicYear(): JsonResponse
    {
        $year = AcademicYear::where('is_current', true)->first();

        if (!$year) {
            return response()->json(['message' => 'Aucune année académique en cours.'], 404);
        }

        return response()->json($year);
    }
}
