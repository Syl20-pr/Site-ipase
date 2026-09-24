// ===== excel-export.js — Export des inscriptions locales vers Excel (XLSX) =====
function getRegistrationsList() {
    return Object.values(getStoredRegistrations())
        .sort((a, b) => new Date(a.registeredAt) - new Date(b.registeredAt));
}

function registrationToExcelRow(reg, index) {
    return {
        'N°': index + 1,
        'Référence': reg.reference || '—',
        'Date inscription': formatDateShort(reg.registeredAt),
        'Nom': reg.nom,
        'Prénom(s)': reg.prenom,
        'Sexe': formatSexe(reg.sexe),
        'Date de naissance': formatDateShort(reg.dob),
        'Lieu de naissance': reg.pob,
        'Nationalité': reg.nationality,
        'Email': reg.email,
        'Téléphone (WhatsApp)': reg.phone,
        'Téléphone parent/tuteur': reg.parentPhone,
        'Adresse': reg.address,
        'Pays': reg.country,
        'Niveau d\'études': reg.level,
        'Domaine': reg.domainLabel,
        'Filière': reg.filiere
    };
}

function exportRegistrationsToExcel() {
    if (typeof XLSX === 'undefined') {
        console.warn('[IPASE] SheetJS non disponible — export Excel ignoré.');
        return false;
    }

    const list = getRegistrationsList();
    if (list.length === 0) {
        alert('Aucune inscription enregistrée pour le moment.');
        return false;
    }

    const rows = list.map((reg, index) => registrationToExcelRow(reg, index));
    const worksheet = XLSX.utils.json_to_sheet(rows);

    worksheet['!cols'] = [
        { wch: 5 }, { wch: 16 }, { wch: 14 }, { wch: 18 }, { wch: 18 }, { wch: 10 },
        { wch: 14 }, { wch: 18 }, { wch: 14 }, { wch: 28 }, { wch: 18 }, { wch: 18 },
        { wch: 28 }, { wch: 12 }, { wch: 14 }, { wch: 32 }, { wch: 36 }
    ];

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Inscriptions IPASE');

    const today = new Date().toISOString().slice(0, 10);
    XLSX.writeFile(workbook, `IPASE_Liste_Inscriptions_${today}.xlsx`);
    return true;
}

