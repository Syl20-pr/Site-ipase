<?php
// ============================================================
//  mailer.php - Envoi d'e-mails SMTP via PHPMailer
//  Institut IPASE - Confirmation d'inscription
// ============================================================
require_once __DIR__ . '/../config/config.php';

$_phpmailerSrc = __DIR__ . '/../vendor/phpmailer/phpmailer/src/';
$phpmailerAvailable = file_exists($_phpmailerSrc . 'PHPMailer.php');

if ($phpmailerAvailable) {
    require_once $_phpmailerSrc . 'Exception.php';
    require_once $_phpmailerSrc . 'PHPMailer.php';
    require_once $_phpmailerSrc . 'SMTP.php';
}

// ============================================================
//  Envoi générique
// ============================================================
function envoyerEmail(string $destinataire, string $nomDestinataire, string $sujet, string $corpsHTML, ?string $corpsTexte = null, array $piecesJointes = []): bool
{
    global $phpmailerAvailable;

    if ($phpmailerAvailable) {
        return _envoyerAvecPHPMailer($destinataire, $nomDestinataire, $sujet, $corpsHTML, $corpsTexte, $piecesJointes);
    }

    return _envoyerAvecMailNatif($destinataire, $sujet, $corpsHTML);
}

function _envoyerAvecPHPMailer(string $to, string $nom, string $sujet, string $html, ?string $texte = null, array $piecesJointes = []): bool
{
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION === 'ssl'
            ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
            : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->addAddress($to, $nom);
        $mail->addReplyTo(EMAIL_SUPPORT, 'Service des Admissions - IPASE');

        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $html;
        $mail->AltBody = $texte ?? _genererVersionTexte($html);

        foreach ($piecesJointes as $pieceJointe) {
            if (!empty($pieceJointe['path'])) {
                $mail->addAttachment($pieceJointe['path'], $pieceJointe['name'] ?? null);
            } elseif (!empty($pieceJointe['content']) && !empty($pieceJointe['name'])) {
                $mail->addStringAttachment(
                    $pieceJointe['content'],
                    $pieceJointe['name'],
                    $pieceJointe['encoding'] ?? 'base64',
                    $pieceJointe['mime'] ?? 'application/octet-stream'
                );
            }
        }

        $mail->send();
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('[IPASE - Email Error] PHPMailer : ' . $mail->ErrorInfo);
        return false;
    }
}

function _envoyerAvecMailNatif(string $to, string $sujet, string $html): bool
{
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_USERNAME . ">\r\n";
    $headers .= 'Reply-To: ' . EMAIL_SUPPORT . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();

    $result = @mail($to, $sujet, $html, $headers);
    if (!$result) {
        error_log('[IPASE - Email Error] mail() natif a échoué pour : ' . $to);
    }
    return $result;
}

function _genererVersionTexte(string $html): string
{
    return html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</div>'], "\n", $html)), ENT_QUOTES, 'UTF-8');
}

