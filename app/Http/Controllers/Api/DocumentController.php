<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Enrollment;
use App\Models\Student;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DocumentController extends Controller
{
    public function __construct(private PdfService $pdfService) {}

    public function certificate(Request $request, Enrollment $enrollment): Response
    {
        $enrollment->load(['student', 'school', 'academicYear']);
        $student = $enrollment->student;

        $pdf = $this->pdfService->generateCertificate($enrollment);

        Document::create([
            'student_id'    => $enrollment->student_id,
            'enrollment_id' => $enrollment->id,
            'document_type' => 'CERTIFICAT_INSCRIPTION',
            'qr_code_data'  => json_encode([
                'type'      => 'CERTIFICAT_INSCRIPTION',
                'matricule' => $student->matricule,
                'nom'       => $student->last_name,
                'prenoms'   => $student->first_name,
                'ecole'     => $enrollment->school->code,
                'annee'     => $enrollment->academicYear->label,
            ], JSON_UNESCAPED_UNICODE),
            'generated_at'  => now(),
            'generated_by'  => $request->user()->id,
        ]);

        $filename = sprintf(
            'certificat_%s_%s.pdf',
            $student->matricule,
            str_replace('-', '_', $enrollment->academicYear->label)
        );

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function studentRecord(Request $request, Student $student): Response
    {
        $student->load(['enrollments.school', 'enrollments.academicYear', 'enrollments.payments']);

        $pdf = $this->pdfService->generateStudentRecord($student);

        Document::create([
            'student_id'    => $student->id,
            'document_type' => 'FICHE_RENSEIGNEMENT',
            'qr_code_data'  => json_encode([
                'type'           => 'FICHE_RENSEIGNEMENT',
                'matricule'      => $student->matricule,
                'nom'            => $student->last_name,
                'prenoms'        => $student->first_name,
                'date_naissance' => $student->date_of_birth?->format('d/m/Y'),
                'lieu_naissance' => $student->place_of_birth,
            ], JSON_UNESCAPED_UNICODE),
            'generated_at'  => now(),
            'generated_by'  => $request->user()->id,
        ]);

        $filename = sprintf('fiche_%s.pdf', $student->matricule);

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
