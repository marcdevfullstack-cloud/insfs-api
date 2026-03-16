<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
  .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: #d97706; color: #fff; padding: 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 20px; }
  .body { padding: 30px; color: #333; line-height: 1.6; }
  .correction-item { background: #fff7ed; border-left: 4px solid #d97706; padding: 12px 16px; margin: 8px 0; border-radius: 4px; }
  .correction-item strong { display: block; color: #92400e; }
  .btn { display: inline-block; background: #d97706; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin: 20px 0; }
  .footer { background: #f0f0f0; padding: 15px 30px; font-size: 12px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Action requise sur votre dossier — INSFS</h1>
  </div>
  <div class="body">
    <p>Bonjour <strong>{{ $portalUser->first_name }} {{ $portalUser->last_name }}</strong>,</p>
    <p>Après examen de votre dossier, le Service de la Scolarité a identifié des informations à corriger ou compléter avant de pouvoir procéder à votre inscription.</p>
    <p><strong>Corrections à apporter :</strong></p>
    @if($application->correction_fields)
      @foreach($application->correction_fields as $correction)
        <div class="correction-item">
          <strong>{{ $correction['field'] ?? 'Champ à corriger' }}</strong>
          {{ $correction['message'] ?? '' }}
        </div>
      @endforeach
    @endif
    <p>Connectez-vous à votre espace pour effectuer les corrections :</p>
    <a href="{{ env('PORTAL_URL', 'http://localhost:3001') }}/espace/dossier" class="btn">Corriger mon dossier</a>
    <p>Cordialement,<br><strong>Le Service de la Scolarité — INSFS</strong></p>
  </div>
  <div class="footer">
    INSFS — Cocody Boulevard de l'Université | Tél : 27 22 44 16 75 | infsofficiel@gmail.com
  </div>
</div>
</body>
</html>
