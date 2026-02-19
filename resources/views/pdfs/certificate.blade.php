<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat d'Inscription — {{ $student->matricule }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            background: white;
        }
        .page { padding: 10mm 18mm 12mm; }

        /* ── Bandeau drapeau ivoirien ─────────────── */
        .flag-band { width: 100%; height: 6px; margin-bottom: 10px; }
        .flag-band table { width: 100%; border-collapse: collapse; height: 6px; }
        .flag-orange { background: #F77F00; width: 33.3%; }
        .flag-white  { background: #FFFFFF; border: 1px solid #e0e0e0; width: 33.3%; }
        .flag-green  { background: #009A44; width: 33.3%; }

        /* ── En-tête ─────────────────────────────── */
        .header { width: 100%; border-collapse: collapse; border-bottom: 3px solid #009A44; margin-bottom: 12px; padding-bottom: 8px; }
        .header td { vertical-align: top; padding-bottom: 8px; }
        .logo-text    { font-size: 20pt; font-weight: bold; color: #009A44; }
        .logo-sub     { font-size: 8pt; color: #555; margin-top: 2px; }
        .logo-ministry { font-size: 7.5pt; color: #444; margin-top: 4px; line-height: 1.5; }
        .header-right { text-align: right; font-size: 7.5pt; color: #555; line-height: 1.6; }
        .header-orange { color: #F77F00; font-weight: bold; }

        /* ── Titre ───────────────────────────────── */
        .title-block { text-align: center; margin: 14px 0 12px; }
        .title { font-size: 17pt; font-weight: bold; color: #009A44; letter-spacing: 1.5px; text-transform: uppercase; }
        .title-bar { width: 60px; height: 3px; background: #F77F00; margin: 7px auto; }
        .academic-year { font-size: 11pt; color: #F77F00; font-weight: bold; }

        /* ── Texte intro ─────────────────────────── */
        .intro { text-align: center; font-size: 10pt; color: #333; margin: 8px 0 12px; line-height: 1.6; }

        /* ── Carte étudiant ──────────────────────── */
        .card { border: 2px solid #009A44; padding: 12px 14px; margin: 10px 0; }
        .matricule-badge {
            text-align: center; background: #009A44; color: white;
            font-size: 13pt; font-weight: bold; letter-spacing: 2px;
            padding: 5px 0; margin-bottom: 10px;
        }
        .card-inner { width: 100%; border-collapse: collapse; }
        .card-inner td { vertical-align: top; }

        /* Photo */
        .photo-cell { width: 92px; padding-right: 12px; }
        .photo-box  {
            width: 88px; height: 108px;
            border: 2px solid #009A44;
            background: #f5f5f5; overflow: hidden;
        }
        .photo-box img { width: 88px; height: 108px; }
        .photo-placeholder {
            font-size: 7.5pt; color: #aaa; text-align: center;
            padding-top: 38px; line-height: 1.5;
        }

        /* Infos */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table tr td { padding: 3px 0; font-size: 9.5pt; }
        .info-label { font-weight: bold; color: #009A44; width: 145px; }
        .info-value { border-bottom: 1px dotted #bbb; padding-bottom: 2px; }

        /* ── Établissement ───────────────────────── */
        .school-block {
            background: #f0faf4; border-left: 4px solid #009A44;
            padding: 8px 12px; margin: 10px 0;
        }
        .school-name   { font-size: 11pt; font-weight: bold; color: #007A33; }
        .school-detail { font-size: 9pt; color: #555; margin-top: 3px; }

        /* ── Notice ──────────────────────────────── */
        .notice {
            background: #fff8f0; border: 1px solid #F77F00;
            padding: 7px 10px; font-size: 8.5pt; color: #7a3c00; text-align: center; margin-top: 10px;
        }

        /* ── Bas de page ─────────────────────────── */
        .bottom { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .bottom td { vertical-align: bottom; }
        .qr-cell { width: 115px; text-align: center; }
        .qr-cell svg { width: 100px; height: 100px; }
        .qr-label { font-size: 7pt; color: #777; margin-top: 4px; }
        .sig-box   { width: 185px; height: 70px; border: 1px solid #ccc; margin-bottom: 5px; }
        .sig-label { font-size: 9pt; font-weight: bold; color: #007A33; }
        .sig-sub   { font-size: 7.5pt; color: #777; margin-top: 2px; }
        .footer    { font-size: 7pt; color: #aaa; text-align: center; margin-top: 16px; }

        /* ── Bandeau bas ─────────────────────────── */
        .footer-band { width: 100%; height: 4px; margin-top: 12px; }
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
                    <span class="header-orange">République de Côte d'Ivoire</span>
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
        <div class="title">Certificat d'Inscription</div>
        <div class="title-bar"></div>
        <div class="academic-year">Année Académique {{ $academicYear->label }}</div>
    </div>

    {{-- Texte intro --}}
    <p class="intro">
        Le Directeur de l'Institut National de Formation Sociale certifie que l'étudiant(e)<br>
        dont les informations figurent ci-dessous est régulièrement inscrit(e)<br>
        pour l'année académique <strong>{{ $academicYear->label }}</strong>.
    </p>

    {{-- Carte étudiant --}}
    <div class="card">
        <div class="matricule-badge">{{ $student->matricule }}</div>
        <table class="card-inner">
            <tr>
                {{-- Photo --}}
                <td class="photo-cell">
                    <div class="photo-box">
                        @if($photo)
                            <img src="{{ $photo }}" alt="Photo étudiant(e)">
                        @else
                            <div class="photo-placeholder">Photo<br>d'identité</div>
                        @endif
                    </div>
                </td>
                {{-- Infos --}}
                <td>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Nom et Prénoms&nbsp;:</td>
                            <td class="info-value">
                                <strong>{{ strtoupper($student->last_name) }} {{ $student->first_name }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">Sexe&nbsp;:</td>
                            <td class="info-value">{{ $student->gender === 'M' ? 'Masculin' : 'Féminin' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Date de naissance&nbsp;:</td>
                            <td class="info-value">{{ $student->date_of_birth?->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Lieu de naissance&nbsp;:</td>
                            <td class="info-value">{{ $student->place_of_birth }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Nationalité&nbsp;:</td>
                            <td class="info-value">{{ $student->nationality }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Qualité&nbsp;:</td>
                            <td class="info-value">{{ $student->status_type }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Mode d'admission&nbsp;:</td>
                            <td class="info-value">
                                @switch($enrollment->quality)
                                    @case('CD') Concours Direct @break
                                    @case('CP') Concours Professionnel @break
                                    @case('FC') Formation Continue @break
                                    @default {{ $enrollment->quality }}
                                @endswitch
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Établissement --}}
    <div class="school-block">
        <div class="school-name">{{ $school->name }} ({{ $school->code }})</div>
        <div class="school-detail">
            Année d'étude : {{ $enrollment->year_of_study }}e année
            @if($enrollment->cycle) &nbsp;|&nbsp; Cycle : {{ $enrollment->cycle }} @endif
            &nbsp;|&nbsp; Date d'inscription : {{ $enrollment->enrollment_date?->format('d/m/Y') }}
        </div>
    </div>

    {{-- Notice --}}
    <div class="notice">
        Ce certificat est valable uniquement pour l'année académique <strong>{{ $academicYear->label }}</strong>.
        Il ne constitue pas une pièce d'identité officielle.
    </div>

    {{-- QR + Signature --}}
    <table class="bottom">
        <tr>
            <td class="qr-cell">
                {!! $qrCode !!}
                <div class="qr-label">Vérification authentique<br>Scanner pour vérifier</div>
            </td>
            <td style="text-align:right;">
                <div class="sig-box"></div>
                <div class="sig-label">Le Directeur de l'INSFS</div>
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
