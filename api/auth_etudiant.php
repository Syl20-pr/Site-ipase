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
require_once __DIR__ . '/../mail/mailer.php';

function responseJson($success, $message, $data = [], $status = 200): void
{
    http_response_code($status);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responseJson(false, 'Méthode non autorisée.', [], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];
if (empty($data)) {
    $data = $_POST;
}

$action = $data['action'] ?? 'login';

try {
    $pdo = getConnexion();

    if ($action === 'login') {
        $identifier = trim((string) ($data['identifier'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($identifier === '' || $password === '') {
            responseJson(false, 'Veuillez renseigner votre identifiant et votre mot de passe.', [], 422);
        }

        $stmt = $pdo->prepare(
            "SELECT ce.id_etudiant, ce.email, ce.mot_de_passe, ce.doit_changer_mdp, e.matricule, e.nom, e.prenom,
                    f.nom_filiere, n.libelle as niveau
             FROM comptes_etudiants ce
             JOIN etudiants e ON e.id_etudiant = ce.id_etudiant
             LEFT JOIN inscriptions i ON i.id_etudiant = e.id_etudiant
             LEFT JOIN filieres f ON f.id_filiere = i.id_filiere
             LEFT JOIN niveaux n ON n.id_niveau = i.id_niveau
             WHERE ce.actif = 1 AND (ce.email = :identifier1 OR e.matricule = :identifier2)
             ORDER BY i.id_inscription DESC LIMIT 1"
        );

        $stmt->execute([':identifier1' => $identifier, ':identifier2' => $identifier]);
        $account = $stmt->fetch();

        if (!$account || !password_verify($password, $account['mot_de_passe'])) {
            responseJson(false, 'Identifiant ou mot de passe incorrect.', [], 401);
        }

        $pdo->prepare("UPDATE comptes_etudiants SET derniere_connexion = NOW() WHERE id_etudiant = :id")
            ->execute([':id' => $account['id_etudiant']]);

        responseJson(true, 'Connexion réussie.', [
            'student' => [
                'id_etudiant' => (int) $account['id_etudiant'],
                'nom' => $account['nom'],
                'prenom' => $account['prenom'],
                'email' => $account['email'],
                'matricule' => $account['matricule'],
                'filiere' => $account['nom_filiere'] ?? 'Non assignée',
                'niveau' => $account['niveau'] ?? '',
                'must_change_password' => (int) $account['doit_changer_mdp'] === 1,
            ],
        ]);
    }

    if ($action === 'change_password') {
        $idEtudiant = (int) ($data['id_etudiant'] ?? 0);
        $currentPassword = (string) ($data['current_password'] ?? '');
        $newPassword = (string) ($data['new_password'] ?? '');

        if ($idEtudiant <= 0 || $currentPassword === '' || $newPassword === '') {
            responseJson(false, 'Informations de changement de mot de passe incomplètes.', [], 422);
        }

        $stmt = $pdo->prepare(
            "SELECT mot_de_passe FROM comptes_etudiants WHERE id_etudiant = :id AND actif = 1"
        );
        $stmt->execute([':id' => $idEtudiant]);
        $account = $stmt->fetch();

        if (!$account || !password_verify($currentPassword, $account['mot_de_passe'])) {
            responseJson(false, 'Le mot de passe actuel est incorrect.', [], 401);
        }

        if (strlen($newPassword) < 8) {
            responseJson(false, 'Le nouveau mot de passe doit contenir au moins 8 caractères.', [], 422);
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $pdo->prepare(
            "UPDATE comptes_etudiants SET mot_de_passe = :hash, doit_changer_mdp = 0, updated_at = NOW() WHERE id_etudiant = :id"
        )->execute([':id' => $idEtudiant, ':hash' => $hash]);

        responseJson(true, 'Mot de passe mis à jour avec succès.', []);
    }

    responseJson(false, 'Action inconnue.', [], 400);
} catch (Throwable $e) {
    responseJson(false, 'Erreur serveur : ' . $e->getMessage(), [], 500);
}
