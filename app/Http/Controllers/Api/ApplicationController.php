<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CorrectionDemandeMail;
use App\Mail\DossierRejeterMail;
use App\Mail\FicheIdentificationMail;
use App\Mail\DossierCompletMail;
use App\Models\ApplicationSignature;
use App\Models\Enrollment;
use App\Models\EnrollmentApplication;
use App\Models\Student;
use App\Services\ApplicationPdfService;
use App\Services\MatriculeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationPdfService $pdfService,
        private MatriculeService $matriculeService,
    ) {}

    /**
     * Liste des dossiers (côté scolarité).
     */
    public function index(Request $request): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $query = EnrollmentApplication::with([
            'portalUser',
            'admittedStudent.school',
            'admittedStudent.academicYear',
            'academicYear',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('school_id')) {
            $query->whereHas('admittedStudent', fn($q) => $q->where('school_id', $request->school_id));
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('last_name', 'like', "%{$term}%")
                  ->orWhere('first_name', 'like', "%{$term}%");
            });
        }

        $applications = $query
            ->orderByRaw("FIELD(status, 'SOUMIS', 'EN_TRAITEMENT', 'CORRECTION_DEMANDEE', 'VALIDE', 'COMPLET', 'REJETE', 'BROUILLON')")
            ->orderBy('submitted_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($applications);
    }

    /**
     * Détail complet d'un dossier.
     */
    public function show(Request $request, EnrollmentApplication $application): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $application->load([
            'portalUser',
            'admittedStudent.school',
            'admittedStudent.academicYear',
            'academicYear',
            'documents',
            'signatures',
            'student',
            'validator',
        ]);

        return response()->json($application);
    }

    /**
     * Passer le dossier en traitement.
     */
    public function startProcessing(Request $request, EnrollmentApplication $application): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if ($application->status !== 'SOUMIS') {
            return response()->json(['message' => 'Le dossier doit être au statut SOUMIS.'], 422);
        }

        $application->update(['status' => 'EN_TRAITEMENT']);

        return response()->json($application->fresh());
    }

    /**
     * Valider un dossier → création étudiant + inscription + génération fiche identification.
     */
    public function validate(Request $request, EnrollmentApplication $application): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if (!in_array($application->status, ['EN_TRAITEMENT', 'SOUMIS'])) {
            return response()->json(['message' => 'Le dossier ne peut pas être validé dans son état actuel.'], 422);
        }

        DB::transaction(function () use ($application, $request) {
            $admitted = $application->admittedStudent;

            // 1. Créer l'étudiant dans le système
            $matricule = $this->matriculeService->generate();

            $student = Student::create([
                'matricule'               => $matricule,
                'last_name'               => $application->last_name,
                'first_name'              => $application->first_name,
                'gender'                  => $application->gender,
                'date_of_birth'           => $application->date_of_birth,
                'place_of_birth'          => $application->place_of_birth,
                'nationality'             => $application->nationality ?? 'Ivoirienne',
                'father_name'             => $application->father_name,
                'mother_name'             => $application->mother_name,
                'marital_status'          => $application->marital_status ?? 'Célibataire',
                'children_count'          => $application->children_count ?? 0,
                'phone'                   => $application->phone,
                'email'                   => $application->email,
                'address'                 => $application->address_quarter,
                'photo_url'               => $application->photo_url,
                'status_type'             => $application->status_type ?? 'Non-boursier',
                'matricule_fonctionnaire' => $application->matricule_fonctionnaire,
                'emploi'                  => $application->emploi,
                'echelon'                 => $application->echelon,
                'categorie'               => $application->categorie,
                'classe'                  => $application->classe,
                'entry_mode'              => $admitted->entry_mode,
                'diploma_cepe'            => $application->diploma_cepe,
                'diploma_bepc'            => $application->diploma_bepc,
                'diploma_bac'             => $application->diploma_bac,
                'diploma_bac_serie'       => $application->diploma_bac_serie,
                'other_diplomas'          => $application->other_diplomas,
                'portal_user_id'          => $application->portal_user_id,
                'application_id'          => $application->id,
            ]);

            // 2. Créer l'inscription
            Enrollment::create([
                'student_id'       => $student->id,
                'school_id'        => $admitted->school_id,
                'academic_year_id' => $admitted->academic_year_id,
                'year_of_study'    => $admitted->year_of_study,
                'quality'          => $this->mapEntryModeToQuality($admitted->entry_mode),
                'enrollment_date'  => $application->entry_date ?? now(),
                'status'           => 'EN_COURS',
            ]);

            // 3. Générer la fiche d'identification PDF
            $pdf  = $this->pdfService->generateFicheIdentification($application, $student, $admitted);
            $path = "applications/{$application->id}/fiche-identification.pdf";
            Storage::disk('public')->put($path, $pdf->output());

            // 4. Mettre à jour le dossier
            $application->update([
                'status'                    => 'VALIDE',
                'student_id'                => $student->id,
                'validated_by'              => $request->user()->id,
                'validated_at'              => now(),
                'fiche_identification_path' => $path,
            ]);

            // 5. Mettre à jour le statut de l'admis
            $admitted->update(['status' => 'VALIDE']);
        });

        $application->refresh()->load(['portalUser', 'admittedStudent.school', 'student']);

        // Envoyer email
        try {
            Mail::to($application->portalUser->email)
                ->send(new FicheIdentificationMail($application->portalUser, $application));
        } catch (\Exception) {
            // Silencieux
        }

        return response()->json($application);
    }

    /**
     * Demander une correction.
     */
    public function requestCorrection(Request $request, EnrollmentApplication $application): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if (!in_array($application->status, ['SOUMIS', 'EN_TRAITEMENT'])) {
            return response()->json(['message' => 'Le dossier doit être soumis ou en traitement.'], 422);
        }

        $request->validate([
            'correction_fields' => 'required|array|min:1',
            'correction_fields.*.field'   => 'required|string',
            'correction_fields.*.message' => 'required|string|max:500',
        ]);

        $application->update([
            'status'            => 'CORRECTION_DEMANDEE',
            'correction_fields' => $request->correction_fields,
        ]);

        // Envoyer email
        try {
            Mail::to($application->portalUser->email)
                ->send(new CorrectionDemandeMail($application->portalUser, $application));
        } catch (\Exception) {
            // Silencieux
        }

        return response()->json($application->fresh());
    }

    /**
     * Rejeter un dossier.
     */
    public function reject(Request $request, EnrollmentApplication $application): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if (!in_array($application->status, ['SOUMIS', 'EN_TRAITEMENT'])) {
            return response()->json(['message' => 'Le dossier doit être soumis ou en traitement.'], 422);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $application->update([
            'status'           => 'REJETE',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Envoyer email
        try {
            Mail::to($application->portalUser->email)
                ->send(new DossierRejeterMail($application->portalUser, $application));
        } catch (\Exception) {
            // Silencieux
        }

        return response()->json($application->fresh());
    }

    /**
     * Finaliser le dossier — générer fiche inscription + certificat (nécessite les 2 signatures admin).
     */
    public function complete(Request $request, EnrollmentApplication $application): JsonResponse
    {
        if (!in_array($request->user()->role, ['ADMIN', 'SCOLARITE'])) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if ($application->status !== 'VALIDE') {
            return response()->json(['message' => 'Le dossier doit être au statut VALIDE.'], 422);
        }

        $request->validate([
            'scolarite_signature' => 'required|string',
            'chef_signature'      => 'required|string',
        ]);

        foreach ([$request->scolarite_signature, $request->chef_signature] as $sig) {
            if (!str_starts_with($sig, 'data:image/')) {
                return response()->json(['message' => 'Format de signature invalide.'], 422);
            }
        }

        $application->load(['student', 'admittedStudent.school', 'admittedStudent.academicYear', 'academicYear', 'portalUser']);

        DB::transaction(function () use ($application, $request) {
            // Sauvegarder les signatures admin
            ApplicationSignature::updateOrCreate(
                ['application_id' => $application->id, 'signature_type' => 'SCOLARITE_INSCRIPTION'],
                [
                    'signature_image' => $request->scolarite_signature,
                    'signer_name'     => $request->user()->full_name,
                    'ip_address'      => $request->ip(),
                    'signed_at'       => now(),
                ]
            );

            ApplicationSignature::updateOrCreate(
                ['application_id' => $application->id, 'signature_type' => 'CHEF_CERTIFICAT'],
                [
                    'signature_image' => $request->chef_signature,
                    'signer_name'     => $request->user()->full_name,
                    'ip_address'      => $request->ip(),
                    'signed_at'       => now(),
                ]
            );

            $application->refresh()->load('signatures');

            // Générer Fiche d'inscription
            $pdfInscription  = $this->pdfService->generateFicheInscription($application);
            $pathInscription = "applications/{$application->id}/fiche-inscription.pdf";
            Storage::disk('public')->put($pathInscription, $pdfInscription->output());

            // Générer Certificat d'inscription
            $pdfCertificat  = $this->pdfService->generateCertificatInscription($application);
            $pathCertificat = "applications/{$application->id}/certificat-inscription.pdf";
            Storage::disk('public')->put($pathCertificat, $pdfCertificat->output());

            $application->update([
                'status'                    => 'COMPLET',
                'completed_at'              => now(),
                'fiche_inscription_path'    => $pathInscription,
                'certificat_inscription_path' => $pathCertificat,
            ]);
        });

        // Envoyer email
        try {
            Mail::to($application->portalUser->email)
                ->send(new DossierCompletMail($application->portalUser, $application));
        } catch (\Exception) {
            // Silencieux
        }

        return response()->json($application->fresh()->load(['signatures', 'student']));
    }

    /**
     * Télécharger un document généré (côté scolarité).
     */
    public function downloadDocument(EnrollmentApplication $application, string $type): BinaryFileResponse
    {
        $pathColumn = match ($type) {
            'fiche-identification'   => 'fiche_identification_path',
            'fiche-inscription'      => 'fiche_inscription_path',
            'certificat-inscription' => 'certificat_inscription_path',
            default                  => null,
        };

        if (!$pathColumn || !$application->$pathColumn) {
            abort(404, 'Document non disponible.');
        }

        $absolutePath = storage_path('app/public/' . $application->$pathColumn);

        if (!file_exists($absolutePath)) {
            abort(404, 'Fichier introuvable.');
        }

        return response()->download($absolutePath, $type . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function mapEntryModeToQuality(string $entryMode): string
    {
        return match ($entryMode) {
            'Concours direct'        => 'CD',
            'Concours professionnel' => 'CP',
            'Analyse de dossier'     => 'FC',
            default                  => 'CD',
        };
    }
}
