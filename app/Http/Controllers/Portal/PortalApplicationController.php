<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Mail\DossierSoumisMail;
use App\Models\ApplicationDocument;
use App\Models\ApplicationSignature;
use App\Models\EnrollmentApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PortalApplicationController extends Controller
{
    /**
     * Récupérer le dossier de l'étudiant connecté.
     */
    public function show(Request $request): JsonResponse
    {
        $application = $this->getApplication($request);

        $application->load([
            'admittedStudent.school',
            'admittedStudent.academicYear',
            'documents',
            'signatures',
            'academicYear',
        ]);

        return response()->json($application);
    }

    /**
     * Sauvegarder la fiche de renseignements (Sections I à V).
     */
    public function saveRenseignements(Request $request): JsonResponse
    {
        $application = $this->getApplication($request);
        $this->assertEditable($application);

        $validated = $request->validate([
            // Section I
            'gender'                  => 'required|in:M,F',
            'date_of_birth'           => 'required|date|before:today',
            'place_of_birth'          => 'required|string|max:255',
            'nationality'             => 'nullable|string|max:100',
            'father_name'             => 'nullable|string|max:255',
            'mother_name'             => 'nullable|string|max:255',
            'marital_status'          => 'nullable|in:Célibataire,Marié(e),Veuf(ve),Divorcé(e)',
            'children_count'          => 'nullable|integer|min:0',
            'phone'                   => 'required|string|max:20',
            'email'                   => 'nullable|email|max:255',
            // Statut
            'status_type'             => 'required|in:Fonctionnaire,Boursier national,Boursier étranger,Non-boursier',
            'matricule_fonctionnaire' => 'nullable|string|max:50',
            'emploi'                  => 'nullable|string|max:100',
            'echelon'                 => 'nullable|string|max:50',
            'categorie'               => 'nullable|string|max:50',
            'classe'                  => 'nullable|string|max:50',
            // Section II - Diplômes
            'diploma_cepe'            => 'nullable|boolean',
            'diploma_bepc'            => 'nullable|boolean',
            'diploma_bac'             => 'nullable|boolean',
            'diploma_bac_serie'       => 'nullable|string|max:10',
            'other_diplomas'          => 'nullable|string|max:1000',
            // Section III
            'entry_date'              => 'nullable|date',
            // Section IV - Adresse
            'address_quarter'         => 'nullable|string|max:255',
            'address_apartment'       => 'nullable|string|max:50',
            'address_phone'           => 'nullable|string|max:20',
            'postal_box'              => 'nullable|string|max:50',
            'vacation_address'        => 'nullable|string|max:500',
            'tutor_name'              => 'nullable|string|max:255',
            'tutor_address'           => 'nullable|string|max:255',
            'tutor_phone'             => 'nullable|string|max:20',
            // Section V - Santé
            'has_health_issues'       => 'nullable|boolean',
            'health_condition'        => 'nullable|string|max:500',
            'doctor_info'             => 'nullable|string|max:500',
        ]);

        // Si correction demandée, ne modifier que les champs signalés
        if ($application->status === 'CORRECTION_DEMANDEE' && !empty($application->correction_fields)) {
            $allowedFields = collect($application->correction_fields)->pluck('field')->toArray();
            $validated = array_intersect_key($validated, array_flip($allowedFields));
        }

        $application->update($validated);

        return response()->json($application->fresh()->load(['admittedStudent.school', 'admittedStudent.academicYear', 'documents', 'signatures']));
    }

    /**
     * Upload photo d'identité (dans la fiche de renseignements).
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $application = $this->getApplication($request);
        $this->assertEditable($application);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:3072',
        ]);

        // Supprimer l'ancienne photo si elle existe
        if ($application->photo_url) {
            $oldPath = str_replace('/storage/', '', parse_url($application->photo_url, PHP_URL_PATH) ?? '');
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('photo')->store("applications/{$application->id}/photo", 'public');
        $url  = Storage::url($path);

        $application->update(['photo_url' => $url]);

        // Créer ou mettre à jour le document de type PHOTO
        ApplicationDocument::updateOrCreate(
            ['application_id' => $application->id, 'document_type' => 'PHOTO'],
            [
                'file_path'     => $path,
                'original_name' => $request->file('photo')->getClientOriginalName(),
                'mime_type'     => $request->file('photo')->getMimeType(),
                'file_size'     => $request->file('photo')->getSize(),
                'status'        => 'SOUMIS',
            ]
        );

        return response()->json(['photo_url' => $url]);
    }

    /**
     * Uploader une pièce justificative.
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $application = $this->getApplication($request);
        $this->assertEditable($application);

        $request->validate([
            'document_type' => 'required|in:CNI_PASSEPORT,EXTRAIT_NAISSANCE,DIPLOME_BEPC,DIPLOME_BAC,CERTIFICAT_TRAVAIL,ATTESTATION_BOURSE,AUTRE_DIPLOME,AUTRE',
            'file'          => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        $path = $request->file('file')->store(
            "applications/{$application->id}/documents",
            'public'
        );

        $document = ApplicationDocument::create([
            'application_id' => $application->id,
            'document_type'  => $request->document_type,
            'file_path'      => $path,
            'original_name'  => $request->file('file')->getClientOriginalName(),
            'mime_type'       => $request->file('file')->getMimeType(),
            'file_size'      => $request->file('file')->getSize(),
            'status'         => 'SOUMIS',
        ]);

        return response()->json($document, 201);
    }

    /**
     * Supprimer une pièce justificative.
     */
    public function deleteDocument(Request $request, ApplicationDocument $document): JsonResponse
    {
        $application = $this->getApplication($request);
        $this->assertEditable($application);

        if ($document->application_id !== $application->id) {
            return response()->json(['message' => 'Document non trouvé.'], 404);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(null, 204);
    }

    /**
     * Enregistrer la signature de l'engagement.
     */
    public function saveEngagement(Request $request): JsonResponse
    {
        $application = $this->getApplication($request);
        $this->assertEditable($application);

        $request->validate([
            'signature_image' => 'required|string', // base64 PNG
        ]);

        // Valider que c'est bien une image base64
        if (!str_starts_with($request->signature_image, 'data:image/')) {
            throw ValidationException::withMessages([
                'signature_image' => 'Format de signature invalide.',
            ]);
        }

        $portalUser = $request->user();

        // Créer ou remplacer la signature engagement
        ApplicationSignature::updateOrCreate(
            [
                'application_id' => $application->id,
                'signature_type' => 'ETUDIANT_ENGAGEMENT',
            ],
            [
                'signature_image' => $request->signature_image,
                'signer_name'     => $portalUser->first_name . ' ' . $portalUser->last_name,
                'ip_address'      => $request->ip(),
                'signed_at'       => now(),
            ]
        );

        $application->update([
            'engagement_signed'    => true,
            'engagement_signed_at' => now(),
        ]);

        return response()->json($application->fresh()->load(['documents', 'signatures']));
    }

    /**
     * Enregistrer la signature de la fiche de renseignements.
     */
    public function saveSignatureRenseignements(Request $request): JsonResponse
    {
        $application = $this->getApplication($request);
        $this->assertEditable($application);

        $request->validate([
            'signature_image' => 'required|string',
        ]);

        if (!str_starts_with($request->signature_image, 'data:image/')) {
            throw ValidationException::withMessages([
                'signature_image' => 'Format de signature invalide.',
            ]);
        }

        $portalUser = $request->user();

        ApplicationSignature::updateOrCreate(
            [
                'application_id' => $application->id,
                'signature_type' => 'ETUDIANT_RENSEIGNEMENTS',
            ],
            [
                'signature_image' => $request->signature_image,
                'signer_name'     => $portalUser->first_name . ' ' . $portalUser->last_name,
                'ip_address'      => $request->ip(),
                'signed_at'       => now(),
            ]
        );

        return response()->json($application->fresh()->load(['documents', 'signatures']));
    }

    /**
     * Soumettre le dossier au service scolarité.
     */
    public function submit(Request $request): JsonResponse
    {
        $application = $this->getApplication($request);
        $this->assertEditable($application);

        // Vérifier que le dossier est complet
        if (!$application->isReadyToSubmit()) {
            throw ValidationException::withMessages([
                'dossier' => 'Le dossier est incomplet. Vérifiez que toutes les sections obligatoires sont renseignées.',
            ]);
        }

        // Vérifier les signatures obligatoires
        $hasRenseignementsSignature = $application->signatures()
            ->where('signature_type', 'ETUDIANT_RENSEIGNEMENTS')
            ->exists();

        $hasEngagementSignature = $application->signatures()
            ->where('signature_type', 'ETUDIANT_ENGAGEMENT')
            ->exists();

        if (!$hasRenseignementsSignature || !$hasEngagementSignature) {
            throw ValidationException::withMessages([
                'signatures' => 'Les deux fiches doivent être signées avant la soumission.',
            ]);
        }

        $application->update([
            'status'       => 'SOUMIS',
            'submitted_at' => now(),
            // Effacer les champs de correction précédents
            'correction_fields' => null,
        ]);

        // Mettre à jour le statut de l'admis
        $application->admittedStudent?->update(['status' => 'SOUMIS']);

        // Envoyer confirmation
        try {
            $portalUser = $request->user();
            Mail::to($portalUser->email)->send(new DossierSoumisMail($portalUser, $application));
        } catch (\Exception) {
            // Silencieux
        }

        return response()->json($application->fresh()->load(['admittedStudent.school', 'admittedStudent.academicYear', 'documents', 'signatures']));
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function getApplication(Request $request): EnrollmentApplication
    {
        $application = $request->user()->application;

        if (!$application) {
            abort(404, 'Aucun dossier trouvé.');
        }

        return $application;
    }

    private function assertEditable(EnrollmentApplication $application): void
    {
        if (!$application->isEditable()) {
            abort(422, 'Ce dossier ne peut plus être modifié dans son état actuel.');
        }
    }
}
