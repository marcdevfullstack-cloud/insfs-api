<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
  .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: #1a3a6b; color: #fff; padding: 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 20px; }
  .header p { margin: 5px 0 0; font-size: 13px; opacity: .8; }
  .body { padding: 30px; color: #333; line-height: 1.6; }
  .btn { display: inline-block; background: #1a3a6b; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin: 20px 0; }
  .footer { background: #f0f0f0; padding: 15px 30px; font-size: 12px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Institut National Supérieur de Formation Sociale</h1>
    <p>Portail Étudiant — Bienvenue</p>
  </div>
  <div class="body">
    <p>Bonjour <strong>{{ $portalUser->first_name }} {{ $portalUser->last_name }}</strong>,</p>
    <p>Bienvenue sur le <strong>Portail Étudiant de l'INSFS</strong> !</p>
    <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter et commencer à remplir vos fiches d'inscription.</p>
    <p>Pour accéder à votre espace :</p>
    <a href="{{ env('PORTAL_URL', 'http://localhost:3001') }}/connexion" class="btn">Accéder à mon espace</a>
    <p>Votre email de connexion : <strong>{{ $portalUser->email }}</strong></p>
    <p><strong>Documents à remplir obligatoirement :</strong></p>
    <ul>
      <li>Fiche de renseignements (avec photo d'identité)</li>
      <li>Fiche d'engagement (avec signature numérique)</li>
      <li>Pièces justificatives (CNI, extrait de naissance, diplômes...)</li>
    </ul>
    <p>Cordialement,<br><strong>Le Service de la Scolarité — INSFS</strong></p>
  </div>
  <div class="footer">
    INSFS — Cocody Boulevard de l'Université | Tél : 27 22 44 16 75 | infsofficiel@gmail.com
  </div>
</div>
</body>
</html>
