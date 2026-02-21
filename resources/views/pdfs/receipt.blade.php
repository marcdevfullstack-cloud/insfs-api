<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement — {{ $payment->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1a1a1a;
            background: white;
        }
        .page { padding: 8mm 14mm 10mm; }

        /* ── Bandeau drapeau ────────── */
        .flag-band table { width: 100%; border-collapse: collapse; height: 5px; }
        .flag-orange { background: #F77F00; width: 33.3%; }
        .flag-white  { background: #FFFFFF; border: 1px solid #e0e0e0; width: 33.3%; }
        .flag-green  { background: #009A44; width: 33.3%; }
        .flag-band { margin-bottom: 8px; }

        /* ── En-tête ────────────────── */
        .header { width: 100%; border-collapse: collapse; border-bottom: 2.5px solid #009A44; margin-bottom: 10px; padding-bottom: 6px; }
        .logo-text    { font-size: 18pt; font-weight: bold; color: #009A44; }
        .logo-sub     { font-size: 7.5pt; color: #555; margin-top: 2px; }
        .logo-ministry { font-size: 7pt; color: #444; margin-top: 3px; line-height: 1.5; }
        .header-right { text-align: right; font-size: 7.5pt; color: #555; line-height: 1.6; }

        /* ── Titre ──────────────────── */
        .title-block { text-align: center; margin: 10px 0 8px; }
        .title { font-size: 14pt; font-weight: bold; color: #009A44; letter-spacing: 1px; text-transform: uppercase; }
        .title-bar { width: 50px; height: 2.5px; background: #F77F00; margin: 5px auto; }
        .receipt-num { font-size: 9.5pt; color: #F77F00; font-weight: bold; }

        /* ── Badge montant ──────────── */
        .amount-badge {
            text-align: center; background: #009A44; color: white;
            font-size: 18pt; font-weight: bold; letter-spacing: 1px;
            padding: 7px 0; margin: 8px 0;
        }
        .amount-label { font-size: 8pt; opacity: 0.85; font-weight: normal; margin-bottom: 2px; }

        /* ── Infos table ────────────── */
        .info-box { border: 1.5px solid #009A44; margin-bottom: 8px; }
        .info-box-title {
            background: #009A44; color: white;
            font-size: 8pt; font-weight: bold; letter-spacing: 0.5px;
            padding: 4px 10px; text-transform: uppercase;
        }
        .info-table { width: 100%; border-collapse: collapse; padding: 0 10px; }
        .info-table tr td { padding: 3.5px 10px; font-size: 8.5pt; border-bottom: 1px dotted #ddd; }
        .info-table tr:last-child td { border-bottom: none; }
        .info-label { font-weight: bold; color: #007A33; width: 140px; }
        .info-value { color: #1a1a1a; }

        /* ── Méthode paiement ───────── */
        .method-badge {
            display: inline-block;
            padding: 2px 8px; border-radius: 3px;
            font-size: 8pt; font-weight: bold;
        }
        .method-especes   { background: #f0faf4; color: #007A33; border: 1px solid #009A44; }
        .method-virement  { background: #eff6ff; color: #1d4ed8; border: 1px solid #3b82f6; }
        .method-mobile    { background: #fff7ed; color: #c2410c; border: 1px solid #f97316; }

        /* ── Notice ─────────────────── */
        .notice {
            background: #f9f9f9; border: 1px solid #ddd;
            padding: 6px 10px; font-size: 7.5pt; color: #666; text-align: center; margin: 8px 0;
        }

        /* ── Bas de page ────────────── */
        .bottom { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .qr-cell { width: 90px; text-align: center; }
        .qr-cell img { width: 80px; height: 80px; display: block; margin: 0 auto; }
        .qr-label { font-size: 6.5pt; color: #777; margin-top: 3px; }
        .sig-box   { width: 150px; height: 55px; border: 1px solid #ccc; margin-bottom: 4px; }
        .sig-label { font-size: 8.5pt; font-weight: bold; color: #007A33; }
        .sig-sub   { font-size: 7pt; color: #777; margin-top: 2px; }
        .footer    { font-size: 6.5pt; color: #aaa; text-align: center; margin-top: 10px; }

        .footer-band { margin-top: 10px; }
        .footer-band table { width: 100%; border-collapse: collapse; height: 4px; }
    </style>
</head>
<body>
<div class="page">

    {{-- Bandeau drapeau haut --}}
    <div class="flag-band">
        <table><tr>
            <td class="flag-orange"></td>
            <td class="flag-white"></td>
            <td class="flag-green"></td>
        </tr></table>
    </div>

    {{-- En-tête --}}
    <table class="header">
        <tr>
            <td style="width:60%;">
                <div class="logo-text">INSFS</div>
                <div class="logo-sub">Institut National de Formation Sociale</div>
                <div class="logo-ministry">
                    Ministère de la Femme, de la Famille et de l'Enfant<br>
                    <span style="color:#F77F00; font-weight:bold;">République de Côte d'Ivoire</span>
                </div>
            </td>
            <td class="header-right">
                Liberté — Égalité — Fraternité<br><br>
                Abidjan, le {{ $generatedAt->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    {{-- Titre --}}
    <div class="title-block">
        <div class="title">Reçu de Paiement</div>
        <div class="title-bar"></div>
        <div class="receipt-num">N° {{ $payment->receipt_number }}</div>
    </div>

    {{-- Badge montant --}}
    <div class="amount-badge">
        <div class="amount-label">Montant reçu</div>
        {{ number_format((float)$payment->amount, 0, ',', ' ') }} FCFA
    </div>

    {{-- Infos étudiant --}}
    <div class="info-box">
        <div class="info-box-title">Étudiant(e)</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Nom et Prénoms :</td>
                <td class="info-value"><strong>{{ strtoupper($student->last_name) }} {{ $student->first_name }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Matricule :</td>
                <td class="info-value"><strong>{{ $student->matricule }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Statut :</td>
                <td class="info-value">{{ $student->status_type }}</td>
            </tr>
        </table>
    </div>

    {{-- Infos paiement --}}
    <div class="info-box">
        <div class="info-box-title">Détails du Paiement</div>
        <table class="info-table">
            <tr>
                <td class="info-label">École :</td>
                <td class="info-value">{{ $school->name }} ({{ $school->code }})</td>
            </tr>
            <tr>
                <td class="info-label">Année académique :</td>
                <td class="info-value">{{ $academicYear->label }}</td>
            </tr>
            <tr>
                <td class="info-label">Type de frais :</td>
                <td class="info-value">
                    {{ $payment->payment_type === 'FRAIS_INSCRIPTION' ? 'Frais d\'inscription' : 'Frais de scolarité' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">Tranche N° :</td>
                <td class="info-value">{{ $payment->installment_number }}</td>
            </tr>
            <tr>
                <td class="info-label">Date de paiement :</td>
                <td class="info-value">{{ $payment->payment_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Mode de paiement :</td>
                <td class="info-value">
                    @php
                        $methodClass = match($payment->payment_method) {
                            'VIREMENT'     => 'method-virement',
                            'MOBILE_MONEY' => 'method-mobile',
                            default        => 'method-especes',
                        };
                        $methodLabel = match($payment->payment_method) {
                            'VIREMENT'     => 'Virement bancaire',
                            'MOBILE_MONEY' => 'Mobile Money',
                            default        => 'Espèces',
                        };
                    @endphp
                    <span class="method-badge {{ $methodClass }}">{{ $methodLabel }}</span>
                </td>
            </tr>
            @if($payment->notes)
            <tr>
                <td class="info-label">Notes :</td>
                <td class="info-value">{{ $payment->notes }}</td>
            </tr>
            @endif
            @if($recorder)
            <tr>
                <td class="info-label">Enregistré par :</td>
                <td class="info-value">{{ $recorder->full_name }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Notice --}}
    <div class="notice">
        Ce reçu atteste du paiement effectué à l'INSFS. Veuillez le conserver comme justificatif officiel.
    </div>

    {{-- QR + Signature --}}
    <table class="bottom">
        <tr>
            <td class="qr-cell">
                <img src="{{ $qrCode }}" alt="QR Code" width="80" height="80">
                <div class="qr-label">Vérification<br>du reçu</div>
            </td>
            <td style="text-align:right;">
                <div class="sig-box"></div>
                <div class="sig-label">Le Responsable Comptable</div>
                <div class="sig-sub">Signature et cachet officiel</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document généré le {{ $generatedAt->format('d/m/Y à H:i') }} — INSFS Gestion des Inscriptions
    </div>

    {{-- Bandeau drapeau bas --}}
    <div class="footer-band">
        <table><tr>
            <td class="flag-orange"></td>
            <td class="flag-white"></td>
            <td class="flag-green"></td>
        </tr></table>
    </div>

</div>
</body>
</html>
