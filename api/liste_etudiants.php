<?php
// ============================================================
//  liste_etudiants.php - Récupérer la liste des inscrits
//  Méthode : GET
//  Paramètres optionnels : ?filiere=INFO&niveau=L1&statut=Validée&search=Koné
// ============================================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/config.php';

try {
    $pdo = getConnexion();

    $where = ['1=1'];
    $params = [];

    // Filtres dynamiques
    if (!empty($_GET['filiere'])) {
        $where[] = "f.code_filiere = :filiere";
        $params[':filiere'] = $_GET['filiere'];
    }
    if (!empty($_GET['niveau'])) {
        $where[] = "n.code_niveau = :niveau";
        $params[':niveau'] = $_GET['niveau'];
    }
    if (!empty($_GET['statut'])) {
        $where[] = "i.statut = :statut";
        $params[':statut'] = $_GET['statut'];
    }
    if (!empty($_GET['search'])) {
        $where[] = "(e.nom LIKE :search OR e.prenom LIKE :search OR e.matricule LIKE :search OR e.email LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }
    if (!empty($_GET['annee'])) {
        $where[] = "a.libelle = :annee";
        $params[':annee'] = $_GET['annee'];
    }

    $whereSQL = implode(' AND ', $where);

    // Pagination
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $limit = min(100, max(1, (int) ($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;

    // Requête principale
    $sql = "
        SELECT
            i.id_inscription,
            e.matricule,
            e.nom,
            e.prenom,
            e.email,
            e.telephone,
            e.sexe,
            e.date_naissance,
            f.nom_filiere   AS filiere,
            f.code_filiere,
            n.libelle       AS niveau,
            a.libelle       AS annee_academique,
            i.date_inscription,
            i.statut,
            i.montant_paye
        FROM inscriptions i
        JOIN etudiants          e ON e.id_etudiant = i.id_etudiant
        JOIN filieres           f ON f.id_filiere  = i.id_filiere
        JOIN niveaux            n ON n.id_niveau   = i.id_niveau
        JOIN annees_academiques a ON a.id_annee    = i.id_annee
        WHERE $whereSQL
        ORDER BY i.date_inscription DESC, e.nom ASC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $etudiants = $stmt->fetchAll();

    // Compter le total pour la pagination
    $stmtCount = $pdo->prepare("
        SELECT COUNT(*) FROM inscriptions i
        JOIN etudiants e          ON e.id_etudiant = i.id_etudiant
        JOIN filieres f           ON f.id_filiere  = i.id_filiere
        JOIN niveaux n            ON n.id_niveau   = i.id_niveau
        JOIN annees_academiques a ON a.id_annee    = i.id_annee
        WHERE $whereSQL
    ");
    foreach ($params as $key => $val) {
        $stmtCount->bindValue($key, $val);
    }
    $stmtCount->execute();
    $total = (int) $stmtCount->fetchColumn();

    echo json_encode([
        'success' => true,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total / $limit),
        'data' => $etudiants,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
?>