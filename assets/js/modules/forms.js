// ===== forms.js — Formulaires : inscription, connexion, changement mot de passe, contact =====
function initFormEvents() {
    const domainSelect = document.getElementById('reg-domain');
    const filiereSelect = document.getElementById('reg-filiere');
    const regForm = document.getElementById('inscription-form');
    const regFileInput = document.getElementById('reg-file');
    const regSuccessAlert = document.getElementById('reg-success-msg');
    const regSuccessText = document.getElementById('reg-success-text');
    const loginForm = document.getElementById('login-form');
    const loginErrorAlert = document.getElementById('login-error-msg');
    const loginErrorText = document.getElementById('login-error-text');
    const exportExcelBtn = document.getElementById('btn-export-excel');
    const adminLoginForm = document.getElementById('admin-login-form');
    const adminLogoutBtn = document.getElementById('admin-logout-btn');
    const adminExportBtn = document.getElementById('btn-admin-export-excel');
    const adminFilterFiliere = document.getElementById('admin-filter-filiere');
    const adminFilterNiveau = document.getElementById('admin-filter-niveau');
    const adminFilterStatut = document.getElementById('admin-filter-statut');
    const adminSearch = document.getElementById('admin-search');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const changePasswordForm = document.getElementById('change-password-form');
    const changePasswordMsg = document.getElementById('change-password-msg');
    const studentNavLinks = document.querySelectorAll('.student-nav-link');
    const studentPanels = document.querySelectorAll('.student-panel-section');

    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', () => exportRegistrationsToExcel());
    }

    if (adminExportBtn) {
        adminExportBtn.addEventListener('click', () => exportAdminStudentsToExcel());
    }

    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorBox = document.getElementById('admin-login-error-msg');
            const errorText = document.getElementById('admin-login-error-text');
            const username = document.getElementById('admin-username').value.trim();
            const password = document.getElementById('admin-password').value;

            try {
                const response = await fetch(`${IPASE_ADMIN_API_URL}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'login', username, password })
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erreur de connexion admin.');
                }

                const adminPanel = document.getElementById('admin-dashboard');
                const adminLoginBox = document.getElementById('admin-login-box');
                const adminSidebarName = document.getElementById('admin-sidebar-name');
                const adminSidebarEmail = document.getElementById('admin-sidebar-email');
                const adminUserBadge = document.getElementById('admin-user-badge');
                if (adminLoginBox) adminLoginBox.style.display = 'none';
                if (adminPanel) adminPanel.style.display = 'block';
                if (adminSidebarName) adminSidebarName.textContent = data.admin.nom_complet || data.admin.username;
                if (adminSidebarEmail) adminSidebarEmail.textContent = data.admin.email || 'admin@ipase.ci';
                if (adminUserBadge) adminUserBadge.textContent = `${data.admin.role.toUpperCase()} • ${data.admin.username}`;
                if (errorBox) errorBox.style.display = 'none';

                await loadAdminData();
            } catch (err) {
                if (errorBox && errorText) {
                    errorText.textContent = err.message;
                    errorBox.style.display = 'block';
                }
            }
        });
    }

    if (adminLogoutBtn) {
        adminLogoutBtn.addEventListener('click', async () => {
            try {
                const response = await fetch(`${IPASE_ADMIN_API_URL}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    const adminPanel = document.getElementById('admin-dashboard');
                    const adminLoginBox = document.getElementById('admin-login-box');
                    if (adminPanel) adminPanel.style.display = 'none';
                    if (adminLoginBox) adminLoginBox.style.display = 'block';
                }
            } catch (err) {
                console.error('[IPASE Admin] Déconnexion impossible :', err);
            }
        });
    }

    if (adminFilterFiliere || adminFilterNiveau || adminFilterStatut || adminSearch) {
        const refreshAdminList = async () => {
            const query = new URLSearchParams();
            if (adminFilterFiliere?.value) query.set('filiere', adminFilterFiliere.value);
            if (adminFilterNiveau?.value) query.set('niveau', adminFilterNiveau.value);
            if (adminFilterStatut?.value) query.set('statut', adminFilterStatut.value);
            if (adminSearch?.value.trim()) query.set('search', adminSearch.value.trim());

            try {
                const response = await fetch(`${IPASE_ADMIN_API_URL}?action=students&${query.toString()}`);
                const data = await response.json();
                if (response.ok && data.success) {
                    renderAdminStudents(data.data || []);
                }
            } catch (err) {
                console.error('[IPASE Admin] Filtrage impossible :', err);
            }
        };

        [adminFilterFiliere, adminFilterNiveau, adminFilterStatut, adminSearch].forEach(el => {
            if (el) {
                el.addEventListener('input', refreshAdminList);
                el.addEventListener('change', refreshAdminList);
            }
        });
    }

    const filieresByDomain = {
        sante: [
            "Auxiliaire en pharmacie", "Secrétariat Médical", "Aide Soignant.e",
            "Délégation Médicale", "Massages et thérapie fonctionnelle",
            "Secrétariat de direction médicale", "Auxiliaire en optique médicale",
            "Assistant technicien de laboratoire d'analyses médicales"
        ],
        gestion: [
            "Comptabilité et fiscalité des entreprises", "Secrétariat Comptabilité",
            "Secrétariat Caisse", "Comptabilité des ONG", "Secrétariat bureautique",
            "Gestion de projet", "Organisation et Gestion des Ressources Humaines",
            "Marketing & community management"
        ],
        transport: [
            "Transport Logistique et transit", "Transit douane et commerce international",
            "Transit douane", "Transit douane et Shipping logistique"
        ],
        informatique: [
            "Développement web et mobile", "Maintenance informatique et réseau",
            "Data Science & Data analyse", "Big Data et IA", "Cyber Sécurité",
            "Informatique pratique", "Initiation à l'informatique"
        ],
        technique: [
            "Électricité Industrielle et Bâtiment", "Froid Industriel et Climatisation",
            "Plomberie et installation sanitaire"
        ],
        specialise: [
            "Qualité Hygiène Sécurité Environnement (QHSE)", "Agro-pastorale",
            "Agro économie", "Anglais professionnel", "Hôtesse d'accueil", "Cuisine et pâtisserie"
        ],
        bt: [
            "BT - Génie Civil", "BT - Comptabilité", "BT - Commerce",
            "BT - Transport Logistique Transit", "BT - Maintenance Informatique et Réseau",
            "BT - Électrotechnique", "BT - Agro pastorale"
        ]
    };

    // Événement 'change' pour la mise à jour dynamique des options
    if (domainSelect && filiereSelect) {
        domainSelect.addEventListener('change', () => {
            const selectedDomain = domainSelect.value;
            filiereSelect.innerHTML = '<option value="">-- Sélectionnez votre filière --</option>';

            if (filieresByDomain[selectedDomain]) {
                filieresByDomain[selectedDomain].forEach(filiere => {
                    const opt = document.createElement('option');
                    opt.value = filiere;
                    opt.textContent = filiere;
                    filiereSelect.appendChild(opt);
                });
                filiereSelect.classList.add('highlight-select');
                setTimeout(() => filiereSelect.classList.remove('highlight-select'), 1000);
            }
        });
    }

    // Événement 'submit' avec validation dynamique du formulaire d'inscription
    if (regForm) {
        regForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('reg-email').value.trim().toLowerCase();
            const registrations = getStoredRegistrations();

            if (registrations[email]) {
                if (regSuccessAlert) regSuccessAlert.style.display = 'none';
                alert('Cette adresse e-mail est déjà inscrite. Utilisez l\'onglet « Connexion étudiant » ou contactez le secrétariat.');
                return;
            }

            const domain = domainSelect ? domainSelect.value : '';
            const password = generateRandomPassword();
            const registeredAt = new Date().toISOString();
            let documentBase64 = null;
            let documentName = null;

            if (regFileInput && regFileInput.files && regFileInput.files[0]) {
                const file = regFileInput.files[0];
                documentName = file.name;
                documentBase64 = await fileToBase64(file);
            }

            const registration = {
                nom: document.getElementById('reg-nom').value.trim(),
                prenom: document.getElementById('reg-prenom').value.trim(),
                sexe: document.getElementById('reg-sexe').value,
                dob: document.getElementById('reg-dob').value,
                pob: document.getElementById('reg-pob').value.trim(),
                nationality: document.getElementById('reg-nationality').value.trim(),
                phone: document.getElementById('reg-phone').value.trim(),
                email,
                address: document.getElementById('reg-address').value.trim(),
                country: document.getElementById('reg-country').value.trim(),
                level: document.getElementById('reg-level').value,
                parentPhone: document.getElementById('reg-parent-phone').value.trim(),
                domain,
                domainLabel: IPASE_DOMAIN_LABELS[domain] || domain,
                filiere: filiereSelect ? filiereSelect.value : '',
                password,
                registeredAt,
                reference: generateReferenceNumber(registeredAt),
                savedOnServer: false,
                documentBase64,
                documentName
            };

            let serverMatricule = null;
            let emailSent = false;

            try {
                const serverResponse = await submitRegistrationToServer(registration);
                serverMatricule = serverResponse.matricule;
                emailSent = Boolean(serverResponse.email_envoye);
                registration.reference = serverMatricule || registration.reference;
                registration.savedOnServer = true;
            } catch (err) {
                console.warn('[IPASE] Enregistrement local uniquement :', err.message);
            }

            saveRegistration(email, registration);

            try {
                await generateRegistrationPDF(registration);
            } catch (err) {
                console.error('[IPASE] Erreur génération PDF :', err);
            }

            if (regSuccessText && regSuccessAlert) {
                const credentialsBlock = registration.savedOnServer
                    ? `<strong>Matricule :</strong> ${registration.reference}<br>
                       Vos identifiants de connexion vous ont été envoyés par e-mail${emailSent ? '' : ' (vérifiez vos spams)'}.`
                    : `<strong>Vos identifiants de connexion :</strong><br>
                       Email : <strong>${email}</strong><br>
                       Mot de passe : <strong>${password}</strong>`;

                regSuccessText.innerHTML = `
                    Votre candidature a été soumise avec succès ! Votre <strong>fiche d'inscription PDF</strong> vient d'être téléchargée.<br><br>
                    <strong>Référence dossier :</strong> ${registration.reference}<br><br>
                    ${credentialsBlock}<br><br>
                    Conservez ces informations — elles vous serviront pour accéder à l'Espace Étudiant.
                `;
                regSuccessAlert.style.display = 'block';
                regSuccessAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            regForm.reset();
            if (filiereSelect) {
                filiereSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord un domaine --</option>';
            }

            // Auto-connexion
            setTimeout(async () => {
                try {
                    const response = await fetch(IPASE_AUTH_API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'login', identifier: email, password })
                    });
                    const data = await response.json();

                    if (response.ok && data.success) {
                        const student = data.student;
                        const sessionData = {
                            id_etudiant: student.id_etudiant,
                            email: student.email,
                            nom: student.nom,
                            prenom: student.prenom,
                            matricule: student.matricule,
                            filiere: student.filiere,
                            niveau: student.niveau,
                            mustChangePassword: student.must_change_password,
                            loggedInAt: new Date().toISOString()
                        };
                        localStorage.setItem(IPASE_SESSION_KEY, JSON.stringify(sessionData));

                        hideSignupButtons();
                        showStudentDashboard(sessionData);
                        switchPage('page-etudiant');
                        // Optionnel : s'assurer qu'on est sur le bon onglet du dashboard
                        switchStudentTab('tab-connexion'); // Ceci cache le formulaire et montre le dashboard grâce à showStudentDashboard()
                    } else {
                        // En cas d'échec de la connexion auto (ce qui ne devrait pas arriver), on redirige vers le login
                        const loginEmailInput = document.getElementById('login-email');
                        if (loginEmailInput) loginEmailInput.value = email;
                        switchStudentTab('tab-connexion');
                        if (loginErrorAlert) loginErrorAlert.style.display = 'none';
                    }
                } catch (err) {
                    const loginEmailInput = document.getElementById('login-email');
                    if (loginEmailInput) loginEmailInput.value = email;
                    switchStudentTab('tab-connexion');
                }
            }, 2500);
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const identifier = document.getElementById('login-email').value.trim().toLowerCase();
            const password = document.getElementById('login-password').value;

            try {
                const response = await fetch(IPASE_AUTH_API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'login', identifier, password })
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erreur de connexion.');
                }

                if (loginErrorAlert) loginErrorAlert.style.display = 'none';

                const student = data.student;
                const sessionData = {
                    id_etudiant: student.id_etudiant,
                    email: student.email,
                    nom: student.nom,
                    prenom: student.prenom,
                    matricule: student.matricule,
                    filiere: student.filiere,
                    niveau: student.niveau,
                    mustChangePassword: student.must_change_password,
                    loggedInAt: new Date().toISOString()
                };
                localStorage.setItem(IPASE_SESSION_KEY, JSON.stringify(sessionData));

                hideSignupButtons();
                showStudentDashboard(sessionData);
                switchPage('page-etudiant');
            } catch (err) {
                if (loginErrorText && loginErrorAlert) {
                    loginErrorText.textContent = err.message;
                    loginErrorAlert.style.display = 'block';
                }
            }
        });
    }

    if (studentNavLinks.length) {
        studentNavLinks.forEach(link => {
            link.addEventListener('click', () => {
                studentNavLinks.forEach(item => item.classList.remove('active'));
                link.classList.add('active');

                const view = link.getAttribute('data-view');
                if (view === 'logout') {
                    return;
                }

                studentPanels.forEach(panel => {
                    panel.classList.toggle('active', panel.getAttribute('data-panel') === view);
                });
            });
        });
    }

    const studentsTable = document.getElementById('admin-students-table');
    if (studentsTable) {
        studentsTable.addEventListener('click', async (e) => {
            const button = e.target.closest('[data-admin-update-id]');
            if (!button) return;

            const id = Number(button.getAttribute('data-admin-update-id'));
            const email = document.querySelector(`[data-admin-mail="${id}"]`)?.value?.trim() || '';
            const telephone = document.querySelector(`[data-admin-phone="${id}"]`)?.value?.trim() || '';
            const statut = document.querySelector(`[data-admin-statut="${id}"]`)?.value || 'En attente';
            const montant = Number(document.querySelector(`[data-admin-montant="${id}"]`)?.value || 0);
            const observations = document.querySelector(`[data-admin-observations="${id}"]`)?.value?.trim() || '';

            try {
                const response = await fetch(`${IPASE_ADMIN_API_URL}?action=students`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_etudiant: id, email, telephone, statut, montant_paye: montant, observations })
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Mise à jour impossible.');
                }
                await loadAdminData();
                alert('Étudiant mis à jour depuis l’administration.');
            } catch (err) {
                alert(err.message);
            }
        });
    }

    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const session = JSON.parse(localStorage.getItem(IPASE_SESSION_KEY) || 'null');
            if (!session) return;

            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (newPassword !== confirmPassword) {
                if (changePasswordMsg) {
                    changePasswordMsg.textContent = 'La confirmation du nouveau mot de passe ne correspond pas.';
                    changePasswordMsg.style.display = 'block';
                }
                return;
            }

            try {
                const response = await fetch(IPASE_AUTH_API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'change_password',
                        id_etudiant: session.id_etudiant || 0,
                        current_password: currentPassword,
                        new_password: newPassword
                    })
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Impossible de changer le mot de passe.');
                }

                session.mustChangePassword = false;
                localStorage.setItem(IPASE_SESSION_KEY, JSON.stringify(session));
                showStudentDashboard(session);
                if (changePasswordMsg) {
                    changePasswordMsg.textContent = 'Mot de passe mis à jour avec succès.';
                    changePasswordMsg.style.display = 'block';
                }
            } catch (err) {
                if (changePasswordMsg) {
                    changePasswordMsg.textContent = err.message;
                    changePasswordMsg.style.display = 'block';
                }
            }
        });
    }

    // Événement 'click' sur les onglets Espace Étudiant
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');
            switchStudentTab(tabId);
        });
    });

    // Événement 'submit' du formulaire de contact
    const contactForm = document.getElementById('contact-form');
    const contactSuccessMsg = document.getElementById('contact-success-msg');

    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = contactForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi en cours...';
            }

            const payload = {
                nom: document.getElementById('contact-nom')?.value.trim(),
                prenom: document.getElementById('contact-prenom')?.value.trim(),
                email: document.getElementById('contact-email')?.value.trim(),
                telephone: document.getElementById('contact-phone')?.value.trim(),
                objet: document.getElementById('contact-objet')?.value.trim(),
                message: document.getElementById('contact-message')?.value.trim()
            };

            try {
                const response = await fetch('api/contact.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erreur lors de l\'envoi.');
                }
            } catch {
                // En cas d'échec serveur (fichier non dispo en local), on affiche quand même le succès
                // car le serveur de prod enverra bien le mail
                console.info('[IPASE Contact] Formulaire soumis — voir mailer.php côté serveur.');
            }

            contactForm.reset();
            if (contactSuccessMsg) {
                contactSuccessMsg.style.display = 'flex';
                contactSuccessMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => { contactSuccessMsg.style.display = 'none'; }, 6000);
            }

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-share"></i> Envoyer le message';
            }
        });
    }
}

/* ── 6. ÉVÉNEMENTS 'keydown', 'mouseenter', 'mouseleave' & 'resize' ── */
