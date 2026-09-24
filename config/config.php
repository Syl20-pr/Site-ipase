<?php
// ============================================================
//  GESTION DES ERREURS PHP
//  Empêche les warnings/notices/deprecated de PHP 8.5 de s'afficher
//  dans la sortie et de casser le JSON renvoyé aux pages admin et
//  étudiant (c'était la cause des "problèmes de connexion").
// ============================================================
error_reporting(E_ALL);
ini_set('display_errors', '0');   // ne jamais imprimer les erreurs dans la réponse
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../tmp/php_errors.log');

// ============================================================
//  SESSIONS PHP (utilisées par l'administration)
//  Chemin relatif au projet : fonctionne quel que soit l'endroit
//  où le dossier est extrait/déplacé (fixe le bug de session qui
//  se perdait après un déplacement du dossier ou une réextraction).
// ============================================================
$__ipaseSessionPath = __DIR__ . '/../tmp';
if (!is_dir($__ipaseSessionPath)) {
    mkdir($__ipaseSessionPath, 0777, true);
}
ini_set('session.save_path', $__ipaseSessionPath);

// ============================================================
//  CHARGEMENT DE LA CONFIGURATION LOCALE (si présente)
// ============================================================
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// ============================================================
//  CONFIGURATION DE LA BASE DE DONNÉES IPASE
// ============================================================
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'ipase_db');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// ============================================================
//  CONFIGURATION EMAIL (SMTP)
// ============================================================
if (!defined('MAIL_HOST')) define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.gmail.com');
if (!defined('MAIL_PORT')) define('MAIL_PORT', (int)(getenv('MAIL_PORT') ?: 587));
if (!defined('MAIL_USERNAME')) define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: 'exemple@gmail.com');
if (!defined('MAIL_PASSWORD')) define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', 'Institut IPASE - Admissions');
if (!defined('MAIL_ENCRYPTION')) define('MAIL_ENCRYPTION', getenv('MAIL_ENCRYPTION') ?: 'tls');
// ------------------------------------------------------------
// SITE_URL est calculé automatiquement à partir de l'adresse
// réellement utilisée pour accéder au site (nom de domaine +
// dossier). C'est ce qui corrige le bug de connexion : avant,
// l'URL était figée sur "http://localhost/DOSSIER%20IPASE", donc
// le lien "Accéder à mon espace étudiant" envoyé par e-mail
// pointait vers l'ordinateur du développeur et pas vers le vrai
// site → les étudiants avaient une erreur "impossible d'accéder
// à ce site" en cliquant sur le lien reçu par mail.
// ------------------------------------------------------------
if (!empty($_SERVER['HTTP_HOST'])) {
    $__scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443) ? 'https' : 'http';
    // Dossier de base du projet, déduit automatiquement (fonctionne
    // que le site soit à la racine du domaine ou dans un sous-dossier)
    $__basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? ''))), '/');
    if ($__basePath === '.' || $__basePath === '/') {
        $__basePath = '';
    }
    define('SITE_URL', $__scheme . '://' . $_SERVER['HTTP_HOST'] . $__basePath);
} else {
    // Repli si le fichier est appelé hors contexte web (CLI, etc.)
    define('SITE_URL', 'https://www.ipase.tg');
}
define('LOGIN_URL', SITE_URL . '/index.php');
define('SITE_WEB', 'https://www.ipase.tg');
define('EMAIL_SUPPORT', 'ipase.tg@gmail.com');
define('TELEPHONE', '+228 93 88 23 52');
define('LOGO_URL', SITE_URL . '/assets/img/logo.jpeg');
define('EMAIL_SUBJECT_INSCRIPTION', 'Confirmation de votre candidature – Institut IPASE');

// Connexion PDO
function getConnexion(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    return $pdo;
}
?>