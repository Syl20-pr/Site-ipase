-- ============================================================
--  BASE DE DONNÉES IPASE - Inscriptions des Étudiants
--  Créé le : 2026-07-25
--  Description : Gestion des étudiants inscrits à l'IPASE
-- ============================================================

-- Création de la base de données
-- ⚠️ Sur un hébergement mutualisé (Hostinger), la base est déjà créée
-- via hPanel (avec un nom du type u123456789_ipase_db) et l'utilisateur
-- n'a pas le droit CREATE DATABASE. On importe donc ce fichier directement
-- "dans" la base déjà sélectionnée en phpMyAdmin — les lignes
-- CREATE DATABASE / USE ont été retirées pour que l'import ne échoue pas.

-- ============================================================
--  TABLE : filieres (Filières / Départements)
-- ============================================================
CREATE TABLE IF NOT EXISTS `filieres` (
    `id_filiere`    INT(11)      NOT NULL AUTO_INCREMENT,
    `code_filiere`  VARCHAR(20)  NOT NULL UNIQUE,
    `nom_filiere`   VARCHAR(100) NOT NULL,
    `description`   TEXT,
    `duree_annees`  INT(2)       DEFAULT 3,
    `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_filiere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données initiales des filières
-- Filières Santé et Social
INSERT INTO `filieres` (`code_filiere`, `nom_filiere`, `duree_annees`) VALUES
('INFIRMIER',   'Sciences Infirmières et Obstétricales',    3),
('SAGE_FEMME',  'Sage-femme / Maïeutique',                  3),
('NUTRITION',   'Nutrition et Diététique',                   3),
('AIDE_SOIN',   'Aide-soignant(e)',                          2),
('LAB_TECH',    'Technicien de Laboratoire Médical',         3),
('RADIO_IMG',   'Radiologie et Imagerie Médicale',           3),
('PHARMA',      'Pharmacie / Assistant Pharmaceutique',      3),
('ANIM_SOCIAL', 'Animateur Social / Travailleur Social',     3),
-- Filières Gestion et Administration
('COMPTA',      'Comptabilité et Gestion',                   3),
('GRH',         'Gestion des Ressources Humaines',           3),
('BANQUE',      'Banque et Finance',                         3),
('COMM_MARK',   'Communication et Marketing',                3),
('SECRETARIAT', 'Secrétariat et Bureau',                     2),
('LOGISTIQUE',  'Gestion de la Chaîne Logistique',           3),
('AUDIT',       'Audit et Contrôle de Gestion',              3),
-- Filières Transport Logistique et Commerce
('TRANSP',      'Transport et Logistique',                   3),
('COMMERCE',    'Commerce International',                    3),
('DOUANE',      'Douane et Fiscalité',                       3),
-- Filières Informatique, Numérique et Technologie
('INFO',        'Informatique de Gestion',                   3),
('DEV_WEB',     'Développement Web et Mobile',               3),
('RESEAUX',     'Réseaux et Systèmes Informatiques',         3),
('CYBER',       'Cybersécurité et Systèmes d Informations',  3),
('GRAPHISME',   'Infographie et Design Numérique',           3),
-- Filières Technique et Industriel
('ELEC',        'Électrotechnique',                          3),
('FROID',       'Froid et Climatisation',                    3),
('MAINTENANCE', 'Maintenance Industrielle',                  3),
('MACON',       'Maçonnerie / Génie Civil',                  3),
('PLOMBER',     'Plomberie et Sanitaire',                    2),
-- Brevet de Technicien (BT)
('BT_ELEC',     'BT Électrotechnique',                       2),
('BT_INFO',     'BT Informatique',                           2),
('BT_SANTE',    'BT Santé Communautaire',                    2),
('BT_COMPTA',   'BT Comptabilité',                           2),
-- Autres filières spécialisées
('JOURNALISME', 'Journalisme et Médias',                     3),
('TOURISME',    'Tourisme et Hôtellerie',                    3),
('ENSEI',       'Sciences de l Éducation / Enseignement',    3);


-- ============================================================
--  TABLE : niveaux (Niveaux d'études)
-- ============================================================
CREATE TABLE IF NOT EXISTS `niveaux` (
    `id_niveau`   INT(11)     NOT NULL AUTO_INCREMENT,
    `code_niveau` VARCHAR(10) NOT NULL UNIQUE,
    `libelle`     VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id_niveau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `niveaux` (`code_niveau`, `libelle`) VALUES
('L1', 'Licence 1ère Année'),
('L2', 'Licence 2ème Année'),
('L3', 'Licence 3ème Année'),
('M1', 'Master 1ère Année'),
('M2', 'Master 2ème Année');


-- ============================================================
--  TABLE : annees_academiques
-- ============================================================
CREATE TABLE IF NOT EXISTS `annees_academiques` (
    `id_annee`   INT(11)     NOT NULL AUTO_INCREMENT,
    `libelle`    VARCHAR(20) NOT NULL,   -- ex: 2025-2026
    `en_cours`   TINYINT(1)  DEFAULT 0,
    PRIMARY KEY (`id_annee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `annees_academiques` (`libelle`, `en_cours`) VALUES
('2024-2025', 0),
('2025-2026', 0),
('2026-2027', 1);


-- ============================================================
--  TABLE : etudiants (Informations personnelles)
-- ============================================================
CREATE TABLE IF NOT EXISTS `etudiants` (
    `id_etudiant`       INT(11)      NOT NULL AUTO_INCREMENT,
    `matricule`         VARCHAR(20)  NOT NULL UNIQUE COMMENT 'Numéro matricule unique',
    `nom`               VARCHAR(100) NOT NULL,
    `prenom`            VARCHAR(100) NOT NULL,
    `date_naissance`    DATE         NOT NULL,
    `lieu_naissance`    VARCHAR(100),
    `sexe`              ENUM('M','F') NOT NULL,
    `nationalite`       VARCHAR(80)  DEFAULT 'Ivoirienne',
    `email`             VARCHAR(150) NOT NULL UNIQUE,
    `telephone`         VARCHAR(20),
    `adresse`           TEXT,
    `photo`             VARCHAR(255) COMMENT 'Chemin vers la photo',
    `situation_famille` ENUM('Célibataire','Marié(e)','Divorcé(e)') DEFAULT 'Célibataire',
    `nom_tuteur`        VARCHAR(200) COMMENT 'Nom du parent / tuteur',
    `tel_tuteur`        VARCHAR(20),
    `created_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_etudiant`),
    INDEX `idx_nom_prenom` (`nom`, `prenom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
--  TABLE : inscriptions (Rattachement étudiant ↔ filière/niveau/année)
-- ============================================================
CREATE TABLE IF NOT EXISTS `inscriptions` (
    `id_inscription`  INT(11)       NOT NULL AUTO_INCREMENT,
    `id_etudiant`     INT(11)       NOT NULL,
    `id_filiere`      INT(11)       NOT NULL,
    `id_niveau`       INT(11)       NOT NULL,
    `id_annee`        INT(11)       NOT NULL,
    `date_inscription`DATE          NOT NULL DEFAULT (CURRENT_DATE), -- défaut dynamique (remplacé par PHP lors de l'insertion)
    `statut`          ENUM('En attente','Validée','Annulée') DEFAULT 'En attente',
    `montant_paye`    DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Frais d inscription payés',
    `recu_paiement`   VARCHAR(100),
    `observations`    TEXT,
    `created_at`      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_inscription`),
    UNIQUE KEY `uq_inscription` (`id_etudiant`, `id_filiere`, `id_niveau`, `id_annee`),
    FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants`(`id_etudiant`) ON DELETE CASCADE,
    FOREIGN KEY (`id_filiere`)  REFERENCES `filieres`(`id_filiere`)   ON DELETE RESTRICT,
    FOREIGN KEY (`id_niveau`)   REFERENCES `niveaux`(`id_niveau`)     ON DELETE RESTRICT,
    FOREIGN KEY (`id_annee`)    REFERENCES `annees_academiques`(`id_annee`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
--  TABLE : documents (Pièces jointes / Documents requis)
-- ============================================================
CREATE TABLE IF NOT EXISTS `documents` (
    `id_document`    INT(11)      NOT NULL AUTO_INCREMENT,
    `id_inscription` INT(11)      NOT NULL,
    `type_document`  VARCHAR(100) NOT NULL COMMENT 'Ex: Acte de naissance, Diplôme…',
    `fichier`        VARCHAR(255) NOT NULL,
    `date_depot`     DATE         DEFAULT NULL,
    `valide`         TINYINT(1)   DEFAULT 0,
    PRIMARY KEY (`id_document`),
    FOREIGN KEY (`id_inscription`) REFERENCES `inscriptions`(`id_inscription`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
--  TABLE : comptes_etudiants (Identifiants espace étudiant)
-- ============================================================
CREATE TABLE IF NOT EXISTS `comptes_etudiants` (
    `id_compte`          INT(11)      NOT NULL AUTO_INCREMENT,
    `id_etudiant`        INT(11)      NOT NULL UNIQUE,
    `email`              VARCHAR(150) NOT NULL UNIQUE,
    `mot_de_passe`       VARCHAR(255) NOT NULL COMMENT 'Hashé bcrypt',
    `doit_changer_mdp`   TINYINT(1)   DEFAULT 1 COMMENT '1 = doit changer au 1er login',
    `derniere_connexion` DATETIME     DEFAULT NULL,
    `actif`              TINYINT(1)   DEFAULT 1,
    `created_at`         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_compte`),
    FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants`(`id_etudiant`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
--  TABLE : users (Accès administrateur / secrétariat)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id_user`    INT(11)      NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(80)  NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL COMMENT 'Mot de passe hashé (bcrypt)',
    `role`       ENUM('admin','secretaire','enseignant') DEFAULT 'secretaire',
    `nom_complet`VARCHAR(150),
    `email`      VARCHAR(150),
    `actif`      TINYINT(1)   DEFAULT 1,
    `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id_message` INT(11)      NOT NULL AUTO_INCREMENT,
    `nom`        VARCHAR(100) NOT NULL,
    `prenom`     VARCHAR(100) NOT NULL,
    `email`      VARCHAR(150) NOT NULL,
    `telephone`  VARCHAR(40)  DEFAULT NULL,
    `objet`      VARCHAR(200) NOT NULL,
    `message`    TEXT         NOT NULL,
    `lu`         TINYINT(1)   DEFAULT 0 COMMENT '0 = non lu, 1 = lu',
    `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_message`),
    INDEX `idx_lu` (`lu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Utilisateur admin par défaut (mot de passe : Admin@ipase2026)
-- Hash bcrypt généré avec password_hash('Admin@ipase2026', PASSWORD_BCRYPT)
-- ⚠️ Mot de passe admin par défaut : Ipase#Secure2026!  (À CHANGER après le premier essai)
INSERT INTO `users` (`username`, `password`, `role`, `nom_complet`, `email`) VALUES
('admin', '$2y$12$9pwkljWF4DuPPuOkHNRZme57e0V.hLnEdRnpHT7RrWVL7t1aSOi2S', 'admin', 'Administrateur IPASE', 'admin@ipase.ci')
ON DUPLICATE KEY UPDATE
    `password` = VALUES(`password`),
    `role` = VALUES(`role`),
    `nom_complet` = VALUES(`nom_complet`),
    `email` = VALUES(`email`),
    `actif` = 1;


-- ============================================================
--  VUE : vue_inscriptions_completes
--  Affiche toutes les infos d'une inscription en une seule requête
-- ============================================================
CREATE OR REPLACE VIEW `vue_inscriptions_completes` AS
SELECT
    i.id_inscription,
    e.matricule,
    CONCAT(e.nom, ' ', e.prenom)        AS etudiant,
    e.email,
    e.telephone,
    f.nom_filiere                        AS filiere,
    n.libelle                            AS niveau,
    a.libelle                            AS annee_academique,
    i.date_inscription,
    i.statut,
    i.montant_paye
FROM `inscriptions` i
JOIN `etudiants`          e ON e.id_etudiant = i.id_etudiant
JOIN `filieres`           f ON f.id_filiere  = i.id_filiere
JOIN `niveaux`            n ON n.id_niveau   = i.id_niveau
JOIN `annees_academiques` a ON a.id_annee    = i.id_annee;


-- ============================================================
--  REQUÊTES UTILES (exemples)
-- ============================================================

-- 1. Lister tous les étudiants inscrits cette année
-- SELECT * FROM vue_inscriptions_completes WHERE annee_academique = '2025-2026';

-- 2. Nombre d'inscrits par filière
-- SELECT filiere, COUNT(*) AS total FROM vue_inscriptions_completes GROUP BY filiere;

-- 3. Rechercher un étudiant par nom
-- SELECT * FROM etudiants WHERE nom LIKE '%Koné%';

-- 4. Inscriptions en attente de validation
-- SELECT * FROM vue_inscriptions_completes WHERE statut = 'En attente';
