<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Génère un QR code SVG contenant les données encodées en JSON.
     * Retourne le SVG brut pour intégration directe dans les templates Blade/dompdf.
     */
    public function generate(array $data): string
    {
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);

        return (string) QrCode::format('svg')
            ->size(150)
            ->margin(1)
            ->generate($jsonData);
    }
}
