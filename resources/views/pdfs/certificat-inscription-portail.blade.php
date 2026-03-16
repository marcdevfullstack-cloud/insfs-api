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
  .separator { border-top: 2px solid #000; margin: 8px 0; }

  .service-label { font-style: italic; font-size: 11px; margin-bottom: 4px; }
  .title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin: 20px 0 25px; text-transform: uppercase; }

  .content-table { width: 100%; border-collapse: collapse; }
  .content-left { vertical-align: top; padding-right: 20px; line-height: 2.2; font-size: 13px; }
  .photo-box { border: 1px solid #555; width: 110px; height: 130px; text-align: center; vertical-align: middle; }
  .photo-box img { width: 108px; height: 128px; object-fit: cover; }
  .photo-placeholder { color: #999; font-size: 11px; padding: 10px; }

  .dot-line { border-bottom: 1px dotted #333; display: inline-block; min-width: 180px; }

  .signature-section { margin-top: 40px; text-align: right; }
  .signature-label { font-weight: bold; text-decoration: underline; font-size: 13px; }

  .footer { text-align: center; font-size: 9px; color: #555; border-top: 1px solid #ccc; padding-top: 6px; margin-top: 30px; }

  .qr-block { margin-top: 10px; }
</style>
</head>
<body>
<div class="page">

  <div class="service-label">sColorite</div>

  <!-- En-tête -->
  <table class="header-table">
    <tr>
      <td style="width:200px; vertical-align:top; font-size:10px; line-height:1.5;">
        MINISTERE DE L'EMPLOI<br>
        ET DE LA PROTECTION SOCIALE<br>
        ─────────────────<br>
        STITUT NATIONAL SUPERIEUR<br>
        DE FORMATION SOCIALE<br>
        ─────────────────
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

  <div class="separator"></div>

  <div class="title">Certificat d'Inscription</div>

  <!-- Corps : texte + photo -->
  <table class="content-table">
    <tr>
      <td class="content-left">
        <p>Le Chef de Service de la Scolarité certifie que :</p>
        <p>
          l'étudiant (e) <span class="dot-line">{{ strtoupper($application->last_name) }} {{ $application->first_name }}</span>
        </p>
        <p>
          né (e) le <span class="dot-line">{{ $application->date_of_birth?->format('d/m/Y') }}</span>
          à <span class="dot-line">{{ $application->place_of_birth }}</span>
        </p>
        <p>
          est inscrit (e) en
          <strong>{{ $admitted->year_of_study }}ème</strong> année du cycle des
          <span class="dot-line">{{ $school->name }}</span>
        </p>
        <p>
          Matricule INSFS <span class="dot-line">{{ $student?->matricule }}</span>
          pour l'année scolaire <span class="dot-line">{{ $academicYear->label }}</span>
        </p>
        <br>
        <p style="font-size:12px; line-height:1.6;">
          En foi de quoi, le présent certificat lui est délivré pour servir et valoir ce que de droit.
        </p>
      </td>
      <td style="vertical-align: top; width: 120px;">
        <div class="photo-box">
          @if($photo)
            <img src="{{ $photo }}" alt="PHOTO">
          @else
            <div class="photo-placeholder">PHOTO</div>
          @endif
        </div>
        @if($qrCode)
          <div class="qr-block">
            <img src="{{ $qrCode }}" width="60" height="60" alt="QR Code">
          </div>
        @endif
      </td>
    </tr>
  </table>

  <!-- Signature -->
  <div class="signature-section">
    <p>Abidjan, le {{ now()->format('d/m/Y') }}</p>
    <br>
    <div class="signature-label">LE CHEF DU SERVICE DE LA SCOLARITE</div>
    @if($chefSignature?->signature_image)
      <div style="margin-top: 8px;">
        <img src="{{ $chefSignature->signature_image }}" height="70" alt="Signature Chef">
      </div>
      <div style="font-size: 11px; color: #555; margin-top: 4px;">
        {{ $chefSignature->signer_name }}<br>
        Signé le {{ $chefSignature->signed_at?->format('d/m/Y à H:i') }}
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