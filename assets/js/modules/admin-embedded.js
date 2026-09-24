// ===== admin-embedded.js — Rendu admin intégrable (non utilisé sur cette page, admin/ a son propre app.js) =====
function formatAdminStatsCard(label, value) {
    return `
        <div class="dash-card">
            <i class="fa-solid fa-chart-column"></i>
            <h4>${label}</h4>
            <p>${value}</p>
        </div>
    `;
}

function renderAdminStats(stats) {
    const statsGrid = document.getElementById('admin-stats-grid');
    if (!statsGrid) return;

    const cards = [
        ['Étudiants', stats.total_etudiants ?? 0],
        ['Inscriptions', stats.total_inscriptions ?? 0],
        ['Messages', stats.total_messages ?? 0],
        ['Utilisateurs admin', stats.total_utilisateurs ?? 0]
    ];

    statsGrid.innerHTML = cards.map(([label, value]) => formatAdminStatsCard(label, value)).join('');

    const reportList = document.getElementById('admin-report-list');
    if (reportList) {
        const parFiliere = (stats.par_filiere || [])
            .map(item => `<div class="student-info-card"><strong>${item.filiere}</strong><br>${item.total} inscrit(s)</div>`)
            .join('');
        reportList.innerHTML = parFiliere || '<div class="student-info-card">Aucune statistique disponible.</div>';
    }
}

function renderAdminStudents(rows) {
    const container = document.getElementById('admin-students-table');
    if (!container) return;

    IPASE_ADMIN_STUDENTS = rows || [];

    if (!rows.length) {
        container.innerHTML = '<div class="student-info-card">Aucun étudiant trouvé pour les filtres actuels.</div>';
        return;
    }

    container.innerHTML = rows.map(student => `
        <div class="student-info-card" style="display:block;">
            <div style="display:grid; gap:8px;">
                <strong>${student.nom} ${student.prenom}</strong>
                <span>Matricule : ${student.matricule}</span>
                <span>Email : ${student.email}</span>
                <span>Filière : ${student.filiere} • Niveau : ${student.niveau}</span>
                <span>Statut : ${student.statut} • Montant : ${Number(student.montant_paye || 0).toFixed(2)} FCFA</span>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" data-admin-mail="${student.id_etudiant}" value="${student.email}" />
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" data-admin-phone="${student.id_etudiant}" value="${student.telephone || ''}" />
                    </div>
                    <div class="form-group">
                        <label>Statut</label>
                        <select data-admin-statut="${student.id_etudiant}">
                            <option value="En attente" ${student.statut === 'En attente' ? 'selected' : ''}>En attente</option>
                            <option value="Validée" ${student.statut === 'Validée' ? 'selected' : ''}>Validée</option>
                            <option value="Annulée" ${student.statut === 'Annulée' ? 'selected' : ''}>Annulée</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Montant payé</label>
                        <input type="number" min="0" data-admin-montant="${student.id_etudiant}" value="${Number(student.montant_paye || 0).toFixed(2)}" />
                    </div>
                    <div class="form-group full-width">
                        <label>Observations</label>
                        <textarea data-admin-observations="${student.id_etudiant}" rows="2" placeholder="Observations interne..."></textarea>
                    </div>
                </div>
                <button type="button" class="btn-submit-form" data-admin-update-id="${student.id_etudiant}">
                    <i class="fa-solid fa-pen"></i> Modifier
                </button>
            </div>
        </div>
    `).join('');
}

function renderAdminMessages(messages) {
    const container = document.getElementById('admin-messages-list');
    if (!container) return;

    if (!messages.length) {
        container.innerHTML = '<div class="student-info-card">Aucun message reçu pour le moment.</div>';
        return;
    }

    container.innerHTML = messages.map(msg => `
        <div class="student-info-card">
            <strong>${msg.prenom} ${msg.nom}</strong><br>
            <span>${msg.email} • ${msg.telephone || 'Sans téléphone'}</span><br>
            <span><em>${msg.objet}</em></span><br>
            <p>${msg.message}</p>
            <small>${msg.created_at}</small>
        </div>
    `).join('');
}

async function loadAdminData() {
    try {
        const [statsResponse, studentsResponse, messagesResponse] = await Promise.all([
            fetch(`${IPASE_ADMIN_API_URL}?action=stats`),
            fetch(`${IPASE_ADMIN_API_URL}?action=students`),
            fetch(`${IPASE_ADMIN_API_URL}?action=messages`)
        ]);

        const statsData = await statsResponse.json();
        const studentsData = await studentsResponse.json();
        const messagesData = await messagesResponse.json();

        if (statsResponse.ok && statsData.success) {
            renderAdminStats(statsData.stats);
        }
        if (studentsResponse.ok && studentsData.success) {
            renderAdminStudents(studentsData.data || []);
        }
        if (messagesResponse.ok && messagesData.success) {
            renderAdminMessages(messagesData.messages || []);
        }
    } catch (err) {
        console.error('[IPASE Admin] Chargement administrateur impossible :', err);
    }
}

function exportAdminStudentsToExcel() {
    if (typeof XLSX === 'undefined') {
        alert('Le support Excel n’est pas chargé dans cette session.');
        return;
    }

    if (!IPASE_ADMIN_STUDENTS.length) {
        alert('Aucun étudiant disponible pour l’export.');
        return;
    }

    const data = IPASE_ADMIN_STUDENTS.map((student, index) => ({
        'N°': index + 1,
        'Matricule': student.matricule,
        'Nom': student.nom,
        'Prénom': student.prenom,
        'Email': student.email,
        'Téléphone': student.telephone || '—',
        'Filière': student.filiere,
        'Niveau': student.niveau,
        'Année': student.annee_academique,
        'Statut': student.statut,
        'Montant payé': Number(student.montant_paye || 0)
    }));

    const sheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, sheet, 'Etudiants IPASE');
    XLSX.writeFile(workbook, `IPASE_Admin_Etudiants_${new Date().toISOString().slice(0, 10)}.xlsx`);
}

