<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
  .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: #1a3a6b; color: #fff; padding: 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 20px; }
  .badge { display: inline-block; background: #059669; padding: 4px 14px; border-radius: 12px; font-size: 13px; margin-top: 8px; }
  .body { padding: 30px; color: #333; line-height: 1.6; }
  .doc-list { background: #eff6ff; border-radius: 6px; padding: 15px 20px; margin: 20px 0; }
  .doc-list li { margin: 6px 0; }
  .btn { display: inline-block; background: #1a3a6b; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin: 20px 0; }
  .footer { background: #f0f0f0; padding: 15px 30px; font-size: 12px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Institut National Supérieur de Formation Sociale</h1>
    <span class="badge">✓ Inscription complète</span>
  </div>
  <div class="body">
    <p>Bonjour <strong>{{ $portalUser->first_name }} {{ $portalUser->last_name }}</strong>,</p>
    <p>Félicitations ! Votre dossier d'inscription est maintenant <strong>complet</strong>. Tous vos documents officiels sont disponibles sur votre espace étudiant.</p>
    <div class="doc-list">
      <strong>Documents disponibles :</strong>
      <ul>
        <li>Fiche d'identification</li>
        <li>Fiche d'inscription (signée par la Scolarité)</li>
        <li>Certificat d'inscription (signé par le Chef du Service)</li>
      </ul>
    </div>
    @if($application->student?->matricule)
    <p>Votre <strong>Matricule INSFS : {{ $application->student->matricule }}</strong></p>
    @endif
    <a href="{{ env('PORTAL_URL', 'http://localhost:3001') }}/espace/documents" class="btn">Accéder à mes documents</a>
    <p>Bienvenue à l'INSFS ! Nous vous souhaitons une excellente année académique.</p>
    <p>Cordialement,<br><strong>Le Service de la Scolarité — INSFS</strong></p>
  </div>
  <div class="footer">
    INSFS — Cocody Boulevard de l'Université | Tél : 27 22 44 16 75 | infsofficiel@gmail.com
  </div>
</div>
</body>
</html>