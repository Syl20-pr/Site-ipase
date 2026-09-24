const ADMIN_API = '../api/admin.php';
let ADMIN_STUDENTS = [];
let ACTIVE_TAB = 'students';

function showError(message) {
  const box = document.getElementById('admin-login-error');
  if (!box) return;
  box.textContent = message;
  box.classList.remove('hidden');
}

function clearError() {
  const box = document.getElementById('admin-login-error');
  if (!box) return;
  box.textContent = '';
  box.classList.add('hidden');
}

function renderStats(stats = {}) {
  const grid = document.getElementById('admin-stats-grid');
  if (!grid) return;

  const cards = [
    ['Étudiants', stats.total_etudiants ?? 0],
    ['Inscriptions', stats.total_inscriptions ?? 0],
    ['Messages', stats.total_messages ?? 0],
    ['Utilisateurs', stats.total_utilisateurs ?? 0],
  ];

  grid.innerHTML = cards.map(([label, value]) => `
    <div class="admin-stat-card">
      <small>${label}</small>
      <strong>${value}</strong>
    </div>
  `).join('');
}

function renderStudents(rows = []) {
  const panel = document.getElementById('admin-students-panel');
  if (!panel) return;
  ADMIN_STUDENTS = rows;

  if (!rows.length) {
    panel.innerHTML = '<div class="admin-row">Aucun étudiant trouvé.</div>';
    return;
  }

  panel.innerHTML = rows.map(student => `
    <div class="admin-row">
      <strong>${student.nom} ${student.prenom}</strong><br>
      <span>Matricule : ${student.matricule}</span><br>
      <span>Email : ${student.email}</span><br>
      <span>Filière : ${student.filiere} • Niveau : ${student.niveau}</span><br>
      <span>Statut : ${student.statut} • Montant payé : ${Number(student.montant_paye ?? 0).toFixed(2)}</span>
      <div class="toolbar">
        <input type="email" data-admin-mail="${student.id_etudiant}" value="${student.email}" />
        <input type="tel" data-admin-phone="${student.id_etudiant}" value="${student.telephone ?? ''}" />
        <select data-admin-statut="${student.id_etudiant}">
          <option value="En attente" ${student.statut === 'En attente' ? 'selected' : ''}>En attente</option>
          <option value="Validée" ${student.statut === 'Validée' ? 'selected' : ''}>Validée</option>
          <option value="Annulée" ${student.statut === 'Annulée' ? 'selected' : ''}>Annulée</option>
        </select>
        <input type="number" data-admin-montant="${student.id_etudiant}" value="${Number(student.montant_paye ?? 0).toFixed(2)}" />
        <textarea rows="2" data-admin-observations="${student.id_etudiant}" placeholder="Observations interne...">${student.observations ?? ''}</textarea>
        <button type="button" class="btn-submit-form" data-admin-update-id="${student.id_etudiant}">Mettre à jour</button>
      </div>
    </div>
  `).join('');
}

function renderMessages(messages = []) {
  const panel = document.getElementById('admin-messages-panel');
  if (!panel) return;

  if (!messages.length) {
    panel.innerHTML = '<div class="admin-row">Aucune boîte de réception.</div>';
    return;
  }

  panel.innerHTML = messages.map(msg => `
    <div class="admin-row">
      <strong>${msg.prenom} ${msg.nom}</strong><br>
      <span>${msg.email} • ${msg.telephone || 'Sans téléphone'}</span><br>
      <span>Objet : ${msg.objet}</span><br>
      <p>${msg.message}</p>
      <small>${msg.created_at}</small>
    </div>
  `).join('');
}

function renderReports(stats = {}) {
  const panel = document.getElementById('admin-report-panel');
  if (!panel) return;
  const items = (stats.par_filiere || []).map(item => `
    <div class="admin-row">${item.filiere} : ${item.total} étudiant(s)</div>
  `).join('');
  panel.innerHTML = items || '<div class="admin-row">Aucune statistique par filière.</div>';
}

function switchAdminTab(tab) {
  ACTIVE_TAB = tab;
  document.getElementById('admin-students-panel').classList.toggle('hidden', tab !== 'students');
  document.getElementById('admin-messages-panel').classList.toggle('hidden', tab !== 'messages');
  document.getElementById('admin-report-panel').classList.toggle('hidden', tab !== 'report');
}

