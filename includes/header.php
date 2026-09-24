<!DOCTYPE html>
<!-- header.php — <head>, en-tête, navigation, ouverture du <main> -->
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IPASE - Institut Professionnel Action Santé Éducation</title>
    <meta name="description" content="IPASE - Institut Professionnel Action Santé Éducation. Formations professionnelles agréées par l'État Togolais en santé, gestion, informatique, transport et plus. Inscrivez-vous en ligne." />
    <meta name="keywords" content="IPASE, formation professionnelle, Togo, Lomé, santé, gestion, informatique, inscription" />
    <meta property="og:title" content="IPASE - Institut Professionnel Action Santé Éducation" />
    <meta property="og:description" content="Formations professionnelles agréées par l'État Togolais. Inscrivez-vous en ligne." />
    <meta property="og:type" content="website" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Stylesheet -->
    <!-- Feuilles de style IPASE — découpées en 5 fichiers pour plus de lisibilité -->
    <!-- L'ordre est important (cascade CSS) -->
    <link rel="stylesheet" href="assets/css/modules/part1-base-hero.css" />
    <link rel="stylesheet" href="assets/css/modules/part2-sections-cards.css" />
    <link rel="stylesheet" href="assets/css/modules/part3-filieres-etudiant.css" />
    <link rel="stylesheet" href="assets/css/modules/part4-dashboard-forms.css" />
    <link rel="stylesheet" href="assets/css/modules/part5-footer-responsive.css" />
</head>

<body>

    <div class="site-wrapper">

        <!-- ═══ HEADER / NAVIGATION FIXE ═══ -->
        <header class="header">
            <div class="header-container">
                <a href="#" class="logo-link" data-page-target="page-accueil">
                    <img src="assets/img/logo.jpeg" alt="Logo IPASE" class="logo-img" />
                </a>

                <button class="mobile-toggle" id="mobile-menu-toggle" aria-label="Menu principal">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <nav class="nav">
                    <a href="#" data-page-target="page-accueil" class="active">ACCUEIL</a>
                    <a href="#" data-page-target="page-apropos">À PROPOS</a>
                    <a href="#" data-page-target="page-filieres">NOS FILIÈRES</a>
                    <a href="#" data-page-target="page-etudiant">ESPACE ÉTUDIANT</a>
                    <a href="#" data-page-target="page-vie-etudiante">VIE ÉTUDIANTE</a>
                    <a href="#" data-page-target="page-actualites">ACTUALITÉS</a>
                    <a href="#" data-page-target="page-contact">CONTACT</a>
                </nav>
            </div>
            <!-- Barre de progression de défilement (Style Université de Genève) -->
            <div id="scroll-progress-bar" class="scroll-progress-bar"></div>
        </header>

        <!-- ═══ APPLICATION CONTENT (7 PAGES) ═══ -->
        <main class="main-body">
