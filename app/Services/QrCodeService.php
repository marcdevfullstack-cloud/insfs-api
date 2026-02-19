<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    /**
     * Génère un QR code PNG encodé en base64 (data URI).
     * Utilise endroid/qr-code avec GD (pas besoin d'imagick).
     * Compatible dompdf pour intégration dans les templates Blade.
     */
    public function generate(array $data): string
    {
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);

        $qrCode = QrCode::create($jsonData)
            ->setSize(150)
            ->setMargin(5);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return $result->getDataUri(); // 'data:image/png;base64,...'
    }
}