async function fetchAdminJson(url, options = {}) {
  const requestOptions = {
    credentials: 'same-origin',
    ...options
  };

  let response;
  try {
    response = await fetch(url, requestOptions);
  } catch (error) {
    throw new Error('Impossible de joindre le serveur d’administration. Vérifiez que le serveur PHP est lancé sur localhost.');
  }

  const text = await response.text();
  if (!text) throw new Error('Réponse vide du serveur admin.');

  let data;
  try {
    data = JSON.parse(text);
  } catch {
    throw new Error('Le serveur admin a répondu avec un contenu non JSON.');
  }

  if (!response.ok || !data.success) {
    throw new Error(data.message || 'Erreur serveur admin.');
  }
  return data;
}

async function loadAdminDashboard() {
  const statsData = await fetchAdminJson(`${ADMIN_API}?action=stats`);
  renderStats(statsData.stats || {});
  renderReports(statsData.stats || {});

  const studentsData = await fetchAdminJson(`${ADMIN_API}?action=students`);
  renderStudents(studentsData.data || []);

  const messagesData = await fetchAdminJson(`${ADMIN_API}?action=messages`);
  renderMessages(messagesData.messages || []);
}

async function refreshStudentsFromFilters() {
  const params = new URLSearchParams();
  const filiere = document.getElementById('admin-filter-filiere')?.value || '';
  const niveau = document.getElementById('admin-filter-niveau')?.value || '';
  const statut = document.getElementById('admin-filter-statut')?.value || '';
  const search = document.getElementById('admin-search')?.value.trim() || '';

  if (filiere) params.set('filiere', filiere);
  if (niveau) params.set('niveau', niveau);
  if (statut) params.set('statut', statut);
  if (search) params.set('search', search);

  const data = await fetchAdminJson(`${ADMIN_API}?action=students&${params.toString()}`);
  renderStudents(data.data || []);
}

async function loginAdmin() {
  const username = document.getElementById('admin-username').value.trim();
  const password = document.getElementById('admin-password').value;

  try {
    const data = await fetchAdminJson(ADMIN_API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: new URLSearchParams({ action: 'login', username, password })
    });

    clearError();
    document.getElementById('admin-login-card').classList.add('hidden');
    document.getElementById('admin-dashboard-card').classList.remove('hidden');
    await loadAdminDashboard();
  } catch (err) {
    showError(err.message);
  }
}

async function logoutAdmin() {
  try {
    await fetchAdminJson(ADMIN_API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: new URLSearchParams({ action: 'logout' })
    });
  } catch {}
  window.location.reload();
}

async function updateStudent(studentId) {
  const email = document.querySelector(`[data-admin-mail="${studentId}"]`)?.value || '';
  const telephone = document.querySelector(`[data-admin-phone="${studentId}"]`)?.value || '';
  const statut = document.querySelector(`[data-admin-statut="${studentId}"]`)?.value || 'En attente';
  const montant = Number(document.querySelector(`[data-admin-montant="${studentId}"]`)?.value || 0);
  const observations = document.querySelector(`[data-admin-observations="${studentId}"]`)?.value || '';

  const data = await fetchAdminJson(ADMIN_API, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
    body: new URLSearchParams({
      id_etudiant: String(studentId),
      email,
      telephone,
      statut,
      montant_paye: String(montant),
      observations
    })
  });

  if (data.success) {
    await refreshStudentsFromFilters();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('admin-login-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    await loginAdmin();
  });

  document.getElementById('admin-logout-btn')?.addEventListener('click', logoutAdmin);

  ['students', 'messages', 'report'].forEach(tab => {
    document.getElementById(`btn-tab-${tab}`)?.addEventListener('click', () => switchAdminTab(tab));
  });

  document.getElementById('btn-admin-export-excel')?.addEventListener('click', () => {
    if (typeof XLSX === 'undefined') {
      showError('La dépendance Excel n’est pas chargée.');
      return;
    }

    if (!ADMIN_STUDENTS.length) {
      alert('Aucune liste d’étudiants pour export.');
      return;
    }

    const rows = ADMIN_STUDENTS.map((student, index) => ({
      '#': index + 1,
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

    const sheet = XLSX.utils.json_to_sheet(rows);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, sheet, 'IPASE Admin');
    XLSX.writeFile(workbook, 'IPASE_Admin_Export.xlsx');
  });

  [
    document.getElementById('admin-filter-filiere'),
    document.getElementById('admin-filter-niveau'),
    document.getElementById('admin-filter-statut'),
    document.getElementById('admin-search')
  ].forEach(el => {
    if (!el) return;
    el.addEventListener('input', refreshStudentsFromFilters);
    el.addEventListener('change', refreshStudentsFromFilters);
  });

  document.getElementById('admin-students-panel')?.addEventListener('click', async (e) => {
    const button = e.target.closest('[data-admin-update-id]');
    if (!button) return;
    await updateStudent(Number(button.getAttribute('data-admin-update-id')));
  });
});
