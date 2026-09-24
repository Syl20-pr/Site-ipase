<?php
// ============================================================
//  api/contact.php — Réception du formulaire de contact IPASE
// ============================================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../mail/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données JSON invalides.']);
    exit;
}

// Nettoyage et validation
$nom      = trim($data['nom']      ?? '');
$prenom   = trim($data['prenom']   ?? '');
$email    = trim($data['email']    ?? '');
$tel      = trim($data['telephone'] ?? '');
$objet    = trim($data['objet']    ?? '');
$message  = trim($data['message']  ?? '');

$errors = [];
if (empty($nom))     $errors[] = 'Le champ Nom est requis.';
if (empty($prenom))  $errors[] = 'Le champ Prénom est requis.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
if (empty($objet))   $errors[] = 'L\'objet est requis.';
if (empty($message)) $errors[] = 'Le message est requis.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$nomComplet = $prenom . ' ' . $nom;

$sujet = '[Contact IPASE] ' . htmlspecialchars($objet);

$html = '<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><style>
body{font-family:Inter,Arial,sans-serif;background:#F8FAFC;color:#0F172A;margin:0;padding:0;}
.wrapper{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.06);}
.header{background:#00205B;padding:28px 32px;text-align:center;}
.header h1{color:#fff;margin:0;font-size:22px;}
.header p{color:#CBD5E1;margin:6px 0 0;font-size:13px;}
.body{padding:32px;}
.field{margin-bottom:16px;padding:14px 16px;background:#F1F5F9;border-radius:8px;border-left:4px solid #C8102E;}
.field strong{display:block;color:#00205B;font-size:12px;text-transform:uppercase;margin-bottom:4px;}
.field span{font-size:15px;color:#1e293b;}
.footer{background:#00205B;padding:20px 32px;text-align:center;font-size:12px;color:#94A3B8;}
</style></head>
<body><div class="wrapper">
<div class="header">
  <h1>📬 Nouveau message de contact</h1>
  <p>Institut Professionnel Action Santé Éducation</p>
</div>
<div class="body">
  <div class="field"><strong>Expéditeur</strong><span>' . htmlspecialchars($nomComplet) . '</span></div>
  <div class="field"><strong>Email</strong><span><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></span></div>
  ' . (!empty($tel) ? '<div class="field"><strong>Téléphone</strong><span>' . htmlspecialchars($tel) . '</span></div>' : '') . '
  <div class="field"><strong>Objet</strong><span>' . htmlspecialchars($objet) . '</span></div>
  <div class="field"><strong>Message</strong><span>' . nl2br(htmlspecialchars($message)) . '</span></div>
</div>
<div class="footer">IPASE — Lomé Agbalepedo | ipase.tg@gmail.com | +228 93 88 23 52</div>
</div></body></html>';

try {
    $pdo = getConnexion();
    $pdo->prepare(
        "INSERT INTO contact_messages (nom, prenom, email, telephone, objet, message, created_at)
         VALUES (:nom, :prenom, :email, :telephone, :objet, :message, NOW())"
    )->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':telephone' => $tel,
        ':objet' => $objet,
        ':message' => $message,
    ]);
} catch (Throwable $e) {
    // On ne bloque pas l’envoi si l’enregistrement inbox échoue, pour ne pas casser le flux de contact.
}

// Envoyer le mail à l'adresse IPASE
$sent = envoyerEmail(
    EMAIL_SUPPORT,
    'Service Contact IPASE',
    $sujet,
    $html
);

// Envoyer un accusé de réception à l'expéditeur
$htmlAccuse = '<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><style>
body{font-family:Inter,Arial,sans-serif;background:#F8FAFC;color:#0F172A;margin:0;padding:0;}
.wrapper{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.06);}
.header{background:#00205B;padding:28px 32px;text-align:center;}
.header h1{color:#fff;margin:0;font-size:20px;}
.body{padding:32px;line-height:1.7;}
.footer{background:#00205B;padding:20px 32px;text-align:center;font-size:12px;color:#94A3B8;}
</style></head>
<body><div class="wrapper">
<div class="header"><h1>✅ Message bien reçu, ' . htmlspecialchars($prenom) . ' !</h1></div>
<div class="body">
  <p>Merci pour votre message. Notre équipe vous répondra dans les <strong>24 heures ouvrées</strong>.</p>
  <p>Pour toute urgence, vous pouvez nous joindre directement :</p>
  <ul>
    <li>📞 Secrétariat : +228 71 32 90 92</li>
    <li>💬 WhatsApp : +228 93 88 23 52</li>
    <li>📧 Email : ipase.tg@gmail.com</li>
  </ul>
  <p style="margin-top:24px;font-style:italic;color:#64748b;">« Osez Grand, Osez IPASE, Osez l\'Excellence »</p>
</div>
<div class="footer">IPASE — Institut Professionnel Action Santé Éducation — Lomé, Togo</div>
</div></body></html>';

envoyerEmail($email, $nomComplet, 'Nous avons reçu votre message — IPASE', $htmlAccuse);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Message envoyé avec succès.']);
} else {
    // Mail natif peut échouer silencieusement — on répond quand même succès
    echo json_encode(['success' => true, 'message' => 'Message reçu.']);
}
?>