function _e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// ============================================================
//  Template HTML : Confirmation de candidature
//  Compatible Gmail, Outlook, Apple Mail (layout table + styles inline)
// ============================================================
function templateEmailConfirmation(array $vars): string
{
    $nomEtPrenom = _e($vars['nom_et_prenom']);
    $email = _e($vars['email']);
    $motDePasse = _e($vars['mot_de_passe']);
    $lienConnexion = _e($vars['lien_connexion']);
    $siteWeb = _e($vars['site_web']);
    $emailSupport = _e($vars['email_support']);
    $telephone = _e($vars['telephone']);
    $logoUrl = _e($vars['logo_url'] ?? LOGO_URL);
    $annee = date('Y');

    $blue = '#00205B';
    $red = '#C8102E';
    $gold = '#D4AF37';
    $light = '#F8FAFC';
    $text = '#334155';
    $muted = '#64748B';

    return <<<HTML
<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Confirmation de candidature – Université IPASE</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#EAEFF5;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#EAEFF5;">
        <tr>
            <td align="center" style="padding:24px 12px;">

                <!-- Conteneur principal -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background-color:#FFFFFF;border-radius:12px;overflow:hidden;border:1px solid #E2E8F0;">

                    <!-- Bandeau supérieur -->
                    <tr>
                        <td style="background-color:{$blue};padding:0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="height:4px;background-color:{$gold};font-size:0;line-height:0;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:28px 24px 22px 24px;">
                                        <img src="{$logoUrl}" alt="Logo Université IPASE" width="90" height="90" style="display:block;width:90px;height:90px;border-radius:50%;border:3px solid rgba(255,255,255,0.25);margin:0 auto 14px auto;">
                                        <p style="margin:0;font-size:22px;line-height:1.3;font-weight:bold;color:#FFFFFF;letter-spacing:0.5px;">Université IPASE</p>
                                        <p style="margin:8px 0 0 0;font-size:13px;line-height:1.5;color:rgba(255,255,255,0.85);">Institut Professionnel Action Santé Éducation</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Corps du message -->
                    <tr>
                        <td style="padding:32px 28px 8px 28px;">
                            <p style="margin:0 0 18px 0;font-size:16px;line-height:1.6;color:{$text};">
                                Bonjour <strong style="color:{$blue};">{$nomEtPrenom}</strong>,
                            </p>
                            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.7;color:{$text};">
                                Nous vous remercions d'avoir effectué votre inscription à l'<strong>Université IPASE</strong>.
                            </p>
                            <p style="margin:0 0 24px 0;font-size:15px;line-height:1.7;color:{$text};">
                                Nous avons le plaisir de vous informer que votre candidature a été enregistrée avec succès et est actuellement <strong>en cours de traitement</strong> par notre service des admissions.
                            </p>
                        </td>
                    </tr>

                    <!-- Bloc identifiants -->
                    <tr>
                        <td style="padding:0 28px 24px 28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:{$light};border:1px solid #E2E8F0;border-left:4px solid {$red};border-radius:8px;">
                                <tr>
                                    <td style="padding:22px 20px;">
                                        <p style="margin:0 0 16px 0;font-size:14px;line-height:1.4;font-weight:bold;color:{$blue};text-transform:uppercase;letter-spacing:0.4px;">
                                            Vos informations de connexion
                                        </p>

                                        <p style="margin:0 0 6px 0;font-size:13px;line-height:1.5;color:{$muted};">Adresse e-mail :</p>
                                        <p style="margin:0 0 16px 0;font-size:15px;line-height:1.5;color:{$text};font-weight:bold;">{$email}</p>

                                        <p style="margin:0 0 6px 0;font-size:13px;line-height:1.5;color:{$muted};">Mot de passe provisoire :</p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 4px 0;">
                                            <tr>
                                                <td style="background-color:{$blue};border-radius:6px;padding:12px 18px;">
                                                    <span style="font-family:'Courier New',Courier,monospace;font-size:20px;line-height:1.2;font-weight:bold;color:#FFFFFF;letter-spacing:2px;">{$motDePasse}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Bouton connexion -->
                    <tr>
                        <td align="center" style="padding:0 28px 24px 28px;">
                            <p style="margin:0 0 14px 0;font-size:15px;line-height:1.7;color:{$text};text-align:center;">
                                Vous pouvez vous connecter à votre espace étudiant en cliquant sur le lien suivant :
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 auto;">
                                <tr>
                                    <td align="center" bgcolor="{$red}" style="border-radius:8px;background-color:{$red};">
                                        <a href="{$lienConnexion}" target="_blank" style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:bold;color:#FFFFFF;text-decoration:none;border-radius:8px;">
                                            Accéder à mon espace étudiant
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:14px 0 0 0;font-size:12px;line-height:1.5;color:{$muted};text-align:center;word-break:break-all;">
                                {$lienConnexion}
                            </p>
                        </td>
                    </tr>

                    <!-- Recommandation sécurité -->
                    <tr>
                        <td style="padding:0 28px 24px 28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <p style="margin:0;font-size:14px;line-height:1.6;color:#92400E;">
                                            <strong>Important :</strong> Pour des raisons de sécurité, nous vous recommandons vivement de modifier votre mot de passe dès votre première connexion. Conservez soigneusement ces informations.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Assistance -->
                    <tr>
                        <td style="padding:0 28px 28px 28px;">
                            <p style="margin:0 0 14px 0;font-size:15px;line-height:1.7;color:{$text};">
                                Si vous rencontrez des difficultés ou avez besoin d'assistance, n'hésitez pas à contacter le service des admissions.
                            </p>
                            <p style="margin:0;font-size:15px;line-height:1.7;color:{$text};">
                                Nous vous remercions pour votre confiance et vous souhaitons beaucoup de succès dans votre parcours universitaire.
                            </p>
                        </td>
                    </tr>

                    <!-- Signature -->
                    <tr>
                        <td style="padding:0 28px 28px 28px;border-top:1px solid #E2E8F0;">
                            <p style="margin:20px 0 6px 0;font-size:15px;line-height:1.6;color:{$text};">Cordialement,</p>
                            <p style="margin:0 0 4px 0;font-size:15px;line-height:1.6;font-weight:bold;color:{$blue};">Service des Admissions</p>
                            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;font-weight:bold;color:{$blue};">Université IPASE</p>
                            <p style="margin:0;font-size:13px;line-height:1.8;color:{$muted};">
                                Site web : <a href="{$siteWeb}" style="color:{$red};text-decoration:none;">{$siteWeb}</a><br>
                                E-mail : <a href="mailto:{$emailSupport}" style="color:{$red};text-decoration:none;">{$emailSupport}</a><br>
                                Téléphone : {$telephone}
                            </p>
                        </td>
                    </tr>

                    <!-- Pied de page -->
                    <tr>
                        <td style="background-color:{$light};padding:18px 28px;text-align:center;border-top:1px solid #E2E8F0;">
                            <p style="margin:0;font-size:11px;line-height:1.6;color:#94A3B8;">
                                © {$annee} Université IPASE – Tous droits réservés.<br>
                                Ce message a été envoyé automatiquement, merci de ne pas y répondre directement.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}

