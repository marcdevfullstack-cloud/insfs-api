<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
  .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: #1a3a6b; color: #fff; padding: 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 20px; }
  .badge { display: inline-block; background: #f59e0b; color: #fff; padding: 4px 14px; border-radius: 12px; font-size: 13px; margin-top: 8px; }
  .body { padding: 30px; color: #333; line-height: 1.6; }
  .info-box { background: #eff6ff; border-left: 4px solid #1a3a6b; padding: 15px 20px; border-radius: 4px; margin: 20px 0; }
  .footer { background: #f0f0f0; padding: 15px 30px; font-size: 12px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Institut National Supérieur de Formation Sociale</h1>
    <span class="badge">Dossier reçu</span>
  </div>
  <div class="body">
    <p>Bonjour <strong>{{ $portalUser->first_name }} {{ $portalUser->last_name }}</strong>,</p>
    <p>Nous confirmons la bonne réception de votre dossier d'inscription soumis le <strong>{{ $application->submitted_at?->format('d/m/Y à H:i') }}</strong>.</p>
    <div class="info-box">
      <strong>Prochaine étape :</strong> Le Service de la Scolarité va examiner votre dossier. Vous serez notifié(e) par email dès qu'une décision est prise. Ce délai peut prendre quelques jours ouvrables.
    </div>
    <p>Vous pouvez suivre l'avancement de votre dossier en vous connectant à votre espace :</p>
    <p><a href="{{ env('PORTAL_URL', 'http://localhost:3001') }}/espace">{{ env('PORTAL_URL', 'http://localhost:3001') }}/espace</a></p>
    <p>Cordialement,<br><strong>Le Service de la Scolarité — INSFS</strong></p>
  </div>
  <div class="footer">
    INSFS — Cocody Boulevard de l'Université | Tél : 27 22 44 16 75 | infsofficiel@gmail.com
  </div>
</div>
</body>
</html>
