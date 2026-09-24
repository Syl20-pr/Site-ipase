<?php
require_once __DIR__ . '/fpdf.php';

function escapePdfText(string $value): string
{
    // FPDF expects ISO-8859-1 for standard fonts like Arial.
    // utf8_decode() est dépréciée depuis PHP 8.2 et générait des
    // avertissements qui cassaient le JSON renvoyé par l'API
    // (cause des problèmes de connexion après inscription).
    $converted = @mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
    return $converted !== false ? $converted : $value;
}

function generateOfficialEnrollmentPdf(array $studentData, string $outputPath): bool
{
    if (!is_dir(dirname($outputPath))) {
        mkdir(dirname($outputPath), 0777, true);
    }

    $nom = trim((string) ($studentData['nom'] ?? ''));
    $prenom = trim((string) ($studentData['prenom'] ?? ''));
    $matricule = trim((string) ($studentData['matricule'] ?? ''));
    $email = trim((string) ($studentData['email'] ?? ''));
    $telephone = trim((string) ($studentData['telephone'] ?? ''));
    $adresse = trim((string) ($studentData['adresse'] ?? ''));
    $filiere = trim((string) ($studentData['filiere'] ?? ''));
    $niveau = trim((string) ($studentData['niveau'] ?? ''));
    $annee = trim((string) ($studentData['annee_academique'] ?? ''));
    $dateInscription = trim((string) ($studentData['date_inscription'] ?? date('d/m/Y')));
    $password = trim((string) ($studentData['mot_de_passe'] ?? ''));
    $sexe = trim((string) ($studentData['sexe'] ?? ''));
    $dateNaissance = trim((string) ($studentData['date_naissance'] ?? ''));
    $nationalite = trim((string) ($studentData['nationalite'] ?? ''));

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 18);
    
    // Header background (Blue #00205B)
    $pdf->SetFillColor(0, 32, 91);
    $pdf->Rect(0, 0, 210, 40, 'F');
    // Red line (#C8102E)
    $pdf->SetFillColor(200, 16, 46);
    $pdf->Rect(0, 40, 210, 2.5, 'F');

    // Logo
    $logoPath = __DIR__ . '/../ressources/logo.jpeg';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 15, 6, 26, 26, 'JPEG');
    }

    // Title
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetXY(50, 12);
    $pdf->Cell(150, 10, escapePdfText('FICHE D\'INSCRIPTION - IPASE'), 0, 1, 'R');
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(50, 20);
    $pdf->Cell(150, 6, escapePdfText('Institut Professionnel Action Santé Éducation'), 0, 1, 'R');
    $pdf->SetXY(50, 25);
    $pdf->Cell(150, 6, escapePdfText('Année académique ' . $annee), 0, 1, 'R');

    // Reference
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(212, 175, 55); // Gold
    $pdf->SetXY(50, 32);
    $pdf->Cell(150, 6, escapePdfText('Réf. ' . $matricule), 0, 1, 'R');

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetY(50);

    // Encart étudiant
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetDrawColor(220, 225, 232);
    $pdf->Rect(15, 50, 180, 16, 'DF');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0, 32, 91);
    $pdf->SetXY(20, 52);
    $pdf->Cell(100, 8, escapePdfText(strtoupper($nom) . ' ' . $prenom), 0, 0);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(200, 16, 46);
    $pdf->Cell(70, 8, escapePdfText($filiere), 0, 1, 'R');
    
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(120, 130, 145);
    $pdf->SetXY(20, 58);
    $pdf->Cell(170, 6, escapePdfText('Inscrit(e) le ' . $dateInscription), 0, 1);
    
    $pdf->Ln(8);

    // Fonction pour les sections
    $drawSection = function($title, $y) use ($pdf) {
        $pdf->SetFillColor(0, 32, 91);
        $pdf->Rect(15, $y, 180, 8, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(18, $y + 1);
        $pdf->Cell(100, 6, escapePdfText($title), 0, 1);
        return $y + 12;
    };

    $drawFieldRow = function($label, $value, $y) use ($pdf) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(0, 32, 91);
        $pdf->SetXY(18, $y);
        $pdf->Cell(50, 5, escapePdfText($label), 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(40, 40, 40);
        $pdf->MultiCell(120, 5, escapePdfText($value), 0, 'L');
        return $pdf->GetY() + 2;
    };

    $y = $pdf->GetY();
    $y = $drawSection('1. Informations personnelles', $y);
    $y = $drawFieldRow('Nom de famille', $nom, $y);
    $y = $drawFieldRow('Prénom(s)', $prenom, $y);
    $y = $drawFieldRow('Sexe', $sexe, $y);
    $y = $drawFieldRow('Date de naissance', $dateNaissance, $y);
    $y = $drawFieldRow('Nationalité', $nationalite, $y);
    
    $y += 4;
    $y = $drawSection('2. Coordonnées & niveau d\'études', $y);
    $y = $drawFieldRow('Téléphone (WhatsApp)', $telephone, $y);
    $y = $drawFieldRow('Adresse e-mail', $email, $y);
    $y = $drawFieldRow('Adresse de résidence', $adresse, $y);
    $y = $drawFieldRow('Niveau d\'études', $niveau, $y);
    
    $y += 4;
    $y = $drawSection('3. Formation choisie', $y);
    $y = $drawFieldRow('Filière', $filiere, $y);
    
    $y += 8;
    // Identifiants
    $pdf->SetFillColor(255, 248, 248);
    $pdf->SetDrawColor(200, 16, 46);
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(15, $y, 180, 26, 'DF');
    $pdf->SetLineWidth(0.2); // reset
    
    $pdf->SetTextColor(200, 16, 46);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetXY(20, $y + 4);
    $pdf->Cell(100, 6, escapePdfText('Identifiants — Espace Étudiant IPASE'), 0, 1);
    
    $pdf->SetTextColor(40, 40, 40);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetXY(20, $y + 12);
    $pdf->Cell(30, 5, escapePdfText('Email :'), 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(100, 5, escapePdfText($email), 0, 1);
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetX(20);
    $pdf->Cell(30, 5, escapePdfText('Mot de passe :'), 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(100, 5, escapePdfText($password), 0, 1);
    
    $y += 32;
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->SetTextColor(120, 130, 145);
    $pdf->SetXY(15, $y);
    $pdf->MultiCell(180, 4, escapePdfText('Document officiel généré automatiquement. Conservez cette fiche et vos identifiants pour accéder au portail étudiant IPASE.'), 0, 'L');

    // Footer
    $pdf->SetDrawColor(120, 130, 145);
    $pdf->Line(15, 280, 195, 280);
    $pdf->SetXY(15, 282);
    $pdf->Cell(180, 5, escapePdfText('IPASE — Lomé, Agbalepedo | ipase.tg@gmail.com | +228 93 88 23 52'), 0, 0, 'C');

    $pdf->Output('F', $outputPath);
    return file_exists($outputPath);
}
