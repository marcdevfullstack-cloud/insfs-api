<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function __construct(private QrCodeService $qrCodeService) {}

    public function generateCertificate(Enrollment $enrollment): \Barryvdh\DomPDF\PDF
    {
        $enrollment->loadMissing(['student', 'school', 'academicYear']);
        $student = $enrollment->student;

        $qrCode = $this->qrCodeService->generate([
            'type'        => 'CERTIFICAT_INSCRIPTION',
            'matricule'   => $student->matricule,
            'nom'         => $student->last_name,
            'prenoms'     => $student->first_name,
            'ecole'       => $enrollment->school->code,
            'annee'       => $enrollment->academicYear->label,
            'annee_etude' => $enrollment->year_of_study,
        ]);

        $photoBase64 = $this->getPhotoBase64($student);

        $pdf = Pdf::loadView('pdfs.certificate', [
            'enrollment'  => $enrollment,
            'student'     => $student,
            'school'      => $enrollment->school,
            'academicYear' => $enrollment->academicYear,
            'qrCode'      => $qrCode,
            'photo'       => $photoBase64,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    public function generateStudentRecord(Student $student): \Barryvdh\DomPDF\PDF
    {
        $student->loadMissing(['enrollments.school', 'enrollments.academicYear', 'enrollments.payments']);

        $qrCode = $this->qrCodeService->generate([
            'type'           => 'FICHE_RENSEIGNEMENT',
            'matricule'      => $student->matricule,
            'nom'            => $student->last_name,
            'prenoms'        => $student->first_name,
            'date_naissance' => $student->date_of_birth?->format('d/m/Y'),
            'lieu_naissance' => $student->place_of_birth,
        ]);

        $photoBase64 = $this->getPhotoBase64($student);

        $pdf = Pdf::loadView('pdfs.student-record', [
            'student'     => $student,
            'enrollments' => $student->enrollments,
            'qrCode'      => $qrCode,
            'photo'       => $photoBase64,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    public function generateReceipt(Payment $payment): \Barryvdh\DomPDF\PDF
    {
        $payment->loadMissing(['enrollment.student', 'enrollment.school', 'enrollment.academicYear', 'recorder']);
        $enrollment = $payment->enrollment;
        $student    = $enrollment->student;

        $qrCode = $this->qrCodeService->generate([
            'type'           => 'RECU_PAIEMENT',
            'receipt_number' => $payment->receipt_number,
            'amount'         => (float) $payment->amount,
            'matricule'      => $student->matricule,
            'date'           => $payment->payment_date->format('d/m/Y'),
        ]);

        $pdf = Pdf::loadView('pdfs.receipt', [
            'payment'      => $payment,
            'enrollment'   => $enrollment,
            'student'      => $student,
            'school'       => $enrollment->school,
            'academicYear' => $enrollment->academicYear,
            'recorder'     => $payment->recorder,
            'qrCode'       => $qrCode,
            'generatedAt'  => now(),
        ]);

        $pdf->setPaper([0, 0, 420, 595], 'portrait'); // A5

        return $pdf;
    }

    private function getPhotoBase64(Student $student): ?string
    {
        if (!$student->photo_url) {
            return null;
        }

        // Extraire le segment de chemin quelle que soit la forme de photo_url :
        // - URL complète : https://api.railway.app/storage/students/photos/xxx.jpg
        // - Chemin absolu : /storage/students/photos/xxx.jpg
        $url = $student->photo_url;
        $parsed = parse_url($url, PHP_URL_PATH) ?? $url; // ex: /storage/students/photos/xxx.jpg

        if (str_starts_with($parsed, '/storage/')) {
            $path = storage_path('app/public/' . substr($parsed, 9));
        } else {
            $path = storage_path('app/public/' . ltrim($parsed, '/'));
        }

        if (file_exists($path)) {
            $mime = mime_content_type($path) ?: 'image/jpeg';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        }

        return null;
    }
}
