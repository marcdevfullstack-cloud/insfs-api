<?php

namespace App\Services;

use App\Models\AdmittedStudent;
use App\Models\EnrollmentApplication;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicationPdfService
{
    public function __construct(private QrCodeService $qrCodeService) {}

    /**
     * Fiche d'identification — générée par la scolarité après validation.
     */
    public function generateFicheIdentification(
        EnrollmentApplication $application,
        Student $student,
        AdmittedStudent $admitted
    ): \Barryvdh\DomPDF\PDF {
        $qrCode = $this->qrCodeService->generate([
            'type'      => 'FICHE_IDENTIFICATION',
            'matricule' => $student->matricule,
            'nom'       => $student->last_name,
            'prenoms'   => $student->first_name,
        ]);

        $photo = $this->getPhotoBase64FromUrl($application->photo_url);

        $pdf = Pdf::loadView('pdfs.fiche-identification', [
            'application'  => $application,
            'student'      => $student,
            'admitted'     => $admitted,
            'school'       => $admitted->school,
            'academicYear' => $admitted->academicYear,
            'qrCode'       => $qrCode,
            'photo'        => $photo,
            'generatedAt'  => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Fiche d'inscription — avec signatures scolarité et visa comptabilité.
     */
    public function generateFicheInscription(EnrollmentApplication $application): \Barryvdh\DomPDF\PDF
    {
        $application->loadMissing([
            'student',
            'admittedStudent.school',
            'admittedStudent.academicYear',
            'academicYear',
            'signatures',
            'student.enrollments.payments',
        ]);

        $scolariteSignature = $application->signature('SCOLARITE_INSCRIPTION');
        $enrollment         = $application->student?->enrollments->first();

        $pdf = Pdf::loadView('pdfs.fiche-inscription', [
            'application'         => $application,
            'student'             => $application->student,
            'admitted'            => $application->admittedStudent,
            'school'              => $application->admittedStudent->school,
            'academicYear'        => $application->admittedStudent->academicYear,
            'enrollment'          => $enrollment,
            'scolariteSignature'  => $scolariteSignature,
            'generatedAt'         => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Certificat d'inscription — signé par le Chef du Service.
     */
    public function generateCertificatInscription(EnrollmentApplication $application): \Barryvdh\DomPDF\PDF
    {
        $application->loadMissing([
            'student',
            'admittedStudent.school',
            'admittedStudent.academicYear',
            'academicYear',
            'signatures',
        ]);

        $chefSignature = $application->signature('CHEF_CERTIFICAT');
        $photo         = $this->getPhotoBase64FromUrl($application->photo_url);

        $qrCode = $this->qrCodeService->generate([
            'type'      => 'CERTIFICAT_INSCRIPTION',
            'matricule' => $application->student?->matricule,
            'nom'       => $application->last_name,
            'prenoms'   => $application->first_name,
            'ecole'     => $application->admittedStudent->school?->code,
            'annee'     => $application->admittedStudent->academicYear?->label,
        ]);

        $pdf = Pdf::loadView('pdfs.certificat-inscription-portail', [
            'application'  => $application,
            'student'      => $application->student,
            'admitted'     => $application->admittedStudent,
            'school'       => $application->admittedStudent->school,
            'academicYear' => $application->admittedStudent->academicYear,
            'chefSignature' => $chefSignature,
            'qrCode'       => $qrCode,
            'photo'        => $photo,
            'generatedAt'  => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    private function getPhotoBase64FromUrl(?string $photoUrl): ?string
    {
        if (!$photoUrl) {
            return null;
        }

        $parsed = parse_url($photoUrl, PHP_URL_PATH) ?? $photoUrl;

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
