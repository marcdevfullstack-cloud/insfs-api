<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
  .page { padding: 20px 30px; }

  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .ministere-text { font-size: 10px; line-height: 1.4; }
  .insfs-logo-box { border: 2px solid #000; padding: 6px 10px; display: inline-block; font-weight: bold; font-size: 14px; color: #1a3a6b; }
  .separator { border-top: 2px solid #000; margin: 8px 0; }

  .service-label { font-style: italic; font-size: 11px; margin-bottom: 4px; }
  .title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin: 12px 0; text-transform: uppercase; }

  .field-row { margin: 8px 0; }
  .field-label { font-weight: bold; font-size: 11px; }
  .field-value { border-bottom: 1px solid #333; display: inline-block; min-width: 250px; padding: 1px 4px; }

  /* Tableau des services */
  .services-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
  .services-table th, .services-table td { border: 1px solid #000; padding: 8px 10px; vertical-align: top; }
  .services-table th { background: #f0f0f0; font-weight: bold; text-align: center; }
  .service-name { font-weight: bold; text-decoration: underline; }
  .service-agent { font-size: 11px; color: #444; margin-top: 4px; }

  .note { font-size: 11px; font-style: italic; margin: 10px 0; }

  .signature-section { margin-top: 30px; text-align: right; }
  .signature-label { font-weight: bold; text-decoration: underline; font-size: 12px; }
  .signature-img { margin-top: 5px; }

  .footer { text-align: center; font-size: 9px; color: #555; border-top: 1px solid #ccc; padding-top: 6px; margin-top: 20px; }
</style>
</head>
<body>
<div class="page">

  <div class="service-label">Scolarite</div>

  <!-- En-tête -->
  <table class="header-table">
    <tr>
      <td style="width:200px; vertical-align:top;">
        <div class="insfs-logo-box">INSFS</div><br>
        <div class="ministere-text">
          INSTITUT NATIONAL SUPERIEUR<br>
          DE DE FORMATION SOCIALE
        </div>
      </td>
      <td style="text-align:center; vertical-align:middle;">
        &nbsp;
      </td>
      <td style="width:180px; text-align:right; vertical-align:top; font-size:11px;">
        REPUBLIQUE DE COTE D'IVOIRE<br>
        <em>Union – Discipline – Travail</em><br>
        ─────────────<br>
        <strong>ANNEE ACADEMIQUE</strong><br>
        {{ $academicYear->label ?? '────────────' }}
      </td>
    </tr>
  </table>

  <div style="text-align:center; font-size:11px; font-weight:bold; margin-bottom:6px;">SERVICE DE LA SCOLARITE</div>
  <div class="separator"></div>

  <div class="title">Fiche d'Inscription</div>

  <!-- Champs étudiant -->
  <div class="field-row">
    <span class="field-label">Nom et Prénoms :</span>
    <span class="field-value">{{ strtoupper($student->last_name) }} {{ $student->first_name }}</span>
  </div>
  <div class="field-row">
    <span class="field-label">Né (e) le :</span>
    <span class="field-value">{{ $student->date_of_birth?->format('d/m/Y') }}</span>
    &nbsp;&nbsp;
    <span class="field-label">à :</span>
    <span class="field-value">{{ $student->place_of_birth }}</span>
  </div>
  <div class="field-row">
    <span class="field-label">Nationalité :</span>
    <span class="field-value">{{ $student->nationality }}</span>
    &nbsp;&nbsp;
    <span class="field-label">Qualité :</span>
    <strong>{{ $admitted->entry_mode === 'Concours direct' ? 'CD' : ($admitted->entry_mode === 'Concours professionnel' ? 'CP' : 'FC') }}</strong>
  </div>
  <div class="field-row">
    <span class="field-label">Ecole :</span>
    <span class="field-value">{{ $school->name }}</span>
    &nbsp; ;
    <span class="field-label">{{ $admitted->year_of_study }}ème</span> année du cycle des :
    <span class="field-value">{{ $school->name }}</span>
  </div>
  <div class="field-row">
    <span class="field-label">Matricule INSFS :</span>
    <span class="field-value">{{ $student->matricule }}</span>
  </div>

  <!-- Tableau des services -->
  <table class="services-table">
    <thead>
      <tr>
        <th style="width:30%">SERVICES</th>
        <th style="width:40%">OBSERVATIONS</th>
        <th style="width:30%">VISA</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <div class="service-name">COMPTABILITE</div>
          <div class="service-agent">
            Agent : {{ $enrollment ? '________________' : '________________' }}<br>
            Date : {{ $enrollment ? now()->format('d/m/Y') : '________________' }}
          </div>
        </td>
        <td>
          Frais d'inscription : {{ $enrollment ? number_format($enrollment->payments->where('payment_type', 'FRAIS_INSCRIPTION')->sum('amount'), 0, ',', ' ') . ' F CFA' : '________________' }}<br><br>
          Frais de scolarité : {{ $enrollment ? number_format($enrollment->payments->where('payment_type', 'FRAIS_SCOLARITE')->sum('amount'), 0, ',', ' ') . ' F CFA' : '________________' }}
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>
          <div class="service-name">SCOLARITE</div>
          <div class="service-agent">
            Agent : {{ $scolariteSignature?->signer_name ?? '________________' }}<br>
            Date : {{ $scolariteSignature?->signed_at?->format('d/m/Y') ?? '________________' }}
          </div>
        </td>
        <td>
          Pièces fournies :<br>
          ……………………………………………<br>
          ……………………………………………<br>
          ……………………………………………
        </td>
        <td>
          @if($scolariteSignature?->signature_image)
            <img src="{{ $scolariteSignature->signature_image }}" height="50" alt="Signature Scolarité">
          @else
            &nbsp;
          @endif
        </td>
      </tr>
    </tbody>
  </table>

  <p class="note"><strong>N.B.</strong> : A classer dans le dossier de l'intéressé (e).</p>

  <div class="signature-section">
    <p>Abidjan, le {{ now()->format('d/m/Y') }}</p>
    <br>
    <div class="signature-label">LE SERVICE DE LA SCOLARITE</div>
    @if($scolariteSignature?->signature_image)
      <div class="signature-img">
        <img src="{{ $scolariteSignature->signature_image }}" height="60" alt="Signature">
      </div>
    @endif
  </div>

  <div class="footer">
    Ministère de l'Emploi et de la Protection Sociale – Institut National Supérieur de Formation Sociale :
    Cocody Boulevard de l'Université / Tél : 27 22 44 16 75 / Mail : infsofficiel@gmail.com / 01 BP 2625 ABJ 01
  </div>
</div>
</body>
</html>