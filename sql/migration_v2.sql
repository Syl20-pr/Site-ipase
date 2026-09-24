-- ============================================================
--  MIGRATION v2 -- IPASE
--  A executer sur une base DEJA INSTALLEE (Hostinger / phpMyAdmin)
--  Cree le : 2026-09-24
--  Description : Corrections et ameliorations apportees lors de
--                la revision generale de la base de donnees.
-- ============================================================

-- ============================================================
-- 1. Colonne `lu` dans contact_messages (messages non lus)
-- ============================================================
ALTER TABLE `contact_messages`
    ADD COLUMN IF NOT EXISTS `lu` TINYINT(1) NOT NULL DEFAULT 0
        COMMENT '0 = non lu, 1 = lu'
        AFTER `message`;

-- Index pour les requetes de filtrage sur les messages non lus
CREATE INDEX IF NOT EXISTS `idx_lu` ON `contact_messages` (`lu`);

-- ============================================================
-- 2. Ajout de l'annee academique 2026-2027 (en cours)
--    et passage de 2025-2026 a "terminee"
-- ============================================================
UPDATE `annees_academiques` SET `en_cours` = 0;

INSERT INTO `annees_academiques` (`libelle`, `en_cours`)
SELECT '2026-2027', 1
WHERE NOT EXISTS (
    SELECT 1 FROM `annees_academiques` WHERE `libelle` = '2026-2027'
);

-- ============================================================
-- 3. Ajout des nouvelles filieres (INSERT IGNORE = pas d'erreur si deja presentes)
-- ============================================================
INSERT IGNORE INTO `filieres` (`code_filiere`, `nom_filiere`, `duree_annees`) VALUES
-- Sante et Social
('INFIRMIER',   'Sciences Infirmieres et Obstetricales',    3),
('SAGE_FEMME',  'Sage-femme / Maieutique',                  3),
('NUTRITION',   'Nutrition et Dietetique',                   3),
('AIDE_SOIN',   'Aide-soignant(e)',                          2),
('LAB_TECH',    'Technicien de Laboratoire Medical',         3),
('RADIO_IMG',   'Radiologie et Imagerie Medicale',           3),
('PHARMA',      'Pharmacie / Assistant Pharmaceutique',      3),
('ANIM_SOCIAL', 'Animateur Social / Travailleur Social',     3),
-- Gestion et Administration
('COMM_MARK',   'Communication et Marketing',                3),
('SECRETARIAT', 'Secretariat et Bureau',                     2),
('LOGISTIQUE',  'Gestion de la Chaine Logistique',           3),
('AUDIT',       'Audit et Controle de Gestion',              3),
-- Transport Logistique et Commerce
('TRANSP',      'Transport et Logistique',                   3),
('COMMERCE',    'Commerce International',                    3),
('DOUANE',      'Douane et Fiscalite',                       3),
-- Informatique, Numerique et Technologie
('DEV_WEB',     'Developpement Web et Mobile',               3),
('RESEAUX',     'Reseaux et Systemes Informatiques',         3),
('CYBER',       'Cybersecurite et Systemes d Informations',  3),
('GRAPHISME',   'Infographie et Design Numerique',           3),
-- Technique et Industriel
('ELEC',        'Electrotechnique',                          3),
('FROID',       'Froid et Climatisation',                    3),
('MAINTENANCE', 'Maintenance Industrielle',                  3),
('MACON',       'Maconnerie / Genie Civil',                  3),
('PLOMBER',     'Plomberie et Sanitaire',                    2),
-- Brevet de Technicien (BT)
('BT_ELEC',     'BT Electrotechnique',                       2),
('BT_INFO',     'BT Informatique',                           2),
('BT_SANTE',    'BT Sante Communautaire',                    2),
('BT_COMPTA',   'BT Comptabilite',                           2),
-- Autres filieres specialisees
('JOURNALISME', 'Journalisme et Medias',                     3),
('TOURISME',    'Tourisme et Hotellerie',                    3),
('ENSEI',       'Sciences de l Education / Enseignement',    3);

-- ============================================================
-- 4. Verification : compte des filieres et annees actives
-- ============================================================
SELECT COUNT(*) AS total_filieres FROM `filieres`;
SELECT `libelle`, `en_cours` FROM `annees_academiques` ORDER BY `id_annee`;
SELECT 'Migration v2 appliquee avec succes !' AS statut;
