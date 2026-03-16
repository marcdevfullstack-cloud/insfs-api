<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
  .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: #059669; color: #fff; padding: 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 20px; }
  .badge { display: inline-block; background: rgba(255,255,255,.2); padding: 4px 14px; border-radius: 12px; font-size: 13px; margin-top: 8px; }
  .body { padding: 30px; color: #333; line-height: 1.6; }
  .info-box { background: #f0fdf4; border-left: 4px solid #059669; padding: 15px 20px; border-radius: 4px; margin: 20px 0; }
  .btn { display: inline-block; background: #1a3a6b; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin: 20px 0; }
  .footer { background: #f0f0f0; padding: 15px 30px; font-size: 12px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Votre dossier a été validé — INSFS</h1>
    <span class="badge">Fiche d'identification disponible</span>
  </div>
  <div class="body">
    <p>Bonjour <strong>{{ $portalUser->first_name }} {{ $portalUser->last_name }}</strong>,</p>
    <p>Nous avons le plaisir de vous informer que votre dossier a été <strong>validé</strong> par le Service de la Scolarité.</p>
    <div class="info-box">
      <strong>Prochaine étape — Paiements :</strong><br>
      Votre <strong>Fiche d'identification</strong> est maintenant disponible sur votre espace. Téléchargez-la et présentez-la à la <strong>Comptabilité</strong> pour effectuer vos paiements (frais d'inscription et frais de scolarité).
    </div>
    <a href="{{ env('PORTAL_URL', 'http://localhost:3001') }}/espace/documents" class="btn">Télécharger ma fiche d'identification</a>
    <p>Cordialement,<br><strong>Le Service de la Scolarité — INSFS</strong></p>
  </div>
  <div class="footer">
    INSFS — Cocody Boulevard de l'Université | Tél : 27 22 44 16 75 | infsofficiel@gmail.com
  </div>
</div>
</body>
</html>
