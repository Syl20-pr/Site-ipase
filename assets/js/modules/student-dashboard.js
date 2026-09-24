// ===== student-dashboard.js — Affichage du tableau de bord étudiant après connexion =====
function hideSignupButtons() {
    const heroBtn = document.getElementById('btn-hero-inscription');
    const floatBtn = document.getElementById('btn-float-inscription');

    if (heroBtn) heroBtn.style.display = 'none';
    if (floatBtn) floatBtn.style.display = 'none';
}

function showStudentDashboard(session) {
    const loginBox = document.getElementById('login-box');
    const dashboard = document.getElementById('student-dashboard');
    const welcomeTitle = document.getElementById('student-welcome-title');
    const passwordPanel = document.getElementById('password-change-panel');
    const sidebarName = document.getElementById('student-sidebar-name');
    const sidebarEmail = document.getElementById('student-sidebar-email');
    const profileName = document.getElementById('student-profile-name');
    const profileMatricule = document.getElementById('student-profile-matricule');
    const profileEmail = document.getElementById('student-profile-email');

    if (loginBox) loginBox.style.display = 'none';
    if (dashboard) dashboard.style.display = 'block';
    if (welcomeTitle) {
        welcomeTitle.textContent = `Bienvenue ${session.prenom} ${session.nom}`;
    }
    if (passwordPanel) {
        passwordPanel.style.display = session.mustChangePassword ? 'block' : 'none';
    }
    if (sidebarName) sidebarName.textContent = `${session.prenom} ${session.nom}`;
    if (sidebarEmail) sidebarEmail.textContent = session.email;
    if (profileName) profileName.textContent = `${session.prenom} ${session.nom}`;
    if (profileMatricule) profileMatricule.textContent = session.matricule || '—';
    if (profileEmail) profileEmail.textContent = session.email;
    
    const profileFiliere = document.getElementById('student-profile-filiere');
    const profileNiveau = document.getElementById('student-profile-niveau');
    if (profileFiliere) profileFiliere.textContent = session.filiere || 'Non assignée';
    if (profileNiveau) profileNiveau.textContent = session.niveau || '—';

    // Remplissage dynamique des cours et notes (mock)
    const coursesList = document.getElementById('student-courses-list');
    const notesList = document.getElementById('student-notes-list');
    
    if (coursesList) {
        if (session.filiere && session.filiere !== 'Non assignée') {
            coursesList.innerHTML = `
                <div class="student-info-card"><strong>Introduction : ${session.filiere}</strong><br>Syllabus • PDF de présentation</div>
                <div class="student-info-card"><strong>Méthodologie de travail</strong><br>Documents • Conseils</div>
            `;
        } else {
            coursesList.innerHTML = `<div class="student-info-card"><em>Vos cours seront affichés ici dès la rentrée.</em></div>`;
        }
    }
    
    if (notesList) {
        notesList.innerHTML = `<div class="student-info-card"><em>Aucune note disponible pour le moment.</em></div>`;
    }
}

function applyStudentSessionState() {
    const session = JSON.parse(localStorage.getItem(IPASE_SESSION_KEY) || 'null');
    if (session) {
        hideSignupButtons();
        showStudentDashboard(session);
    }
}

