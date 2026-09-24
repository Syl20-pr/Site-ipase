<!-- ===== footer.php — Fermeture du <main>, boutons flottants, pied de page, modale, scripts ===== -->
        </main>

        <!-- ═══ BOUTONS ACTION FLOTTANTS FIXES ═══ -->
        <!-- Bouton S'inscrire Flottant (Gauche) -->
        <a href="#" data-page-target="page-etudiant" class="floating-btn float-left" id="btn-float-inscription"
            title="S'inscrire en ligne">
            <i class="fa-solid fa-user-plus"></i>
            <span class="float-text">S'inscrire</span>
        </a>

        <!-- Bouton WhatsApp Flottant (Droite) -->
        <a href="https://wa.me/22893882352" target="_blank" rel="noopener noreferrer" class="floating-btn float-right"
            title="Discuter sur WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
            <span class="float-text">WhatsApp</span>
        </a>

        <!-- ═══ PIED DE PAGE GLOBAL (FOOTER) ═══ -->
        <footer class="footer">
            <div class="footer-container">
                <!-- Col 1 : À propos IPASE -->
                <div class="footer-col">
                    <img src="assets/img/logo.jpeg" alt="Logo IPASE" class="footer-logo" />
                    <p class="footer-about">
                        Institut Professionnel ACTION SANTÉ ÉDUCATION (IPASE) — Établissement de formation
                        professionnelle agréé par l'État Togolais (Arrêté N°2025/058/METFPA/CAB/SE-CCCS).
                    </p>
                </div>

                <!-- Col 2 : Liens Rapides -->
                <div class="footer-col footer-links">
                    <h4>Liens Rapides</h4>
                    <ul>
                        <li><a href="#" data-page-target="page-accueil"><i class="fa-solid fa-chevron-right"></i>
                                Accueil</a></li>
                        <li><a href="#" data-page-target="page-apropos"><i class="fa-solid fa-chevron-right"></i> À
                                propos de nous</a></li>
                        <li><a href="#" data-page-target="page-filieres"><i class="fa-solid fa-chevron-right"></i> Nos
                                filières de formation</a></li>
                        <li><a href="#" data-page-target="page-etudiant"><i class="fa-solid fa-chevron-right"></i>
                                Espace étudiant</a></li>
                        <li><a href="#" data-page-target="page-vie-etudiante"><i class="fa-solid fa-chevron-right"></i>
                                Vie étudiante</a></li>
                        <li><a href="#" data-page-target="page-actualites"><i class="fa-solid fa-chevron-right"></i>
                                Actualités</a></li>
                        <li><a href="#" data-page-target="page-contact"><i class="fa-solid fa-chevron-right"></i> Nous
                                contacter</a></li>
                        <li><a href="admin/index.html" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-shield-halved"></i> Administration</a></li>
                    </ul>
                </div>

                <!-- Col 3 : Coordonnées -->
                <div class="footer-col">
                    <h4>Coordonnées</h4>
                    <p><i class="fa-solid fa-location-dot"></i> Lomé Agbalepedo, au bord des pavés menant à LK, face
                        Église Pentecôte.</p>
                    <p><i class="fa-solid fa-envelope"></i> ipase.tg@gmail.com</p>
                    <p><i class="fa-solid fa-phone"></i> Secrétariat : +228 71329092</p>
                    <p><i class="fa-solid fa-user-tie"></i> Direction : +228 93882352 / 98084275</p>
                </div>

                <!-- Col 4 : Réseaux Sociaux & Newsletter -->
                <div class="footer-col">
                    <h4>Suivez-nous</h4>
                    <div class="social-links">
                        <a href="https://www.facebook.com/share/17wRwQJS3i/" target="_blank" rel="noopener noreferrer" title="Facebook"><i
                                class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://tiktok.com/@ipase228" target="_blank" rel="noopener noreferrer" title="TikTok"><i
                                class="fa-brands fa-tiktok"></i></a>
                        <a href="https://whatsapp.com/channel/0029Vb7YFWxFSAt7zBoPPy37" target="_blank" rel="noopener noreferrer"
                            title="WhatsApp Channel"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="#" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>

                    <h4 class="newsletter-title">Newsletter</h4>
                    <form class="footer-newsletter"
                        onsubmit="event.preventDefault(); alert('Merci pour votre abonnement !');">
                        <input type="email" placeholder="Votre e-mail..." required />
                        <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 IPASE - Institut Professionnel Action Santé Éducation. Tous droits réservés.</p>
            </div>
        </footer>

        <!-- ═══ BOUTON RETOUR EN HAUT ═══ -->
        <button id="back-to-top" class="back-to-top-btn" title="Retour en haut de page"
            aria-label="Retour en haut de page">
            <i class="fa-solid fa-chevron-up"></i>
        </button>

        <!-- ═══ MODALE INTERACTIVE RAPIDE (UNIGE Style) ═══ -->
        <div id="quick-modal" class="modal-overlay" aria-hidden="true" role="dialog">
            <div class="modal-card">
                <button class="modal-close" id="modal-close-btn" aria-label="Fermer">&times;</button>
                <div id="modal-content-body" class="modal-body-content"></div>
            </div>
        </div>

    </div>

    <!-- jsPDF pour le récapitulatif d'inscription -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- SheetJS pour la liste Excel des inscriptions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- Script JavaScript -->
    <!-- Scripts IPASE — découpés en modules pour plus de lisibilité -->
    <!-- L'ordre est important : chaque fichier dépend des constantes/fonctions des précédents -->
    <script src="assets/js/modules/config.js"></script>
    <script src="assets/js/modules/utils.js"></script>
    <script src="assets/js/modules/excel-export.js"></script>
    <script src="assets/js/modules/admin-embedded.js"></script>
    <script src="assets/js/modules/student-dashboard.js"></script>
    <script src="assets/js/modules/pdf-generator.js"></script>
    <script src="assets/js/modules/app-init.js"></script>
    <script src="assets/js/modules/forms.js"></script>
    <script src="assets/js/modules/ui-effects.js"></script>
</body>


</html>