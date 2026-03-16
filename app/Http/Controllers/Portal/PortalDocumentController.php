<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PortalDocumentController extends Controller
{
    /**
     * Liste des documents générés disponibles pour l'étudiant.
     */
    public function index(Request $request): JsonResponse
    {
        $application = $request->user()->application;

        if (!$application) {
            return response()->json(['documents' => []]);
        }

        $documents = [];

        if ($application->fiche_identification_path) {
            $documents[] = [
                'type'        => 'FICHE_IDENTIFICATION',
                'label'       => "Fiche d'identification",
                'available'   => true,
                'description' => 'À présenter à la Comptabilité pour vos paiements.',
            ];
        }

        if ($application->fiche_inscription_path) {
            $documents[] = [
                'type'        => 'FICHE_INSCRIPTION',
                'label'       => "Fiche d'inscription",
                'available'   => true,
                'description' => 'Document officiel d\'inscription signé par la Scolarité.',
            ];
        }

        if ($application->certificat_inscription_path) {
            $documents[] = [
                'type'        => 'CERTIFICAT_INSCRIPTION',
                'label'       => "Certificat d'inscription",
                'available'   => true,
                'description' => 'Certificat officiel signé par le Chef du Service.',
            ];
        }

        return response()->json([
            'application_status' => $application->status,
            'documents'          => $documents,
        ]);
    }

    /**
     * Télécharger un document généré.
     */
    public function download(Request $request, string $type): BinaryFileResponse
    {
        $application = $request->user()->application;

        if (!$application) {
            abort(404, 'Aucun dossier trouvé.');
        }

        $pathColumn = match ($type) {
            'FICHE_IDENTIFICATION' => 'fiche_identification_path',
            'FICHE_INSCRIPTION'    => 'fiche_inscription_path',
            'CERTIFICAT_INSCRIPTION' => 'certificat_inscription_path',
            default                => null,
        };

        if (!$pathColumn || !$application->$pathColumn) {
            abort(404, 'Document non disponible.');
        }

        $absolutePath = storage_path('app/public/' . $application->$pathColumn);

        if (!file_exists($absolutePath)) {
            abort(404, 'Fichier introuvable.');
        }

        $fileName = match ($type) {
            'FICHE_IDENTIFICATION'   => 'fiche-identification.pdf',
            'FICHE_INSCRIPTION'      => 'fiche-inscription.pdf',
            'CERTIFICAT_INSCRIPTION' => 'certificat-inscription.pdf',
            default                  => 'document.pdf',
        };

        return response()->download($absolutePath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
