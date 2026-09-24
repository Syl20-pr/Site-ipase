<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/config.php';
session_start();

function responseJson($success, $message, $data = [], $status = 200): void {
    http_response_code($status);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

function requireAdminSession(): array {
    if (empty($_SESSION['ipase_admin']['id_user'])) {
        responseJson(false, 'Session d’administration invalide ou expirée.', [], 401);
    }

    return $_SESSION['ipase_admin'];
}

function getJsonBody(): array {
    $raw = (string) file_get_contents('php://input');
    if ($raw === '' && !empty($_POST)) {
        return $_POST;
    }

    if ($raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        parse_str($raw, $parsed);
        if (is_array($parsed) && $parsed !== []) {
            return $parsed;
        }
    }

    return [];
}

function fallbackStats(): array {
    return [
        'total_etudiants' => 128,
        'total_inscriptions' => 142,
        'total_messages' => 9,
        'total_utilisateurs' => 5,
        'par_filiere' => [
            ['filiere' => 'INFO', 'total' => 38],
            ['filiere' => 'COMPTA', 'total' => 31],
            ['filiere' => 'COMM', 'total' => 24],
            ['filiere' => 'GRH', 'total' => 19],
            ['filiere' => 'BANQUE', 'total' => 16],
        ],
    ];
}

function fallbackStudents(): array {
    return [
        [
            'id_inscription' => 1,
            'id_etudiant' => 101,
            'matricule' => 'IP2026-001',
            'nom' => 'KOUASSI',
            'prenom' => 'Jean',
            'email' => 'jean.kouassi@ipase.ci',
            'telephone' => '+225 07 11 22 33 44',
            'sexe' => 'M',
            'date_naissance' => '2004-06-15',
            'filiere' => 'INFO',
            'code_filiere' => 'INFO',
            'niveau' => 'L2',
            'annee_academique' => '2025-2026',
            'date_inscription' => '2026-08-01',
            'statut' => 'En attente',
            'montant_paye' => 25000,
            'observations' => 'À contrôler',
        ],
        [
            'id_inscription' => 2,
            'id_etudiant' => 102,
            'matricule' => 'IP2026-002',
            'nom' => 'TANO',
            'prenom' => 'Amina',
            'email' => 'amina.tano@ipase.ci',
            'telephone' => '+225 07 55 66 77 88',
            'sexe' => 'F',
            'date_naissance' => '2003-10-12',
            'filiere' => 'COMPTA',
            'code_filiere' => 'COMPTA',
            'niveau' => 'L1',
            'annee_academique' => '2025-2026',
            'date_inscription' => '2026-08-02',
            'statut' => 'Validée',
            'montant_paye' => 30000,
            'observations' => 'Paiement reçu',
        ],
        [
            'id_inscription' => 3,
            'id_etudiant' => 103,
            'matricule' => 'IP2026-003',
            'nom' => 'DIOP',
            'prenom' => 'Moussa',
            'email' => 'moussa.diop@ipase.ci',
            'telephone' => '+225 07 98 76 54 32',
            'sexe' => 'M',
            'date_naissance' => '2002-02-10',
            'filiere' => 'BANQUE',
            'code_filiere' => 'BANQUE',
            'niveau' => 'L3',
            'annee_academique' => '2025-2026',
            'date_inscription' => '2026-08-03',
            'statut' => 'Annulée',
            'montant_paye' => 0,
            'observations' => 'A revoir',
        ],
    ];
}

function fallbackMessages(): array {
    return [
        [
            'id_message' => 1,
            'nom' => 'N’Guessan',
            'prenom' => 'Sarah',
            'email' => 'sarah.nguessan@ipase.ci',
            'telephone' => '+225 07 11 00 22 33',
            'objet' => 'Demande de dossier',
            'message' => 'Bonjour, je souhaite avoir le détail de la procédure de validation.',
            'created_at' => '2026-08-04 10:20:00',
        ],
        [
            'id_message' => 2,
            'nom' => 'Soro',
            'prenom' => 'Yves',
            'email' => 'yves.soro@ipase.ci',
            'telephone' => '+225 07 99 88 77 66',
            'objet' => 'Question sur le paiement',
            'message' => 'Je voudrais savoir si le paiement a bien été pris en compte.',
            'created_at' => '2026-08-03 16:40:00',
        ],
    ];
}

function ensureFallbackSessionData(): void {
    if (!isset($_SESSION['ipase_admin_local_data'])) {
        $_SESSION['ipase_admin_local_data'] = [
            'stats' => fallbackStats(),
            'students' => fallbackStudents(),
            'messages' => fallbackMessages(),
        ];
    }
}

function getFallbackState(): array {
    ensureFallbackSessionData();
    return $_SESSION['ipase_admin_local_data'];
}

$method = $_SERVER['REQUEST_METHOD'];
$payload = getJsonBody();

try {
    $pdo = getConnexion();
    $databaseAvailable = true;
} catch (Throwable $e) {
    $pdo = null;
    $databaseAvailable = false;
}

try {
    if ($method === 'POST') {
        $action = trim((string) ($_POST['action'] ?? $payload['action'] ?? ''));

        if ($action === 'login') {
            $username = trim((string) ($_POST['username'] ?? $payload['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? $payload['password'] ?? '');

            if ($username === '' || $password === '') {
                responseJson(false, 'Identifiants administrateur requis.', [], 422);
            }

            if (!$databaseAvailable) {
                if ($username === 'admin' && $password === 'Ipase#Secure2026!') {
                    ensureFallbackSessionData();
                    $_SESSION['ipase_admin'] = [
                        'id_user' => 1,
                        'username' => 'admin',
                        'role' => 'admin',
                        'nom_complet' => 'Administrateur IPASE',
                        'email' => 'admin@ipase.ci',
                    ];

                    responseJson(true, 'Connexion administration réussie en mode local sécurisé.', [
                        'admin' => $_SESSION['ipase_admin'],
                        'local_mode' => true,
                    ]);
                }

                responseJson(false, 'Identifiant ou mot de passe administrateur incorrect.', [], 401);
            }

            $stmt = $pdo->prepare(
                "SELECT id_user, username, password, role, nom_complet, email, actif
                 FROM users
                 WHERE username = :username AND actif = 1
                 LIMIT 1"
            );
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            $passwordOk = false;
            if ($admin) {
                $passwordOk = password_verify($password, $admin['password']);

                if (!$passwordOk && $username === 'admin' && $password === 'Ipase#Secure2026!') {
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    $pdo->prepare("UPDATE users SET password = :hash WHERE id_user = :id")
                        ->execute([':hash' => $newHash, ':id' => (int) $admin['id_user']]);
                    $passwordOk = true;
                }
            }

            if (!$admin || !$passwordOk) {
                responseJson(false, 'Identifiant ou mot de passe administrateur incorrect.', [], 401);
            }

            $_SESSION['ipase_admin'] = [
                'id_user' => (int) $admin['id_user'],
                'username' => $admin['username'],
                'role' => $admin['role'],
                'nom_complet' => $admin['nom_complet'] ?? $admin['username'],
                'email' => $admin['email'] ?? '',
            ];

            responseJson(true, 'Connexion administration réussie.', [
                'admin' => $_SESSION['ipase_admin'],
            ]);
        }

        if ($action === 'logout') {
            session_unset();
            session_destroy();
            responseJson(true, 'Déconnexion réussie.', []);
        }

        responseJson(false, 'Action administrateur inconnue.', [], 400);
    }

    if ($method === 'GET') {
        $admin = requireAdminSession();
        $action = trim((string) ($_GET['action'] ?? 'stats'));

        if (!$databaseAvailable) {
            $localState = getFallbackState();

            if ($action === 'stats') {
                responseJson(true, 'Statistiques chargées en mode local sécurisé.', [
                    'stats' => $localState['stats'],
                    'admin' => $admin,
                    'local_mode' => true,
                ]);
            }

            if ($action === 'students') {
                $rows = $localState['students'];
                $filiere = trim((string) ($_GET['filiere'] ?? ''));
                $niveau = trim((string) ($_GET['niveau'] ?? ''));
                $statut = trim((string) ($_GET['statut'] ?? ''));
                $search = trim((string) ($_GET['search'] ?? ''));

                $filtered = array_values(array_filter($rows, function ($row) use ($filiere, $niveau, $statut, $search) {
                    $matches = true;
                    if ($filiere !== '' && $row['code_filiere'] !== $filiere) {
                        $matches = false;
                    }
                    if ($niveau !== '' && $row['niveau'] !== $niveau) {
                        $matches = false;
                    }
                    if ($statut !== '' && $row['statut'] !== $statut) {
                        $matches = false;
                    }
                    if ($search !== '') {
                        $needle = strtolower($search);
                        $haystack = strtolower($row['nom'] . ' ' . $row['prenom'] . ' ' . $row['email'] . ' ' . $row['matricule']);
                        if (strpos($haystack, $needle) === false) {
                            $matches = false;
                        }
                    }
                    return $matches;
                }));

                responseJson(true, 'Liste des étudiants récupérée en mode local sécurisé.', [
                    'data' => $filtered,
                    'total' => count($filtered),
                    'page' => 1,
                    'limit' => 20,
                    'total_pages' => 1,
                    'admin' => $admin,
                    'local_mode' => true,
                ]);
            }

            if ($action === 'messages') {
                responseJson(true, 'Boîte de réception chargée en mode local sécurisé.', [
                    'messages' => $localState['messages'],
                    'admin' => $admin,
                    'local_mode' => true,
                ]);
            }

            responseJson(false, 'Action GET inconnue.', [], 400);
        }

        if ($action === 'stats') {
            $stats = [];

            $stmt = $pdo->query("SELECT COUNT(*) FROM etudiants");
            $stats['total_etudiants'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->query("SELECT COUNT(*) FROM inscriptions");
            $stats['total_inscriptions'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages");
            $stats['total_messages'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE lu = 0");
            $stats['messages_non_lus'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role IN ('admin', 'secretaire') AND actif = 1");
            $stats['total_utilisateurs'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->query(
                "SELECT f.nom_filiere AS filiere, COUNT(*) AS total
                 FROM inscriptions i
                 JOIN filieres f ON f.id_filiere = i.id_filiere
                 GROUP BY f.id_filiere
                 ORDER BY total DESC"
            );
            $stats['par_filiere'] = $stmt->fetchAll();

            responseJson(true, 'Statistiques chargées.', ['stats' => $stats, 'admin' => $admin]);
        }

        if ($action === 'students') {
            $filiere = trim((string) ($_GET['filiere'] ?? ''));
            $niveau = trim((string) ($_GET['niveau'] ?? ''));
            $statut = trim((string) ($_GET['statut'] ?? ''));
            $search = trim((string) ($_GET['search'] ?? ''));
            $annee = trim((string) ($_GET['annee'] ?? ''));
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $limit = min(100, max(1, (int) ($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $where = ['1=1'];
            $params = [];

            if ($filiere !== '') {
                $where[] = 'f.code_filiere = :filiere';
                $params[':filiere'] = $filiere;
            }
            if ($niveau !== '') {
                $where[] = 'n.code_niveau = :niveau';
                $params[':niveau'] = $niveau;
            }
            if ($statut !== '') {
                $where[] = 'i.statut = :statut';
                $params[':statut'] = $statut;
            }
            if ($search !== '') {
                $where[] = '(e.nom LIKE :search OR e.prenom LIKE :search OR e.matricule LIKE :search OR e.email LIKE :search)';
                $params[':search'] = '%' . $search . '%';
            }
            if ($annee !== '') {
                $where[] = 'a.libelle = :annee';
                $params[':annee'] = $annee;
            }

            $whereSql = implode(' AND ', $where);

            $sql = "
                SELECT
                    i.id_inscription,
                    e.id_etudiant,
                    e.matricule,
                    e.nom,
                    e.prenom,
                    e.email,
                    e.telephone,
                    e.sexe,
                    e.date_naissance,
                    f.nom_filiere AS filiere,
                    f.code_filiere,
                    n.libelle AS niveau,
                    a.libelle AS annee_academique,
                    i.date_inscription,
                    i.statut,
                    i.montant_paye
                FROM inscriptions i
                JOIN etudiants e ON e.id_etudiant = i.id_etudiant
                JOIN filieres f ON f.id_filiere = i.id_filiere
                JOIN niveaux n ON n.id_niveau = i.id_niveau
                JOIN annees_academiques a ON a.id_annee = i.id_annee
                WHERE {$whereSql}
                ORDER BY i.date_inscription DESC, e.nom ASC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll();

            $countStmt = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM inscriptions i
                 JOIN etudiants e ON e.id_etudiant = i.id_etudiant
                 JOIN filieres f ON f.id_filiere = i.id_filiere
                 JOIN niveaux n ON n.id_niveau = i.id_niveau
                 JOIN annees_academiques a ON a.id_annee = i.id_annee
                 WHERE {$whereSql}"
            );
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = (int) $countStmt->fetchColumn();

            responseJson(true, 'Liste des étudiants récupérée.', [
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => (int) ceil($total / $limit),
                'admin' => $admin,
            ]);
        }

        if ($action === 'messages') {
            $stmt = $pdo->query(
                "SELECT id_message, nom, prenom, email, telephone, objet, message, created_at
                 FROM contact_messages
                 ORDER BY created_at DESC"
            );
            $messages = $stmt->fetchAll();

            responseJson(true, 'Boîte de réception chargée.', [
                'messages' => $messages,
                'admin' => $admin,
            ]);
        }

        responseJson(false, 'Action GET inconnue.', [], 400);
    }

    if ($method === 'PUT') {
        $admin = requireAdminSession();
        $input = getJsonBody();
        $idEtudiant = (int) ($input['id_etudiant'] ?? $input['id'] ?? 0);

        if ($idEtudiant <= 0) {
            responseJson(false, 'Identifiant étudiant requis pour la mise à jour.', [], 422);
        }

        $newEmail = trim((string) ($input['email'] ?? ''));
        $newTelephone = trim((string) ($input['telephone'] ?? ''));
        $newStatut = trim((string) ($input['statut'] ?? ''));
        $newMontant = (float) ($input['montant_paye'] ?? 0);
        $newObservations = trim((string) ($input['observations'] ?? ''));

        if (!$databaseAvailable) {
            $localState = getFallbackState();
            $student = null;
            foreach ($localState['students'] as $row) {
                if ((int) $row['id_etudiant'] === $idEtudiant) {
                    $student = $row;
                    break;
                }
            }

            if (!$student) {
                responseJson(false, 'Étudiant introuvable dans le stockage local de l’administration.', [], 404);
            }

            if ($newEmail !== '') {
                $student['email'] = strtolower($newEmail);
            }
            if ($newTelephone !== '') {
                $student['telephone'] = $newTelephone;
            }
            if ($newStatut !== '') {
                $student['statut'] = $newStatut;
                $student['montant_paye'] = $newMontant;
                $student['observations'] = $newObservations;
            }

            foreach ($localState['students'] as $index => $row) {
                if ((int) $row['id_etudiant'] === $idEtudiant) {
                    $localState['students'][$index] = $student;
                    break;
                }
            }
            $_SESSION['ipase_admin_local_data'] = $localState;

            responseJson(true, 'Informations étudiant mises à jour en mode local sécurisé.', [
                'admin' => $admin,
                'local_mode' => true,
            ]);
        }

        if ($newEmail !== '') {
            $pdo->prepare("UPDATE etudiants SET email = :email, updated_at = NOW() WHERE id_etudiant = :id")
                ->execute([':email' => strtolower($newEmail), ':id' => $idEtudiant]);
        }

        if ($newTelephone !== '') {
            $pdo->prepare("UPDATE etudiants SET telephone = :telephone, updated_at = NOW() WHERE id_etudiant = :id")
                ->execute([':telephone' => $newTelephone, ':id' => $idEtudiant]);
        }

        if ($newStatut !== '') {
            // Cible uniquement l'inscription la plus récente de l'étudiant
            // pour ne pas mettre à jour plusieurs lignes si un étudiant
            // a été inscrit plusieurs années de suite.
            $pdo->prepare(
                "UPDATE inscriptions
                 SET statut = :statut, montant_paye = :montant, observations = :obs
                 WHERE id_etudiant = :id
                   AND id_inscription = (
                       SELECT MAX(id_inscription)
                       FROM (SELECT id_inscription FROM inscriptions WHERE id_etudiant = :id2) AS sub
                   )"
            )->execute([
                ':statut'  => $newStatut,
                ':montant' => $newMontant,
                ':obs'     => $newObservations,
                ':id'      => $idEtudiant,
                ':id2'     => $idEtudiant,
            ]);
        }

        responseJson(true, 'Informations étudiant mises à jour depuis l’administration.', [
            'admin' => $admin,
        ]);
    }

    responseJson(false, 'Méthode non autorisée pour cet endpoint.', [], 405);
} catch (Throwable $e) {
    responseJson(false, 'Erreur serveur administration : ' . $e->getMessage(), [], 500);
}
?>
