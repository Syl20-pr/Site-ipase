// ===== pdf-generator.js — Génération du PDF de la fiche d'inscription (jsPDF) =====
async function generateRegistrationPDF(registration) {
    if (!window.jspdf || !window.jspdf.jsPDF) {
        console.warn('[IPASE] jsPDF non disponible — PDF non généré.');
        return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit: 'mm', format: 'a4' });
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const margin = 14;
    const contentWidth = pageWidth - margin * 2;
    let y = 0;

    const ensureSpace = (needed) => {
        if (y + needed > pageHeight - 18) {
            doc.addPage();
            y = 18;
        }
    };

    const drawPageFooter = () => {
        doc.setDrawColor(...IPASE_COLORS.midGray);
        doc.setLineWidth(0.2);
        doc.line(margin, pageHeight - 12, pageWidth - margin, pageHeight - 12);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(8);
        doc.setTextColor(...IPASE_COLORS.midGray);
        doc.text('IPASE — Lomé, Agbalepedo | ipase.tg@gmail.com | +228 93 88 23 52', pageWidth / 2, pageHeight - 7, { align: 'center' });
    };

    // En-tête institutionnel
    doc.setFillColor(...IPASE_COLORS.blue);
    doc.rect(0, 0, pageWidth, 42, 'F');
    doc.setFillColor(...IPASE_COLORS.red);
    doc.rect(0, 42, pageWidth, 2.5, 'F');

    try {
        const logoData = await loadImageAsDataUrl(IPASE_LOGO_PATH);
        doc.addImage(logoData, 'JPEG', margin, 7, 26, 26);
    } catch {
        doc.setFillColor(...IPASE_COLORS.white);
        doc.circle(margin + 13, 20, 12, 'F');
        doc.setTextColor(...IPASE_COLORS.blue);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(11);
        doc.text('IPASE', margin + 13, 21, { align: 'center' });
    }

    doc.setTextColor(...IPASE_COLORS.white);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(17);
    doc.text('FICHE D\'INSCRIPTION', pageWidth - margin, 16, { align: 'right' });
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.text('Institut Professionnel Action Santé Éducation', pageWidth - margin, 23, { align: 'right' });
    doc.text('Année académique 2026-2027', pageWidth - margin, 29, { align: 'right' });
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(9);
    doc.setTextColor(...IPASE_COLORS.gold);
    doc.text(`Réf. ${registration.reference || '—'}`, pageWidth - margin, 36, { align: 'right' });

    y = 52;

    // Bandeau candidat
    doc.setFillColor(...IPASE_COLORS.lightGray);
    doc.setDrawColor(220, 225, 232);
    doc.roundedRect(margin, y, contentWidth, 16, 2, 2, 'FD');
    doc.setTextColor(...IPASE_COLORS.blue);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(13);
    doc.text(`${registration.prenom} ${registration.nom}`.toUpperCase(), margin + 5, y + 7);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(...IPASE_COLORS.midGray);
    doc.text(`Inscrit(e) le ${formatDateFr(registration.registeredAt)}`, margin + 5, y + 13);
    doc.setTextColor(...IPASE_COLORS.red);
    doc.setFont('helvetica', 'bold');
    doc.text(registration.filiere || '—', pageWidth - margin - 5, y + 10, { align: 'right', maxWidth: 90 });
    y += 24;

    const drawSection = (title) => {
        ensureSpace(14);
        doc.setFillColor(...IPASE_COLORS.blue);
        doc.roundedRect(margin, y, contentWidth, 8, 1.5, 1.5, 'F');
        doc.setTextColor(...IPASE_COLORS.white);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text(title, margin + 4, y + 5.5);
        y += 11;
    };

    const drawFieldRow = (label, value) => {
        ensureSpace(9);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);
        doc.setTextColor(...IPASE_COLORS.blue);
        doc.text(label, margin + 2, y);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(40, 40, 40);
        const lines = doc.splitTextToSize(String(value || '—'), contentWidth - 52);
        doc.text(lines, margin + 50, y);
        y += Math.max(6, lines.length * 5) + 1.5;
    };

    drawSection('1. Informations personnelles');
    drawFieldRow('Nom de famille', registration.nom);
    drawFieldRow('Prénom(s)', registration.prenom);
    drawFieldRow('Sexe', formatSexe(registration.sexe));
    drawFieldRow('Date de naissance', formatDateFr(registration.dob));
    drawFieldRow('Lieu de naissance', registration.pob);
    drawFieldRow('Nationalité', registration.nationality);
    y += 3;

    drawSection('2. Coordonnées & niveau d\'études');
    drawFieldRow('Téléphone (WhatsApp)', registration.phone);
    drawFieldRow('Adresse e-mail', registration.email);
    drawFieldRow('Adresse de résidence', registration.address);
    drawFieldRow('Pays de résidence', registration.country);
    drawFieldRow('Niveau d\'études', registration.level);
    drawFieldRow('Téléphone tuteur / parent', registration.parentPhone);
    y += 3;

    drawSection('3. Formation choisie');
    drawFieldRow('Domaine', registration.domainLabel);
    drawFieldRow('Filière', registration.filiere);
    y += 4;

    // Encadré identifiants
    ensureSpace(36);
    doc.setFillColor(255, 248, 248);
    doc.setDrawColor(...IPASE_COLORS.red);
    doc.setLineWidth(0.6);
    doc.roundedRect(margin, y, contentWidth, 30, 2, 2, 'FD');
    doc.setTextColor(...IPASE_COLORS.red);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11);
    doc.text('Identifiants — Espace Étudiant IPASE', margin + 5, y + 8);
    doc.setFontSize(9.5);
    doc.setTextColor(40, 40, 40);
    doc.setFont('helvetica', 'bold');
    doc.text('Email :', margin + 5, y + 16);
    doc.setFont('helvetica', 'normal');
    doc.text(registration.email, margin + 22, y + 16);
    doc.setFont('helvetica', 'bold');
    doc.text('Mot de passe :', margin + 5, y + 23);
    doc.setFont('helvetica', 'normal');
    doc.text(registration.password, margin + 32, y + 23);
    y += 36;

    ensureSpace(12);
    doc.setFont('helvetica', 'italic');
    doc.setFontSize(8.5);
    doc.setTextColor(...IPASE_COLORS.midGray);
    doc.text(
        'Document officiel généré automatiquement. Conservez cette fiche et vos identifiants pour accéder au portail étudiant IPASE.',
        margin,
        y,
        { maxWidth: contentWidth }
    );

    drawPageFooter();

    const safeName = `${registration.nom}_${registration.prenom}`.replace(/[^a-zA-Z0-9_-]/g, '_');
    doc.save(`IPASE_Fiche_Inscription_${safeName}.pdf`);
}

