<!-- ===== page-contact.php — Section 7/7 : Contact ===== -->
            <!-- 📄 PAGE 7 : NOUS CONTACTER -->
            <section id="page-contact" class="page-section">
                <div class="page-banner">
                    <div class="banner-overlay">
                        <h1>Contactez-nous</h1>
                        <p>Une question ? Besoin d'orientation ? Notre équipe est à votre disposition.</p>
                    </div>
                </div>

                <div class="section-container">
                    <!-- Quick Direct Action Buttons -->
                    <div class="contact-quick-buttons">
                        <a href="https://wa.me/22893882352" target="_blank" rel="noopener noreferrer" class="contact-quick-btn whatsapp">
                            <i class="fa-brands fa-whatsapp"></i>
                            <div>
                                <span>WhatsApp Direct</span>
                                <strong>+228 93 88 23 52</strong>
                            </div>
                        </a>

                        <a href="tel:+22871329092" class="contact-quick-btn call">
                            <i class="fa-solid fa-phone"></i>
                            <div>
                                <span>Secrétariat Général</span>
                                <strong>+228 71 32 90 92</strong>
                            </div>
                        </a>

                        <a href="mailto:ipase.tg@gmail.com" class="contact-quick-btn email">
                            <i class="fa-solid fa-envelope"></i>
                            <div>
                                <span>Email Officiel</span>
                                <strong>ipase.tg@gmail.com</strong>
                            </div>
                        </a>
                    </div>

                    <div class="contact-layout">
                        <!-- Formulaire de contact -->
                        <div class="contact-form-box">
                            <h2><i class="fa-solid fa-paper-plane"></i> Envoyez-nous un message</h2>
                            <form id="contact-form" class="styled-form">
                                <div id="contact-success-msg" class="alert-success" style="display:none;">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Message envoyé avec succès ! Nous vous répondrons sous 24h.</span>
                                </div>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="contact-nom">Nom *</label>
                                        <input type="text" id="contact-nom" name="Nom" required
                                            placeholder="Votre nom" />
                                    </div>
                                    <div class="form-group">
                                        <label for="contact-prenom">Prénom(s) *</label>
                                        <input type="text" id="contact-prenom" name="Prenom" required
                                            placeholder="Votre prénom" />
                                    </div>
                                    <div class="form-group">
                                        <label for="contact-email">Email *</label>
                                        <input type="email" id="contact-email" name="Email" required
                                            placeholder="votre.email@gmail.com" />
                                    </div>
                                    <div class="form-group">
                                        <label for="contact-phone">Téléphone *</label>
                                        <input type="tel" id="contact-phone" name="Telephone" required
                                            placeholder="+228..." />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contact-objet">Objet *</label>
                                    <input type="text" id="contact-objet" name="Objet" required
                                        placeholder="Demande de renseignements..." />
                                </div>
                                <div class="form-group">
                                    <label for="contact-message">Message *</label>
                                    <textarea id="contact-message" name="Message" rows="5" required
                                        placeholder="Écrivez votre message ici..."></textarea>
                                </div>
                                <button type="submit" class="btn-submit-form">
                                    <i class="fa-solid fa-share"></i> Envoyer le message
                                </button>
                            </form>
                        </div>

                        <!-- Info & Géolocalisation -->
                        <div class="contact-info-card">
                            <h2><i class="fa-solid fa-location-dot"></i> Nos Coordonnées</h2>

                            <div class="info-item">
                                <i class="fa-solid fa-map-pin"></i>
                                <div>
                                    <strong>Siège Social :</strong>
                                    <p>Lomé Agbalepedo, au bord des pavés menant à LK, immeuble à étage face Église
                                        Pentecôte.</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fa-solid fa-envelope"></i>
                                <div>
                                    <strong>Email :</strong>
                                    <p><a href="mailto:ipase.tg@gmail.com">ipase.tg@gmail.com</a></p>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fa-solid fa-phone"></i>
                                <div>
                                    <strong>Secrétariat :</strong>
                                    <p><a href="tel:+22871329092">+228 71 32 90 92</a></p>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fa-solid fa-user-tie"></i>
                                <div>
                                    <strong>Direction Générale :</strong>
                                    <p>(+228) 93 88 23 52 / 98 08 42 75</p>
                                </div>
                            </div>

                            <div class="geo-link-box">
                                <a href="https://share.google/gCLbNnKX1tv4gN2dA" target="_blank" class="btn-geo">
                                    <i class="fa-solid fa-map-location-dot"></i> Voir sur Google Maps (Géolocalisation)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
