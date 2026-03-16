<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
  .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: #dc2626; color: #fff; padding: 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 20px; }
  .body { padding: 30px; color: #333; line-height: 1.6; }
  .reason-box { background: #fef2f2; border-left: 4px solid #dc2626; padding: 15px 20px; border-radius: 4px; margin: 20px 0; }
  .footer { background: #f0f0f0; padding: 15px 30px; font-size: 12px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Décision sur votre dossier — INSFS</h1>
  </div>
  <div class="body">
    <p>Bonjour <strong>{{ $portalUser->first_name }} {{ $portalUser->last_name }}</strong>,</p>
    <p>Après examen de votre dossier d'inscription, le Service de la Scolarité a pris la décision suivante :</p>
    <div class="reason-box">
      <strong>Motif du rejet :</strong><br>
      {{ $application->rejection_reason }}
    </div>
    <p>Pour toute question ou réclamation, veuillez contacter directement le Service de la Scolarité :</p>
    <ul>
      <li>Téléphone : 27 22 44 16 75</li>
      <li>Email : infsofficiel@gmail.com</li>
      <li>Adresse : Cocody Boulevard de l'Université</li>
    </ul>
    <p>Cordialement,<br><strong>Le Service de la Scolarité — INSFS</strong></p>
  </div>
  <div class="footer">
    INSFS — Cocody Boulevard de l'Université | Tél : 27 22 44 16 75 | infsofficiel@gmail.com
  </div>
</div>
</body>
</html>