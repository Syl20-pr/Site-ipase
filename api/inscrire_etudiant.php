<?php
// ============================================================
//  inscrire_etudiant.php - Enregistrement d'un étudiant
//  Méthode : POST (JSON ou formulaire HTML)
// ============================================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../mail/mailer.php';
require_once __DIR__ . '/pdf_generator.php';

// ---- 1. Vérification méthode ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// ---- 2. Récupération des données (formulaire ou JSON) ----
$data = $_POST;
if (empty($data)) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
}

// ---- 3. Validation des champs obligatoires ----
$required = ['nom', 'prenom', 'date_naissance', 'sexe', 'email', 'id_filiere', 'id_niveau'];
$errors = [];
foreach ($required as $field) {
    if (empty(trim($data[$field] ?? ''))) {
        $errors[] = "Le champ '$field' est obligatoire.";
    }
}

// Validation email
if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Adresse email invalide.";
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// ---- 4a. Génération du matricule automatique ----
function genererMatricule(PDO $pdo): string
{
    $annee = date('Y');
    // MAX(id_etudiant) évite les doublons de matricule si des étudiants ont été supprimés
    $stmt = $pdo->query("SELECT COALESCE(MAX(id_etudiant), 0) FROM etudiants");
    $maxId = (int) $stmt->fetchColumn() + 1;
    return 'IPASE-' . $annee . '-' . str_pad($maxId, 4, '0', STR_PAD_LEFT);
}

// ---- 4b. Génération d'un mot de passe provisoire sécurisé ----
function genererMotDePasse(int $longueur = 12): string
{
    $majuscules = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $minuscules = 'abcdefghjkmnpqrstuvwxyz';
    $chiffres   = '23456789';
    $speciaux   = '@#!$';

    $tous = $majuscules . $minuscules . $chiffres . $speciaux;
    $mot  = [
        $majuscules[random_int(0, strlen($majuscules) - 1)],
        $minuscules[random_int(0, strlen($minuscules) - 1)],
        $chiffres[random_int(0, strlen($chiffres) - 1)],
        $speciaux[random_int(0, strlen($speciaux) - 1)],
    ];

    for ($i = count($mot); $i < $longueur; $i++) {
        $mot[] = $tous[random_int(0, strlen($tous) - 1)];
    }

    shuffle($mot);
    return implode('', $mot);
}

