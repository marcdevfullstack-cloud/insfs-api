<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de Renseignements — {{ $student->matricule }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1a1a1a;
            background: white;
        }
        .page { padding: 8mm 16mm 10mm; }

        /* ── Drapeau ─────────────────────────────── */
        .flag-table { width: 100%; border-collapse: collapse; height: 5px; margin-bottom: 8px; }
        .fo { background: #F77F00; width: 33.3%; }
        .fw { background: #FFFFFF; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; width: 33.3%; }
        .fg { background: #009A44; width: 33.3%; }

        /* ── En-tête ─────────────────────────────── */
        .header { width: 100%; border-collapse: collapse; border-bottom: 3px solid #009A44; margin-bottom: 8px; }
        .header td { vertical-align: top; padding-bottom: 6px; }
        .logo-text    { font-size: 18pt; font-weight: bold; color: #009A44; }
        .logo-sub     { font-size: 7.5pt; color: #555; margin-top: 2px; }
        .logo-ministry { font-size: 7pt; color: #444; margin-top: 3px; line-height: 1.5; }
        .header-right { text-align: right; font-size: 7pt; color: #666; line-height: 1.7; }
        .ci-flag-inline { color: #F77F00; font-weight: bold; }

        /* ── Titre + matricule ───────────────────── */
        .title-block { text-align: center; margin: 8px 0 6px; }
        .title     { font-size: 14pt; font-weight: bold; color: #009A44; text-transform: uppercase; letter-spacing: 1px; }
        .title-bar { width: 60px; height: 3px; background: #F77F00; margin: 5px auto; }
        .matricule-band {
            text-align: center; background: #009A44; color: white;
            font-size: 13pt; font-weight: bold; letter-spacing: 2px;
            padding: 5px; margin-bottom: 8px;
        }

        /* ── Photo dans un tableau ───────────────── */
        .header-block { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header-block .info-col { vertical-align: top; }
        .header-block .photo-col { width: 88px; vertical-align: top; padding-left: 10px; }
        .photo-box {
            width: 86px; height: 106px;
            border: 2px solid #009A44;
            background: #f5f5f5; overflow: hidden;
        }
        .photo-box img { width: 86px; height: 106px; }
        .photo-placeholder { font-size: 7pt; color: #aaa; text-align: center; padding-top: 35px; line-height: 1.5; }

        /* ── Sections ────────────────────────────── */
        .section { margin-bottom: 10px; }
        .section-title {
            font-size: 9.5pt; font-weight: bold; color: white;
            background: #009A44; padding: 3px 10px; margin-bottom: 5px;
        }

        /* ── Grille champs ───────────────────────── */
        .fields { width: 100%; border-collapse: collapse; }
        .fields td { vertical-align: top; padding: 2.5px 4px; font-size: 9pt; }
        .fl { font-weight: bold; color: #007A33; width: 130px; }
        .fv { border-bottom: 1px dotted #bbb; padding-bottom: 2px; min-width: 80px; }

        /* ── Diplômes ────────────────────────────── */
        .diploma-row td { padding: 3px 8px; font-size: 9pt; }
        .ok  { color: #009A44; font-weight: bold; }
        .nok { color: #ccc; }

        /* ── Tableau inscriptions ─────────────────── */
        .enroll-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        .enroll-table th {
            background: #009A44; color: white;
            padding: 4px 7px; text-align: left; font-weight: normal;
        }
        .enroll-table td { padding: 4px 7px; border-bottom: 1px solid #e8e8e8; }
        .enroll-table tr:nth-child(even) td { background: #f5faf5; }
        .badge { padding: 2px 7px; font-size: 7.5pt; font-weight: bold; }
        .badge-EN_COURS { background: #fff3cd; color: #856404; }
        .badge-VALIDE   { background: #d4edda; color: #155724; }
        .badge-ANNULE   { background: #f8d7da; color: #721c24; }

        /* ── Bas de page ─────────────────────────── */
        .bottom { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .bottom td { vertical-align: bottom; }
        .qr-cell { width: 105px; text-align: center; }
        .qr-cell img { width: 88px; height: 88px; display: block; margin: 0 auto; }
        .qr-label { font-size: 6.5pt; color: #777; margin-top: 3px; }
        .sig-box   { width: 180px; height: 62px; border: 1px solid #ccc; margin-bottom: 5px; }
        .sig-label { font-size: 9pt; font-weight: bold; color: #007A33; }
        .sig-sub   { font-size: 7.5pt; color: #777; margin-top: 2px; }
        .footer    { font-size: 6.5pt; color: #aaa; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
<div class="page">

    {{-- Drapeau haut --}}
    <table class="flag-table"><tr>
        <td class="fo"></td><td class="fw"></td><td class="fg"></td>
    </tr></table>

    {{-- En-tête --}}
    <table class="header">
        <tr>
            <td style="width:62%;">
                <div class="logo-text">INSFS</div>
                <div class="logo-sub">Institut National de Formation Sociale</div>
                <div class="logo-ministry">
                    Ministère de la Femme, de la Famille et de l'Enfant<br>
                    <span class="ci-flag-inline">République de Côte d'Ivoire</span>
                </div>
            </td>
            <td class="header-right">
                Liberté — Égalité — Fraternité<br><br>
                Le {{ $generatedAt->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <div class="title-block">
        <div class="title">Fiche de Renseignements de l'Étudiant</div>
        <div class="title-bar"></div>
    </div>

    <div class="matricule-band">{{ $student->matricule }}</div>

    {{-- I. Informations personnelles (avec photo en colonne de droite) --}}
    <div class="section">
        <div class="section-title">I. Informations Personnelles</div>
        <table class="header-block">
            <tr>
                <td class="info-col">
                    <table class="fields">
                        <tr>
                            <td><span class="fl">Nom :</span></td>
                            <td><span class="fv">{{ strtoupper($student->last_name) }}</span></td>
                            <td><span class="fl">Prénoms :</span></td>
                            <td><span class="fv">{{ $student->first_name }}</span></td>
                        </tr>
                        <tr>
                            <td><span class="fl">Sexe :</span></td>
                            <td><span class="fv">{{ $student->gender === 'M' ? 'Masculin' : 'Féminin' }}</span></td>
                            <td><span class="fl">Sit. matrimoniale :</span></td>
                            <td><span class="fv">{{ $student->marital_status }}</span></td>
                        </tr>
                        <tr>
                            <td><span class="fl">Date de naissance :</span></td>
                            <td><span class="fv">{{ $student->date_of_birth?->format('d/m/Y') }}</span></td>
                            <td><span class="fl">Lieu de naissance :</span></td>
                            <td><span class="fv">{{ $student->place_of_birth }}</span></td>
                        </tr>
                        <tr>
                            <td><span class="fl">Nationalité :</span></td>
                            <td><span class="fv">{{ $student->nationality }}</span></td>
                            <td><span class="fl">Nb. d'enfants :</span></td>
                            <td><span class="fv">{{ $student->children_count }}</span></td>
                        </tr>
                        <tr>
                            <td><span class="fl">Père :</span></td>
                            <td><span class="fv">{{ $student->father_name ?? '—' }}</span></td>
                            <td><span class="fl">Mère :</span></td>
                            <td><span class="fv">{{ $student->mother_name ?? '—' }}</span></td>
                        </tr>
                        <tr>
                            <td><span class="fl">Téléphone :</span></td>
                            <td><span class="fv">{{ $student->phone ?? '—' }}</span></td>
                            <td><span class="fl">Email :</span></td>
                            <td><span class="fv">{{ $student->email ?? '—' }}</span></td>
                        </tr>
                        <tr>
                            <td><span class="fl">Adresse :</span></td>
                            <td colspan="3"><span class="fv">{{ $student->address ?? '—' }}</span></td>
                        </tr>
                    </table>
                </td>
                {{-- Photo en colonne de droite --}}
                <td class="photo-col">
                    <div class="photo-box">
                        @if($photo)
                            <img src="{{ $photo }}" alt="Photo étudiant(e)">
                        @else
                            <div class="photo-placeholder">Photo<br>d'identité</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- II. Statut --}}
    <div class="section">
        <div class="section-title">II. Statut &amp; Situation Professionnelle</div>
        <table class="fields">
            <tr>
                <td><span class="fl">Qualité :</span></td>
                <td><span class="fv">{{ $student->status_type }}</span></td>
                <td><span class="fl">Mode d'entrée :</span></td>
                <td><span class="fv">{{ $student->entry_mode }}</span></td>
            </tr>
            @if($student->status_type === 'Fonctionnaire')
            <tr>
                <td><span class="fl">Mat. fonctionnaire :</span></td>
                <td><span class="fv">{{ $student->matricule_fonctionnaire ?? '—' }}</span></td>
                <td><span class="fl">Emploi :</span></td>
                <td><span class="fv">{{ $student->emploi ?? '—' }}</span></td>
            </tr>
            <tr>
                <td><span class="fl">Catégorie :</span></td>
                <td><span class="fv">{{ $student->categorie ?? '—' }}</span></td>
                <td><span class="fl">Échelon / Classe :</span></td>
                <td><span class="fv">{{ $student->echelon ?? '—' }} / {{ $student->classe ?? '—' }}</span></td>
            </tr>
            @endif
        </table>
    </div>

    {{-- III. Diplômes --}}
    <div class="section">
        <div class="section-title">III. Diplômes et Titres</div>
        <table class="diploma-row">
            <tr>
                <td class="{{ $student->diploma_cepe ? 'ok' : 'nok' }}">
                    {{ $student->diploma_cepe ? '✔' : '✘' }} CEPE
                </td>
                <td class="{{ $student->diploma_bepc ? 'ok' : 'nok' }}">
                    {{ $student->diploma_bepc ? '✔' : '✘' }} BEPC
                </td>
                <td class="{{ $student->diploma_bac ? 'ok' : 'nok' }}">
                    {{ $student->diploma_bac ? '✔' : '✘' }} BAC
                    @if($student->diploma_bac && $student->diploma_bac_serie)
                        (série {{ $student->diploma_bac_serie }})
                    @endif
                </td>
                @if($student->other_diplomas)
                <td class="ok">Autres : {{ $student->other_diplomas }}</td>
                @endif
            </tr>
        </table>
    </div>

    {{-- IV. Inscriptions --}}
    <div class="section">
        <div class="section-title">IV. Historique des Inscriptions</div>
        @if($enrollments->isEmpty())
            <p style="color:#777;font-size:9pt;padding:5px 0;">Aucune inscription enregistrée.</p>
        @else
        <table class="enroll-table">
            <thead>
                <tr>
                    <th>Année</th>
                    <th>École</th>
                    <th>Année d'étude</th>
                    <th>Qualité</th>
                    <th>Statut</th>
                    <th style="text-align:right;">Payé (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments->sortBy('enrollment_date') as $enr)
                <tr>
                    <td>{{ $enr->academicYear?->label ?? '—' }}</td>
                    <td>{{ $enr->school?->code ?? '—' }}</td>
                    <td style="text-align:center;">{{ $enr->year_of_study }}e</td>
                    <td>
                        @switch($enr->quality)
                            @case('CD') Concours Direct @break
                            @case('CP') Concours Prof. @break
                            @case('FC') Formation Continue @break
                            @default {{ $enr->quality }}
                        @endswitch
                    </td>
                    <td>
                        <span class="badge badge-{{ $enr->status }}">
                            @switch($enr->status)
                                @case('EN_COURS') En cours @break
                                @case('VALIDE') Validée @break
                                @case('ANNULE') Annulée @break
                                @default {{ $enr->status }}
                            @endswitch
                        </span>
                    </td>
                    <td style="text-align:right;">
                        {{ number_format($enr->payments->sum('amount'), 0, ',', ' ') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Bas de page --}}
    <table class="bottom">
        <tr>
            <td class="qr-cell">
                <img src="{{ $qrCode }}" alt="QR Code" width="88" height="88">
                <div class="qr-label">Code de vérification INSFS</div>
            </td>
            <td style="text-align:right;">
                <div class="sig-box"></div>
                <div class="sig-label">Le Directeur de l'INSFS</div>
                <div class="sig-sub">Signature et cachet officiel</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Fiche générée le {{ $generatedAt->format('d/m/Y à H:i') }} — INSFS Gestion des Inscriptions
    </div>

    {{-- Drapeau bas --}}
    <table class="flag-table" style="margin-top:8px;"><tr>
        <td class="fo"></td><td class="fw"></td><td class="fg"></td>
    </tr></table>

</div>
</body>
</html>
