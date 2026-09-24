// ===== utils.js — Fonctions utilitaires génériques (navigation, formats, stockage local) =====
// ===== config.js — Constantes globales (clés de stockage, URLs API, libellés) =====
function switchPage(pageId, anchor = null) {
    const pages = document.querySelectorAll('.page-section');

    pages.forEach(page => {
        page.classList.toggle('active', page.id === pageId);
    });

    document.querySelectorAll('.nav a, .footer-links a').forEach(link => {
        link.classList.toggle('active', link.getAttribute('data-page-target') === pageId);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });

    if (anchor) {
        setTimeout(() => {
            const targetElem = document.getElementById(anchor);
            if (targetElem) {
                targetElem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                targetElem.classList.add('highlight-card');
                setTimeout(() => targetElem.classList.remove('highlight-card'), 2200);
            }
        }, 350);
    }
}

function switchStudentTab(tabId) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tabId);
    });

    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.toggle('active', content.id === tabId);
    });
}

function getStoredRegistrations() {
    try {
        return JSON.parse(localStorage.getItem(IPASE_STORAGE_KEY)) || {};
    } catch {
        return {};
    }
}

function saveRegistration(email, data) {
    const registrations = getStoredRegistrations();
    registrations[email.toLowerCase()] = data;
    localStorage.setItem(IPASE_STORAGE_KEY, JSON.stringify(registrations));
}

function generateRandomPassword(length = 10) {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789@#!$';
    let password = '';
    for (let i = 0; i < length; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}

function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = () => reject(new Error('Impossible de lire le fichier sélectionné.'));
        reader.readAsDataURL(file);
    });
}

function formatDateFr(dateStr) {
    if (!dateStr) return '—';
    const date = new Date(dateStr);
    if (Number.isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
}

function formatDateShort(dateStr) {
    if (!dateStr) return '—';
    const date = new Date(dateStr);
    if (Number.isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString('fr-FR');
}

function formatSexe(sexe) {
    if (sexe === 'M') return 'Masculin';
    if (sexe === 'F') return 'Féminin';
    return sexe || '—';
}

function generateReferenceNumber(registeredAt) {
    const date = new Date(registeredAt);
    const year = date.getFullYear();
    const stamp = String(date.getTime()).slice(-6);
    return `IPASE-${year}-${stamp}`;
}

async function submitRegistrationToServer(registration) {
    const payload = {
        nom: registration.nom,
        prenom: registration.prenom,
        date_naissance: registration.dob,
        sexe: registration.sexe,
        email: registration.email,
        id_filiere: IPASE_DOMAIN_TO_FILIERE[registration.domain] || 1,
        id_niveau: IPASE_LEVEL_TO_NIVEAU[registration.level] || 1,
        lieu_naissance: registration.pob,
        nationalite: registration.nationality,
        telephone: registration.phone,
        adresse: `${registration.address}, ${registration.country}`,
        nom_tuteur: 'Parent / Tuteur',
        tel_tuteur: registration.parentPhone,
        mot_de_passe: registration.password,
        piece_jointe: registration.documentBase64 || null,
        piece_jointe_nom: registration.documentName || null
    };

    const response = await fetch(IPASE_API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    const data = await response.json();
    if (!response.ok || !data.success) {
        throw new Error(data.message || data.errors?.join(' ') || 'Erreur lors de l\'enregistrement du dossier.');
    }

    return data;
}

function loadImageAsDataUrl(src) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            canvas.getContext('2d').drawImage(img, 0, 0);
            resolve(canvas.toDataURL('image/jpeg', 0.92));
        };
        img.onerror = () => reject(new Error('Logo introuvable'));
        img.src = src;
    });
}

