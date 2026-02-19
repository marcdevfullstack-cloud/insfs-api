<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FeeSchedule::with(['academicYear', 'school'])
            ->orderBy('school_id')
            ->orderBy('student_status')
            ->orderBy('fee_type');

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|uuid|exists:academic_years,id',
            'school_id'        => 'required|uuid|exists:schools,id',
            'student_status'   => 'required|in:Fonctionnaire,Boursier national,Boursier étranger,Non-boursier',
            'fee_type'         => 'required|in:FRAIS_INSCRIPTION,FRAIS_SCOLARITE',
            'total_amount'     => 'required|numeric|min:0',
            'max_installments' => 'required|integer|min:1|max:12',
        ]);

        $feeSchedule = FeeSchedule::create($validated);

        return response()->json($feeSchedule->load(['academicYear', 'school']), 201);
    }

    public function update(Request $request, FeeSchedule $feeSchedule): JsonResponse
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $validated = $request->validate([
            'total_amount'     => 'sometimes|numeric|min:0',
            'max_installments' => 'sometimes|integer|min:1|max:12',
        ]);

        $feeSchedule->update($validated);

        return response()->json($feeSchedule->load(['academicYear', 'school']));
    }
}