function templateEmailConfirmationTexte(array $vars): string
{
    return implode("\n", [
        'Bonjour ' . $vars['nom_et_prenom'] . ',',
        '',
        'Nous vous remercions d\'avoir effectué votre inscription à l\'institut  IPASE.',
        '',
        'Nous avons le plaisir de vous informer que votre candidature a été enregistrée avec succès et est actuellement en cours de traitement par notre service des admissions.',
        '',
        'Vos informations de connexion à votre espace étudiant sont les suivantes :',
        '',
        'Adresse e-mail :',
        $vars['email'],
        '',
        'Mot de passe provisoire :',
        $vars['mot_de_passe'],
        '',
        'Lien de connexion :',
        $vars['lien_connexion'],
        '',
        'Pour des raisons de sécurité, nous vous recommandons vivement de modifier votre mot de passe dès votre première connexion.',
        'Conservez soigneusement ces informations.',
        '',
        'Si vous rencontrez des difficultés ou avez besoin d\'assistance, n\'hésitez pas à contacter le service des admissions.',
        '',
        'Nous vous remercions pour votre confiance et vous souhaitons beaucoup de succès dans votre parcours universitaire.',
        '',
        'Cordialement,',
        '',
        'Service des Admissions',
        'Institut IPASE',
        'Site web : ' . $vars['site_web'],
        'E-mail : ' . $vars['email_support'],
        'Téléphone : ' . $vars['telephone'],
    ]);
}

// ============================================================
//  Envoi automatique après inscription réussie
// ============================================================
function envoyerEmailConfirmationInscription(
    string $prenom,
    string $nom,
    string $email,
    string $motDePasse,
    array $piecesJointes = []
): bool {
    $vars = [
        'nom_et_prenom' => trim($prenom . ' ' . $nom),
        'email' => $email,
        'mot_de_passe' => $motDePasse,
        'lien_connexion' => LOGIN_URL,
        'site_web' => SITE_WEB,
        'email_support' => EMAIL_SUPPORT,
        'telephone' => TELEPHONE,
        'logo_url' => LOGO_URL,
    ];

    $html = templateEmailConfirmation($vars);
    $texte = templateEmailConfirmationTexte($vars);

    return envoyerEmail(
        $email,
        $vars['nom_et_prenom'],
        EMAIL_SUBJECT_INSCRIPTION,
        $html,
        $texte,
        $piecesJointes
    );
}

/**
 * @deprecated Utiliser envoyerEmailConfirmationInscription()
 */
function templateEmailInscription(
    string $prenom,
    string $nom,
    string $matricule,
    string $email,
    string $motDePasse,
    string $filiere,
    string $niveau,
    string $annee
): string {
    return templateEmailConfirmation([
        'nom_et_prenom' => trim($prenom . ' ' . $nom),
        'email' => $email,
        'mot_de_passe' => $motDePasse,
        'lien_connexion' => LOGIN_URL,
        'site_web' => SITE_WEB,
        'email_support' => EMAIL_SUPPORT,
        'telephone' => TELEPHONE,
        'logo_url' => LOGO_URL,
    ]);
}
?>