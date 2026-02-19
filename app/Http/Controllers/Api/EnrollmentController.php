<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnrollmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::with(['student', 'school', 'academicYear']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('enrollment_date', 'desc')->paginate(15);

        return response()->json($enrollments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|uuid|exists:students,id',
            'school_id' => 'required|uuid|exists:schools,id',
            'academic_year_id' => 'required|uuid|exists:academic_years,id',
            'year_of_study' => 'required|integer|min:1|max:3',
            'cycle' => 'nullable|string|max:50',
            'quality' => 'required|in:CD,CP,FC',
            'enrollment_date' => 'required|date',
        ]);

        $exists = Enrollment::where('student_id', $validated['student_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('school_id', $validated['school_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Cet étudiant est déjà inscrit dans cette école pour cette année académique.',
            ], 422);
        }

        $enrollment = Enrollment::create($validated);
        $enrollment->load(['student', 'school', 'academicYear']);

        return response()->json($enrollment, 201);
    }

    public function show(Enrollment $enrollment): JsonResponse
    {
        $enrollment->load(['student', 'school', 'academicYear', 'payments.recorder']);

        return response()->json(array_merge($enrollment->toArray(), [
            'total_paid' => $enrollment->total_paid,
        ]));
    }

    public function update(Request $request, Enrollment $enrollment): JsonResponse
    {
        $validated = $request->validate([
            'year_of_study' => 'sometimes|integer|min:1|max:3',
            'cycle' => 'nullable|string|max:50',
            'quality' => 'sometimes|in:CD,CP,FC',
            'enrollment_date' => 'sometimes|date',
            'status' => 'sometimes|in:EN_COURS,VALIDE,ANNULE',
        ]);

        $enrollment->update($validated);

        return response()->json($enrollment->fresh(['student', 'school', 'academicYear']));
    }
}
