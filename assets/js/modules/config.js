/* ═══════════════════════════════════════════════════════════════════════════
   IPASE - INSTITUT PROFESSIONNEL ACTION SANTÉ ÉDUCATION
   Module JavaScript d'Événements & Dynamisme Inspiré de l'Université de Genève (UNIGE)
   
   ÉVÉNEMENTS JAVASCRIPT UTILISÉS :
   1.  Event 'DOMContentLoaded' & 'load' (Initialisation globale)
   2.  Event 'scroll' (Barre de progression, Header compact, Bouton retour en haut)
   3.  Event 'click' (Navigation SPA, Onglets, Modale rapide, Accordéons par domaine)
   4.  Event 'input' & 'keyup' (Recherche instantanée en temps réel avec surbrillance <mark>)
   5.  Event 'change' (Mise à jour dynamique de la liste déroulante des filières)
   6.  Event 'submit' (Validation interactive & notifications des formulaires)
   7.  Event 'keydown' (Accessibilité : Fermeture des modales par la touche Échap)
   8.  Event 'mouseenter' & 'mouseleave' (Micro-animations interactives au survol)
   9.  Event 'resize' (Adaptation responsive et réinitialisation des menus)
   10. IntersectionObserver API (Animation progressive au défilement des éléments)
   ═══════════════════════════════════════════════════════════════════════════ */

const IPASE_STORAGE_KEY = 'ipase_registrations';
const IPASE_SESSION_KEY = 'ipase_student_session';
const IPASE_LOGO_PATH = 'assets/img/logo.jpeg';
const IPASE_API_URL = 'api/inscrire_etudiant.php';
const IPASE_AUTH_API_URL = 'api/auth_etudiant.php';
const IPASE_ADMIN_API_URL = 'api/admin.php';
let IPASE_ADMIN_STUDENTS = [];

const IPASE_DOMAIN_TO_FILIERE = {
    informatique: 1,
    gestion: 2,
    transport: 2,
    sante: 3,
    technique: 1,
    specialise: 3,
    bt: 2
};

const IPASE_LEVEL_TO_NIVEAU = {
    BEPC: 1,
    BAC1: 1,
    BAC2: 1,
    LICENCE: 3,
    AUTRE: 1
};

const IPASE_COLORS = {
    blue: [0, 32, 91],
    red: [200, 16, 46],
    gold: [212, 175, 55],
    lightGray: [245, 247, 250],
    midGray: [120, 130, 145],
    white: [255, 255, 255]
};

const IPASE_DOMAIN_LABELS = {
    sante: 'Santé et Social',
    gestion: 'Gestion et Administration',
    transport: 'Transport Logistique et Commerce',
    informatique: 'Informatique, Numérique et Technologie',
    technique: 'Technique et Industriel',
    specialise: 'Autres Filières Spécialisées',
    bt: 'Brevet de Technicien (BT)'
};
