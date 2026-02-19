<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\FeeSchedule;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['enrollment.student', 'recorder'])
            ->orderBy('payment_date', 'desc');

        if ($request->filled('enrollment_id')) {
            $query->where('enrollment_id', $request->enrollment_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!in_array($user->role, ['ADMIN', 'COMPTABILITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $validated = $request->validate([
            'enrollment_id'      => 'required|uuid|exists:enrollments,id',
            'payment_type'       => 'required|in:FRAIS_INSCRIPTION,FRAIS_SCOLARITE',
            'amount'             => 'required|numeric|min:1',
            'payment_date'       => 'required|date',
            'receipt_number'     => 'required|string|max:50',
            'installment_number' => 'required|integer|min:1',
            'notes'              => 'nullable|string|max:500',
        ]);

        $payment = Payment::create([
            ...$validated,
            'payment_method' => 'ESPÈCES',
            'recorded_by'    => $user->id,
        ]);

        // Auto-validation : si total payé >= total dû, marquer l'inscription VALIDE
        $enrollment = Enrollment::with('student')->find($validated['enrollment_id']);
        $totalPaid  = (float) $enrollment->payments()->sum('amount');

        $totalOwed = (float) FeeSchedule::where('academic_year_id', $enrollment->academic_year_id)
            ->where('school_id', $enrollment->school_id)
            ->where('student_status', $enrollment->student?->status_type)
            ->sum('total_amount');

        if ($totalOwed > 0 && $totalPaid >= $totalOwed && $enrollment->status === 'EN_COURS') {
            $enrollment->update(['status' => 'VALIDE']);
        }

        return response()->json($payment->load(['enrollment.student', 'recorder']), 201);
    }

    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $payment->delete();

        return response()->json(null, 204);
    }
}
