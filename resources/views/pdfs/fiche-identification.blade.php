<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
  .page { padding: 20px 30px; }

  /* En-tête */
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-left { width: 200px; vertical-align: top; }
  .header-center { text-align: center; vertical-align: middle; }
  .header-right { width: 180px; text-align: right; vertical-align: top; font-size: 11px; }
  .insfs-logo-box { border: 2px solid #000; padding: 6px 10px; display: inline-block; font-weight: bold; font-size: 14px; color: #1a3a6b; }
  .ministere-text { font-size: 10px; line-height: 1.4; }
  .annee-label { font-weight: bold; font-size: 11px; }
  .annee-value { border-bottom: 1px solid #000; min-width: 120px; display: inline-block; }

  .separator { border-top: 2px solid #000; margin: 8px 0; }

  /* Titre */
  .title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin: 12px 0; text-transform: uppercase; }
  .subtitle { text-align: center; font-size: 13px; font-weight: bold; margin-bottom: 15px; }

  /* Corps */
  .content-table { width: 100%; border-collapse: collapse; }
  .content-left { vertical-align: top; padding-right: 20px; }
  .photo-box { border: 1px solid #555; width: 100px; height: 120px; text-align: center; vertical-align: middle; }
  .photo-box img { width: 98px; height: 118px; object-fit: cover; }
  .photo-placeholder { color: #999; font-size: 11px; padding: 10px; }

  .field-row { margin: 10px 0; }
  .field-label { font-weight: bold; font-size: 11px; }
  .field-value { border-bottom: 1px solid #333; display: inline-block; min-width: 280px; padding: 1px 4px; font-size: 12px; }

  .footer { text-align: center; font-size: 9px; color: #555; border-top: 1px solid #ccc; padding-top: 6px; margin-top: 30px; }

  /* Compta header */
  .compta-header { font-style: italic; font-size: 11px; margin-bottom: 5px; }
</style>
</head>
<body>
<div class="page">

  <!-- Compta label -->
  <div class="compta-header">Compta</div>

  <!-- En-tête -->
  <table class="header-table">
    <tr>
      <td class="header-left">
        <div class="ministere-text">
          MINISTERE DE L'EMPLOI<br>
          ET DE LA PROTECTION SOCIALE<br>
          ─────────────────<br>
          INSTITUT NATIONAL SUPERIEUR<br>
          DE FORMATION SOCIALE<br>
          ─────────────────
        </div>
      </td>
      <td class="header-center">
        <div class="insfs-logo-box">INSFS</div>
      </td>
      <td class="header-right">
        REPUBLIQUE DE COTE D'IVOIRE<br>
        <em>Union – Discipline – Travail</em><br>
        ─────────────<br>
        <span class="annee-label">ANNEE ACADEMIQUE</span><br>
        <span class="annee-value">{{ $academicYear->label ?? '─────────' }}</span>
      </td>
    </tr>
  </table>

  <div class="separator"></div>

  <div class="title">Fiche d'Identification</div>
  <div class="subtitle">CARTE D'ETUDIANT INSFS – ANNEE {{ $academicYear->label ?? '' }}</div>

  <!-- Corps : infos + photo -->
  <table class="content-table">
    <tr>
      <td class="content-left">
        <div class="field-row">
          <span class="field-label">Nom :</span>
          <span class="field-value">{{ strtoupper($student->last_name) }}</span>
        </div>
        <div class="field-row">
          <span class="field-label">Prénoms :</span>
          <span class="field-value">{{ $student->first_name }}</span>
        </div>
        <div class="field-row">
          <span class="field-label">Né (e) le :</span>
          <span class="field-value">{{ $student->date_of_birth?->format('d/m/Y') }}</span>
        </div>
        <div class="field-row">
          <span class="field-label">À :</span>
          <span class="field-value">{{ $student->place_of_birth }}</span>
        </div>
        <div class="field-row">
          <span class="field-label">Nationalité :</span>
          <span class="field-value">{{ $student->nationality }}</span>
        </div>
        <div class="field-row">
          <span class="field-label">Inscrit(e) en :</span>
          <span class="field-value">{{ $admitted->year_of_study }}ème année du cycle des {{ $school->name ?? '' }}</span>
        </div>
        <div class="field-row">
          <span class="field-label">Matricule INSFS :</span>
          <span class="field-value">{{ $student->matricule }}</span>
        </div>
      </td>
      <td style="vertical-align: top; width: 110px;">
        <div class="photo-box">
          @if($photo)
            <img src="{{ $photo }}" alt="Photo">
          @else
            <div class="photo-placeholder">Photo</div>
          @endif
        </div>
      </td>
    </tr>
  </table>

  @if($qrCode)
  <div style="text-align: right; margin-top: 10px;">
    <img src="{{ $qrCode }}" width="60" height="60" alt="QR Code">
  </div>
  @endif

  <div class="footer">
    Ministère de l'Emploi et de la Protection Sociale – Institut National Supérieur de Formation Sociale :
    Cocody Boulevard de l'Université / Tél : 27 22 44 16 75 / Mail : infsofficiel@gmail.com / 01 BP 2625 ABJ 01
  </div>
</div>
</body>
</html>