try {
    $pdo = getConnexion();

    // Récupérer l'année académique en cours
    $stmtAnnee = $pdo->query("SELECT id_annee, libelle FROM annees_academiques WHERE en_cours = 1 LIMIT 1");
    $anneeRow = $stmtAnnee->fetch();
    if (!$anneeRow) {
        echo json_encode(['success' => false, 'message' => 'Aucune année académique active. Veuillez contacter l\'administration.']);
        exit;
    }
    $id_annee       = $anneeRow['id_annee'];
    $libelle_annee  = $anneeRow['libelle']; // ex: '2026-2027'

    // ---- Démarrage de la transaction ----
    $pdo->beginTransaction();

    // ---- 5. Insertion dans la table etudiants ----
    $matricule = genererMatricule($pdo);

    $stmtEtudiant = $pdo->prepare("
        INSERT INTO etudiants
            (matricule, nom, prenom, date_naissance, lieu_naissance, sexe,
             nationalite, email, telephone, adresse, situation_famille, nom_tuteur, tel_tuteur)
        VALUES
            (:matricule, :nom, :prenom, :date_naissance, :lieu_naissance, :sexe,
             :nationalite, :email, :telephone, :adresse, :situation_famille, :nom_tuteur, :tel_tuteur)
    ");

    $stmtEtudiant->execute([
        ':matricule' => $matricule,
        ':nom' => strtoupper(trim($data['nom'])),
        ':prenom' => ucwords(strtolower(trim($data['prenom']))),
        ':date_naissance' => $data['date_naissance'],
        ':lieu_naissance' => $data['lieu_naissance'] ?? null,
        ':sexe' => $data['sexe'],
        ':nationalite' => $data['nationalite'] ?? 'Ivoirienne',
        ':email' => strtolower(trim($data['email'])),
        ':telephone' => $data['telephone'] ?? null,
        ':adresse' => $data['adresse'] ?? null,
        ':situation_famille' => $data['situation_famille'] ?? 'Célibataire',
        ':nom_tuteur' => $data['nom_tuteur'] ?? null,
        ':tel_tuteur' => $data['tel_tuteur'] ?? null,
    ]);

    $id_etudiant = (int) $pdo->lastInsertId();

    // ---- 6. Insertion dans la table inscriptions ----
    $stmtInscription = $pdo->prepare("
        INSERT INTO inscriptions (id_etudiant, id_filiere, id_niveau, id_annee, date_inscription, statut, montant_paye)
        VALUES (:id_etudiant, :id_filiere, :id_niveau, :id_annee, :date_inscription, 'En attente', :montant_paye)
    ");

    $stmtInscription->execute([
        ':id_etudiant'     => $id_etudiant,
        ':id_filiere'      => (int) $data['id_filiere'],
        ':id_niveau'       => (int) $data['id_niveau'],
        ':id_annee'        => $id_annee,
        ':date_inscription'=> date('Y-m-d'),
        ':montant_paye'    => (float) ($data['montant_paye'] ?? 0),
    ]);

    $id_inscription = (int) $pdo->lastInsertId();

    // Enregistrement éventuel de la pièce justificative
    $piecePath = null;
    if (!empty($data['piece_jointe']) && !empty($data['piece_jointe_nom'])) {
        $rawData = $data['piece_jointe'];
        $segments = explode(',', $rawData, 2);
        $encoded = $segments[1] ?? $rawData;
        $binary = base64_decode($encoded, true);
        if ($binary !== false) {
            $uploadDir = __DIR__ . '/../ressources/uploads/etudiants/' . $id_etudiant . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $data['piece_jointe_nom']);
            $piecePath = $uploadDir . $safeName;
            file_put_contents($piecePath, $binary);

            $stmtDocument = $pdo->prepare(
                "INSERT INTO documents (id_inscription, type_document, fichier, date_depot, valide)
                 VALUES (:id_inscription, :type_document, :fichier, CURDATE(), 1)"
            );
            $stmtDocument->execute([
                ':id_inscription' => $id_inscription,
                ':type_document' => 'Pièce justificative',
                ':fichier' => str_replace(__DIR__ . '/../', '', $piecePath),
            ]);
        }
    }

    $stmtFiliere = $pdo->prepare("SELECT nom_filiere FROM filieres WHERE id_filiere = :id");
    $stmtFiliere->execute([':id' => (int) $data['id_filiere']]);
    $filiereRow = $stmtFiliere->fetch();

    $stmtNiveau = $pdo->prepare("SELECT libelle FROM niveaux WHERE id_niveau = :id");
    $stmtNiveau->execute([':id' => (int) $data['id_niveau']]);
    $niveauRow = $stmtNiveau->fetch();

    // ---- 7. Génération du mot de passe & sauvegarde du compte étudiant ----
    $motDePasseClair = !empty($data['mot_de_passe']) ? $data['mot_de_passe'] : genererMotDePasse(10);
    $motDePasseHash  = password_hash($motDePasseClair, PASSWORD_BCRYPT);

    // Créer (ou mettre à jour) le compte étudiant dans la table comptes_etudiants
    $stmtCompte = $pdo->prepare("
        INSERT INTO comptes_etudiants (id_etudiant, email, mot_de_passe, doit_changer_mdp)
        VALUES (:id_etudiant, :email, :mdp, 1)
        ON DUPLICATE KEY UPDATE
            mot_de_passe      = VALUES(mot_de_passe),
            doit_changer_mdp  = 1,
            updated_at        = NOW()
    ");
    $stmtCompte->execute([
        ':id_etudiant' => $id_etudiant,
        ':email'       => strtolower(trim($data['email'])),
        ':mdp'         => $motDePasseHash,
    ]);

    // Commit de la transaction — toutes les insertions ont réussi
    $pdo->commit();

    // ---- 8. Génération du PDF officiel et envoi de l'e-mail de confirmation ----
    $prenomEtudiant = ucwords(strtolower(trim($data['prenom'])));
    $nomEtudiant    = strtoupper(trim($data['nom']));
    $emailEtudiant  = strtolower(trim($data['email']));

    // La génération du PDF et l'envoi de l'e-mail ne doivent jamais faire
    // échouer l'inscription : le compte est déjà créé en base à ce stade.
    // On isole ces deux étapes pour garantir une réponse JSON propre même
    // si l'une d'elles rencontre un problème (police manquante, SMTP
    // injoignable, etc.), ce qui empêchait auparavant la connexion
    // automatique juste après l'inscription.
    $piecesJointes = [];
    try {
        $pdfPath = __DIR__ . '/../ressources/pdfs/inscriptions/' . $matricule . '.pdf';
        $pdfOk = generateOfficialEnrollmentPdf([
            'nom' => $nomEtudiant,
            'prenom' => $prenomEtudiant,
            'matricule' => $matricule,
            'email' => $emailEtudiant,
            'telephone' => $data['telephone'] ?? '',
            'adresse' => $data['adresse'] ?? '',
            'filiere' => $filiereRow['nom_filiere'] ?? '',
            'niveau' => $niveauRow['libelle'] ?? '',
            'annee_academique' => $libelle_annee,
            'date_inscription' => date('d/m/Y'),
            'mot_de_passe' => $motDePasseClair,
            'sexe' => $data['sexe'] ?? '',
            'date_naissance' => $data['date_naissance'] ?? '',
            'nationalite' => $data['nationalite'] ?? '',
        ], $pdfPath);

        if ($pdfOk && file_exists($pdfPath)) {
            $piecesJointes[] = ['path' => $pdfPath, 'name' => 'Confirmation_Inscription_' . $matricule . '.pdf'];
        }
    } catch (Throwable $e) {
        error_log('[IPASE - PDF Error] ' . $e->getMessage());
    }

    $emailEnvoye = false;
    try {
        $emailEnvoye = envoyerEmailConfirmationInscription(
            $prenomEtudiant,
            $nomEtudiant,
            $emailEtudiant,
            $motDePasseClair,
            $piecesJointes
        );
    } catch (Throwable $e) {
        error_log('[IPASE - Mail Error] ' . $e->getMessage());
    }

    echo json_encode([
        'success'        => true,
        'message'        => 'Inscription enregistrée avec succès !',
        'matricule'      => $matricule,
        'id_etudiant'    => $id_etudiant,
        'id_inscription' => $id_inscription,
        'email_envoye'   => $emailEnvoye,
    ]);

} catch (PDOException $e) {
    // Rollback si la transaction était active
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Gestion doublon email ou matricule
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé par un autre étudiant.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    }
} catch (Throwable $e) {
    // Rollback si la transaction était active
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Filet de sécurité : garantit une réponse JSON valide même en cas
    // d'erreur inattendue, au lieu de laisser PHP renvoyer une page
    // d'erreur HTML que le JavaScript ne peut pas décoder.
    error_log('[IPASE - Inscription Error] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur inattendue. Veuillez réessayer.']);
}
?>