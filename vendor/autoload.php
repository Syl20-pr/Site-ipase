<?php
// ============================================================
//  autoload.php - Chargement manuel de PHPMailer
//  (Remplace Composer pour les environnements sans CLI)
// ============================================================

$phpmailerSrc = __DIR__ . '/../phpmailer/phpmailer/src/';

require_once $phpmailerSrc . 'Exception.php';
require_once $phpmailerSrc . 'PHPMailer.php';
require_once $phpmailerSrc . 'SMTP.php';
?>
