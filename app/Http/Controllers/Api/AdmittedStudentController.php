<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdmittedStudent;
use App\Services\AdmittedStudentImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AdmittedStudentController extends Controller
{
    public function __construct(private AdmittedStudentImportService $importService) {}

    /**
     * Liste des admis avec filtres.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->user()->role === 'COMPTABILITE') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $query = AdmittedStudent::with(['school', 'academicYear', 'portalUser']);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('last_name', 'like', "%{$term}%")
                  ->orWhere('first_name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }

        $admitted = $query->orderBy('last_name')->paginate($request->get('per_page', 20));

        return response()->json($admitted);
    }

    /**
     * Ajouter un admis manuellement.
     */
    public function store(Request $request): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $validated = $request->validate([
            'school_id'        => 'required|uuid|exists:schools,id',
            'academic_year_id' => 'required|uuid|exists:academic_years,id',
            'last_name'        => 'required|string|max:255',
            'first_name'       => 'required|string|max:255',
            'date_of_birth'    => 'required|date|before:today',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'entry_mode'       => 'required|in:Concours direct,Analyse de dossier,Concours professionnel',
            'year_of_study'    => 'required|integer|min:1|max:3',
        ]);

        $validated['imported_by']   = $request->user()->id;
        $validated['import_batch']  = 'MANUEL-' . now()->format('Ymd-His');

        $admitted = AdmittedStudent::create($validated);

        return response()->json($admitted->load(['school', 'academicYear']), 201);
    }

    /**
     * Import CSV d'admis.
     */
    public function import(Request $request): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $request->validate([
            'file'             => 'required|file|mimes:csv,txt|max:5120',
            'academic_year_id' => 'required|uuid|exists:academic_years,id',
        ]);

        $result = $this->importService->import(
            $request->file('file'),
            $request->academic_year_id,
            $request->user()->id
        );

        return response()->json($result, 200);
    }

    /**
     * Télécharger le template CSV.
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\Response
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-admis.csv"',
        ];

        $rows = [
            ['nom', 'prenoms', 'date_naissance', 'email', 'telephone', 'code_ecole', 'mode_entree', 'annee_cycle'],
            ['KOUAME', 'Jean-Baptiste', '2000-05-15', 'jean@email.com', '0700000000', 'EES', 'Concours direct', '1'],
            ['DIALLO', 'Aminata', '2001-03-22', 'aminata@email.com', '0709090909', 'EEP', 'Analyse de dossier', '1'],
        ];

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . $v . '"', $row)) . "\r\n";
        }

        return Response::make($csv, 200, $headers);
    }

    /**
     * Supprimer un admis (seulement s'il n'a pas encore créé de compte).
     */
    public function destroy(Request $request, AdmittedStudent $admittedStudent): JsonResponse
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if ($admittedStudent->portalUser()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer : un compte portail est déjà créé pour cet admis.'], 422);
        }

        $admittedStudent->delete();

        return response()->json(null, 204);
    }
}
