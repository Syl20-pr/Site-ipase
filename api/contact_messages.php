<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];

$nom = trim((string) ($data['nom'] ?? ''));
$prenom = trim((string) ($data['prenom'] ?? ''));
$email = trim((string) ($data['email'] ?? ''));
$telephone = trim((string) ($data['telephone'] ?? ''));
$objet = trim((string) ($data['objet'] ?? ''));
$message = trim((string) ($data['message'] ?? ''));

if ($nom === '' || $prenom === '' || $email === '' || $objet === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Tous les champs requis doivent être renseignés.']);
    exit;
}

try {
    $pdo = getConnexion();
    $stmt = $pdo->prepare(
        "INSERT INTO contact_messages (nom, prenom, email, telephone, objet, message, created_at)
         VALUES (:nom, :prenom, :email, :telephone, :objet, :message, NOW())"
    );
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':telephone' => $telephone,
        ':objet' => $objet,
        ':message' => $message,
    ]);

    echo json_encode(['success' => true, 'message' => 'Message enregistré dans la boîte de réception admin.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
?>
