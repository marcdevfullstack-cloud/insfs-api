<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function storeAcademicYear(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $validated = $request->validate([
            'label'      => 'required|string|max:20|unique:academic_years,label',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        if (!empty($validated['is_current'])) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        $year = AcademicYear::create($validated);

        return response()->json($year, 201);
    }

    public function setCurrentAcademicYear(Request $request, AcademicYear $academicYear): JsonResponse
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        AcademicYear::where('is_current', true)->update(['is_current' => false]);
        $academicYear->update(['is_current' => true]);

        return response()->json($academicYear);
    }
}
