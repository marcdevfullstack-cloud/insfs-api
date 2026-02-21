<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\MatriculeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function __construct(private MatriculeService $matriculeService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['enrollments.school', 'enrollments.academicYear']);

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        if ($request->filled('school_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('school_id', $request->school_id);
            });
        }

        if ($request->filled('school_code')) {
            $query->whereHas('enrollments.school', function ($q) use ($request) {
                $q->where('code', $request->school_code);
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        $students = $query->orderBy('last_name')->paginate($request->get('per_page', 15));

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'gender' => 'required|in:M,F',
            'date_of_birth' => 'required|date|before:today',
            'place_of_birth' => 'required|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:Célibataire,Marié(e),Veuf(ve),Divorcé(e)',
            'children_count' => 'nullable|integer|min:0',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status_type' => 'required|in:Fonctionnaire,Boursier national,Boursier étranger,Non-boursier',
            'matricule_fonctionnaire' => 'nullable|string|max:50',
            'emploi' => 'nullable|string|max:100',
            'echelon' => 'nullable|string|max:50',
            'categorie' => 'nullable|string|max:50',
            'classe' => 'nullable|string|max:50',
            'entry_mode' => 'required|in:Concours direct,Analyse de dossier,Concours professionnel',
            'diploma_cepe' => 'nullable|boolean',
            'diploma_bepc' => 'nullable|boolean',
            'diploma_bac' => 'nullable|boolean',
            'diploma_bac_serie' => 'nullable|string|max:10',
            'other_diplomas' => 'nullable|string',
        ]);

        $validated['matricule'] = $this->matriculeService->generate();

        $student = Student::create($validated);

        return response()->json($student, 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['enrollments.school', 'enrollments.academicYear', 'enrollments.payments']);

        return response()->json($student);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'last_name' => 'sometimes|string|max:255',
            'first_name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:M,F',
            'date_of_birth' => 'sometimes|date|before:today',
            'place_of_birth' => 'sometimes|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:Célibataire,Marié(e),Veuf(ve),Divorcé(e)',
            'children_count' => 'nullable|integer|min:0',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status_type' => 'sometimes|in:Fonctionnaire,Boursier national,Boursier étranger,Non-boursier',
            'matricule_fonctionnaire' => 'nullable|string|max:50',
            'emploi' => 'nullable|string|max:100',
            'echelon' => 'nullable|string|max:50',
            'categorie' => 'nullable|string|max:50',
            'classe' => 'nullable|string|max:50',
            'entry_mode' => 'sometimes|in:Concours direct,Analyse de dossier,Concours professionnel',
            'diploma_cepe' => 'nullable|boolean',
            'diploma_bepc' => 'nullable|boolean',
            'diploma_bac' => 'nullable|boolean',
            'diploma_bac_serie' => 'nullable|string|max:10',
            'other_diplomas' => 'nullable|string',
        ]);

        $student->update($validated);

        return response()->json($student->fresh());
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json(null, 204);
    }

    public function block(Request $request, Student $student): JsonResponse
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $validated = $request->validate([
            'block_reason' => 'nullable|string|max:500',
        ]);

        $student->update([
            'is_blocked'   => true,
            'block_reason' => $validated['block_reason'] ?? null,
        ]);

        return response()->json($student->fresh());
    }

    public function unblock(Request $request, Student $student): JsonResponse
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $student->update(['is_blocked' => false, 'block_reason' => null]);

        return response()->json($student->fresh());
    }

    public function uploadPhoto(Request $request, Student $student): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($student->photo_url) {
            $oldPath = str_replace('/storage/', '', $student->photo_url);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('photo')->store("students/photos", 'public');
        $student->update(['photo_url' => Storage::url($path)]);

        return response()->json(['photo_url' => $student->photo_url]);
    }
}
