<!-- ===== page-etudiant.php — Section 4/7 : Espace étudiant (inscription, connexion, tableau de bord) ===== -->
            <!-- 📄 PAGE 4 : ESPACE ÉTUDIANT -->
            <section id="page-etudiant" class="page-section">
                <div class="page-banner">
                    <div class="banner-overlay">
                        <h1>Espace Étudiant & Inscription</h1>
                        <p>Connectez-vous à votre portail étudiant ou soumettez votre candidature en ligne.</p>
                    </div>
                </div>

                <div class="section-container">
                    <!-- Onglets Connexion / Inscription -->
                    <div class="student-tabs-header">
                        <button class="tab-btn active" data-tab="tab-inscription">
                            <i class="fa-solid fa-pen-to-square"></i> INSCRIPTION EN LIGNE
                        </button>
                        <button class="tab-btn" data-tab="tab-connexion">
                            <i class="fa-solid fa-right-to-bracket"></i> CONNEXION ÉTUDIANT
                        </button>
                    </div>

                    <!-- TAB 1 : INSCRIPTION EN LIGNE -->
                    <div id="tab-inscription" class="tab-content active">
                        <div class="form-container-card">
                            <div class="form-intro">
                                <h3>Formulaire d'Inscription 2026-2027</h3>
                                <p>Veuillez remplir soigneusement le formulaire ci-dessous. Notre secrétariat examinera
                                    votre dossier sous 24h.</p>
                            </div>

                            <div id="reg-success-msg" class="alert-success" style="display: none;">
                                <i class="fa-solid fa-circle-check"></i>
                                <span id="reg-success-text"></span>
                            </div>

                            <form id="inscription-form" class="styled-form">
                                <!-- Group 1: Informations personnelles -->
                                <div class="form-section-title">
                                    <i class="fa-solid fa-user"></i> 1. Informations Personnelles
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="reg-nom">Nom de famille *</label>
                                        <input type="text" id="reg-nom" required placeholder="Ex: KOFFI" />
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-prenom">Prénom(s) *</label>
                                        <input type="text" id="reg-prenom" required placeholder="Ex: Yawovi Marc" />
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-sexe">Sexe *</label>
                                        <select id="reg-sexe" required>
                                            <option value="">-- Sélectionnez --</option>
                                            <option value="M">Masculin</option>
                                            <option value="F">Féminin</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-dob">Date de naissance *</label>
                                        <input type="date" id="reg-dob" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-pob">Lieu de naissance *</label>
                                        <input type="text" id="reg-pob" required placeholder="Ex: Lomé" />
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-nationality">Nationalité *</label>
                                        <input type="text" id="reg-nationality" required placeholder="Ex: Togolaise" />
                                    </div>

                                    <div class="form-group full-width">
                                        <label for="reg-file">Pièce d'identité ou Extrait de Naissance (Fichier à
                                            joindre) *</label>
                                        <input type="file" id="reg-file" accept=".pdf,.png,.jpg,.jpeg" required />
                                        <small>Formats acceptés : PDF, JPG, PNG (Max: 5Mo)</small>
                                    </div>
                                </div>

                                <!-- Group 2: Coordonnées -->
                                <div class="form-section-title">
                                    <i class="fa-solid fa-address-book"></i> 2. Coordonnées & Niveau d'études
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="reg-phone">Téléphone (WhatsApp) *</label>
                                        <input type="tel" id="reg-phone" required placeholder="Ex: +228 90 00 00 00" />
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-email">Adresse Email *</label>
                                        <input type="email" id="reg-email" required placeholder="exemple@gmail.com" />
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-address">Adresse de résidence (Ville & Quartier) *</label>
                                        <input type="text" id="reg-address" required
                                            placeholder="Ex: Lomé, Agbalepedo" />
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-country">Pays de résidence *</label>
                                        <input type="text" id="reg-country" required value="Togo" />
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-level">Niveau d'études actuel *</label>
                                        <select id="reg-level" required>
                                            <option value="">-- Sélectionnez votre niveau --</option>
                                            <option value="BEPC">BEPC</option>
                                            <option value="BAC1">BAC 1 (Première)</option>
                                            <option value="BAC2">BAC 2 (Terminale / BACCALAURÉAT)</option>
                                            <option value="LICENCE">Licence universitaire</option>
                                            <option value="AUTRE">Autre niveau / Professionnel</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-parent-phone">Téléphone (Tuteur / Parent) *</label>
                                        <input type="tel" id="reg-parent-phone" required
                                            placeholder="Ex: +228 98 00 00 00" />
                                    </div>
                                </div>

                                <!-- Group 3: Choix de la formation -->
                                <div class="form-section-title">
                                    <i class="fa-solid fa-graduation-cap"></i> 3. Choix de la Formation
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="reg-domain">Choisissez votre domaine *</label>
                                        <select id="reg-domain" required>
                                            <option value="">-- Choisissez un domaine --</option>
                                            <option value="sante">Santé et Social</option>
                                            <option value="gestion">Gestion et Administration</option>
                                            <option value="transport">Transport Logistique et Commerce</option>
                                            <option value="informatique">Informatique, Numérique et Technologie</option>
                                            <option value="technique">Technique et Industriel</option>
                                            <option value="specialise">Autres Filières Spécialisées</option>
                                            <option value="bt">Brevet de Technicien (BT)</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="reg-filiere">Choisissez votre filière *</label>
                                        <select id="reg-filiere" required>
                                            <option value="">-- Sélectionnez d'abord un domaine --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-submit-wrapper">
                                    <button type="submit" class="btn-submit-form">
                                        <i class="fa-solid fa-paper-plane"></i> Soumettre ma candidature
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- TAB 2 : CONNEXION ÉTUDIANT -->
                    <div id="tab-connexion" class="tab-content">
                        <!-- Login Box -->
                        <div id="login-box" class="form-container-card small-card">
                            <div class="form-intro">
                                <h3>Connexion Portail Étudiant</h3>
                                <p>Accédez à votre espace d'apprentissage en ligne IPASE.</p>
                            </div>

                            <div id="login-error-msg" class="alert-error" style="display: none;">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span id="login-error-text"></span>
                            </div>

                            <form id="login-form" class="styled-form">
                                <div class="form-group">
                                    <label for="login-email">Adresse e-mail ou matricule</label>
                                    <input type="text" id="login-email" required placeholder="etudiant@ipase.tg ou IPASE-2026-0001" />
                                </div>

                                <div class="form-group">
                                    <label for="login-password">Mot de passe</label>
                                    <input type="password" id="login-password" required placeholder="••••••••" />
                                </div>

                                <div class="form-options">
                                    <a href="#" class="forgot-pass">Mot de passe oublié ?</a>
                                </div>

                                <button type="submit" class="btn-submit-form full">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Se connecter
                                </button>
                            </form>
                        </div>

                        <!-- Tableau de bord étudiant -->
                        <div id="student-dashboard" class="student-dashboard-shell" style="display: none;">
                            <div class="dashboard-header">
                                <h3 id="student-welcome-title"><i class="fa-solid fa-user-graduate"></i> Bienvenue sur votre Tableau de Bord Étudiant IPASE</h3>
                                <p>Année Académique 2026-2027 | Statut : <strong>Actif</strong></p>
                            </div>

                            <div id="password-change-panel" class="alert-success student-alert" style="display: none;">
                                <strong>Première connexion :</strong> vous devez obligatoirement modifier votre mot de passe pour accéder pleinement à votre espace.
                                <form id="change-password-form" class="styled-form student-password-form">
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="current-password">Mot de passe actuel</label>
                                            <input type="password" id="current-password" required placeholder="Mot de passe actuel" />
                                        </div>
                                        <div class="form-group">
                                            <label for="new-password">Nouveau mot de passe</label>
                                            <input type="password" id="new-password" required placeholder="Minimum 8 caractères" />
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm-password">Confirmer le nouveau mot de passe</label>
                                            <input type="password" id="confirm-password" required placeholder="Confirmer" />
                                        </div>
                                    </div>
                                    <div id="change-password-msg" class="alert-error student-alert" style="display: none;"></div>
                                    <button type="submit" class="btn-submit-form full student-submit-btn">
                                        <i class="fa-solid fa-lock"></i> Mettre à jour mon mot de passe
                                    </button>
                                </form>
                            </div>

                            <div class="student-dashboard-layout">
                                <aside class="student-sidebar">
                                    <div class="student-profile-card">
                                        <div class="student-avatar"><i class="fa-solid fa-user-graduate"></i></div>
                                        <h4 id="student-sidebar-name">Étudiant IPASE</h4>
                                        <p id="student-sidebar-email">etudiant@ipase.tg</p>
                                    </div>
                                    <nav class="student-nav">
                                        <button class="student-nav-link active" data-view="home"><i class="fa-solid fa-house"></i> Accueil</button>
                                        <button class="student-nav-link" data-view="profile"><i class="fa-solid fa-id-card"></i> Mon profil</button>
                                        <button class="student-nav-link" data-view="courses"><i class="fa-solid fa-book-open"></i> Mes cours</button>
                                        <button class="student-nav-link" data-view="notes"><i class="fa-solid fa-square-poll-vertical"></i> Mes notes</button>
                                        <button class="student-nav-link" data-view="documents"><i class="fa-solid fa-folder-open"></i> Documents</button>
                                        <button class="student-nav-link" data-view="payments"><i class="fa-solid fa-wallet"></i> Paiements</button>
                                        <button class="student-nav-link" data-view="messages"><i class="fa-solid fa-message"></i> Messagerie</button>
                                    </nav>
                                </aside>

                                <section class="student-main-panel">
                                    <div class="student-panel-section active" data-panel="home">
                                        <div class="dashboard-grid">
                                            <div class="dash-card"><i class="fa-solid fa-bell"></i><h4>Notifications</h4><p>3 nouveaux messages et 2 annonces</p></div>
                                            <div class="dash-card"><i class="fa-solid fa-calendar-days"></i><h4>Emploi du temps</h4><p>Votre planning de cette semaine</p></div>
                                            <div class="dash-card"><i class="fa-solid fa-book-open"></i><h4>Mes cours</h4><p>Supports, vidéos et travaux pratiques</p></div>
                                            <div class="dash-card"><i class="fa-solid fa-square-poll-vertical"></i><h4>Mes notes</h4><p>Contrôle continu et examens</p></div>
                                        </div>
                                    </div>

                                    <div class="student-panel-section" data-panel="profile">
                                        <h4>Profil étudiant</h4>
                                        <div class="student-card-list">
                                            <div class="student-info-card"><strong>Nom :</strong> <span id="student-profile-name">—</span></div>
                                            <div class="student-info-card"><strong>Matricule :</strong> <span id="student-profile-matricule">—</span></div>
                                            <div class="student-info-card"><strong>Email :</strong> <span id="student-profile-email">—</span></div>
                                            <div class="student-info-card"><strong>Filière :</strong> <span id="student-profile-filiere">—</span></div>
                                            <div class="student-info-card"><strong>Niveau :</strong> <span id="student-profile-niveau">—</span></div>
                                        </div>
                                    </div>

                                    <div class="student-panel-section" data-panel="courses">
                                        <h4>Mes cours</h4>
                                        <div class="student-card-list" id="student-courses-list">
                                            <div class="student-info-card"><em>Chargement des cours...</em></div>
                                        </div>
                                    </div>

                                    <div class="student-panel-section" data-panel="notes">
                                        <h4>Mes notes</h4>
                                        <div class="student-card-list" id="student-notes-list">
                                            <div class="student-info-card"><em>Chargement des notes...</em></div>
                                        </div>
                                    </div>

                                    <div class="student-panel-section" data-panel="documents">
                                        <h4>Documents administratifs</h4>
                                        <div class="student-card-list">
                                            <div class="student-info-card"><strong>Attestation de scolarité</strong><br><a href="#">Télécharger</a></div>
                                            <div class="student-info-card"><strong>Certificat d'inscription</strong><br><a href="#">Télécharger</a></div>
                                            <div class="student-info-card"><strong>Reçu de paiement</strong><br><a href="#">Télécharger</a></div>
                                        </div>
                                    </div>

                                    <div class="student-panel-section" data-panel="payments">
                                        <h4>Paiements</h4>
                                        <div class="student-card-list">
                                            <div class="student-info-card"><strong>Montant payé :</strong> 180 000 FCFA</div>
                                            <div class="student-info-card"><strong>Montant restant :</strong> 70 000 FCFA</div>
                                            <div class="student-info-card"><strong>Dernier reçu :</strong> Disponible en PDF</div>
                                        </div>
                                    </div>

                                    <div class="student-panel-section" data-panel="messages">
                                        <h4>Messagerie</h4>
                                        <div class="student-card-list">
                                            <div class="student-info-card"><strong>Enseignant :</strong> Nouveau commentaire sur votre devoir</div>
                                            <div class="student-info-card"><strong>Administration :</strong> Votre attestation est prête</div>
                                            <div class="student-info-card"><strong>Groupe :</strong> Discussion de promotion ouverte</div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
