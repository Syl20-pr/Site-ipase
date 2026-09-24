<?php
// ============================================================
//  index.php — Page d'accueil IPASE (assemblage des sections)
//  Le contenu a été découpé en plusieurs fichiers dans includes/
//  pour rester lisible et facile à maintenir. Chaque fichier
//  correspond à une des 7 pages du site (une seule page HTML au
//  final pour le visiteur, comme avant).
// ============================================================
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/page-accueil.php';
require __DIR__ . '/includes/page-apropos.php';
require __DIR__ . '/includes/page-filieres.php';
require __DIR__ . '/includes/page-etudiant.php';
require __DIR__ . '/includes/page-vie-etudiante.php';
require __DIR__ . '/includes/page-actualites.php';
require __DIR__ . '/includes/page-contact.php';
require __DIR__ . '/includes/footer.php';
