// ===== app-init.js — Initialisation au chargement : scroll, navigation, recherche, modales =====
document.addEventListener('DOMContentLoaded', () => {
    console.log("⚡ [IPASE JS Engine] Initialisation des événements interactifs (Style UNIGE)...");

    initScrollEvents();
    initNavigationEvents();
    initSearchAndFilterEvents();
    initModalEvents();
    initFormEvents();
    initHoverAndAccessibilityEvents();
    initScrollAnimations();
    applyStudentSessionState();
});

/* ── 1. ÉVÉNEMENT 'scroll' : Progression, Header Compact & Back to Top ── */
function initScrollEvents() {
    const progressBar = document.getElementById('scroll-progress-bar');
    const header = document.querySelector('.header');
    const backToTopBtn = document.getElementById('back-to-top');

    window.addEventListener('scroll', () => {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;

        // A. Mise à jour dynamique de la barre de progression en haut
        if (progressBar) {
            progressBar.style.width = scrollPercent + '%';
        }

        // B. Header compact au défilement (Style UNIGE)
        if (header) {
            if (scrollTop > 80) {
                header.classList.add('scrolled-compact');
            } else {
                header.classList.remove('scrolled-compact');
            }
        }

        // C. Affichage / Masquage du bouton "Retour en haut"
        if (backToTopBtn) {
            if (scrollTop > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        }
    });

    // Événement 'click' sur le bouton Retour en haut
    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

/* ── 2. ÉVÉNEMENT 'click' : Navigation SPA & Menu Mobile ── */
function initNavigationEvents() {
    const nav = document.querySelector('.nav');
    const mobileToggle = document.getElementById('mobile-menu-toggle');

    // Événement 'click' global pour les déclencheurs de page
    document.addEventListener('click', (e) => {
        const pageTrigger = e.target.closest('[data-page-target]');
        if (pageTrigger) {
            e.preventDefault();
            const pageId = pageTrigger.getAttribute('data-page-target');
            const anchor = pageTrigger.getAttribute('data-anchor');
            switchPage(pageId, anchor);

            if (nav) nav.classList.remove('open');
        }
    });

    // Événement 'click' pour le menu mobile hamburger
    if (mobileToggle && nav) {
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            nav.classList.toggle('open');
        });
    }
}

/* ── 3. ÉVÉNEMENTS 'input' & 'keyup' : Recherche en temps réel avec Surbrillance ── */
function initSearchAndFilterEvents() {
    const searchInput = document.getElementById('search-filiere');
    const filterButtons = document.querySelectorAll('.domain-filter-btn');
    const domainSections = document.querySelectorAll('.domain-section');

    function performSearch() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const activeBtn = document.querySelector('.domain-filter-btn.active');
        const selectedDomain = activeBtn ? activeBtn.getAttribute('data-domain') : 'all';

        domainSections.forEach(section => {
            const sectionDomain = section.getAttribute('data-domain');
            let hasVisibleCards = false;

            const cards = section.querySelectorAll('.filiere-card');
            cards.forEach(card => {
                const titleElem = card.querySelector('h4');
                const titleText = titleElem ? titleElem.textContent : '';
                const descElem = card.querySelector('p');
                const descText = descElem ? descElem.textContent : '';

                const matchesQuery = !query || titleText.toLowerCase().includes(query) || descText.toLowerCase().includes(query);
                const matchesDomain = (selectedDomain === 'all') || (sectionDomain === selectedDomain);

                if (matchesQuery && matchesDomain) {
                    card.style.display = 'flex';
                    hasVisibleCards = true;

                    // Surbrillance en temps réel des mots recherchés (Style UNIGE)
                    if (query.length >= 2 && titleElem) {
                        const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
                        titleElem.innerHTML = titleText.replace(regex, '<mark class="search-highlight">$1</mark>');
                    } else if (titleElem) {
                        titleElem.innerHTML = titleText;
                    }
                } else {
                    card.style.display = 'none';
                }
            });

            section.style.display = hasVisibleCards ? 'block' : 'none';
        });
    }

    if (searchInput) {
        // Événement 'input' pour réaction immédiate à la frappe
        searchInput.addEventListener('input', performSearch);
        // Événement 'keyup' pour détection de la touche Effacer
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                performSearch();
            }
        });
    }

    // Événement 'click' sur les filtres par domaine
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            performSearch();
        });
    });
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/* ── 4. ÉVÉNEMENT 'click' : Modale d'Aperçu Rapide Interactive (Quick View) ── */
function initModalEvents() {
    const modal = document.getElementById('quick-modal');
    const modalBody = document.getElementById('modal-content-body');
    const closeBtn = document.getElementById('modal-close-btn');

    function openModal(htmlContent) {
        if (modal && modalBody) {
            modalBody.innerHTML = htmlContent;
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal() {
        if (modal) {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    // Événement 'click' pour ouvrir la modale au clic sur les cartes de filières
    document.addEventListener('click', (e) => {
        const filiereCard = e.target.closest('.filiere-card');
        const pdfBtn = e.target.closest('.btn-pdf-download');

        // Si l'utilisateur a cliqué sur la carte mais PAS directement sur le bouton de téléchargement PDF
        if (filiereCard && !pdfBtn) {
            const title = filiereCard.querySelector('h4') ? filiereCard.querySelector('h4').textContent : 'Filière IPASE';
            const desc = filiereCard.querySelector('p') ? filiereCard.querySelector('p').textContent : '';
            const imgSrc = filiereCard.querySelector('img') ? filiereCard.querySelector('img').src : '';
            const pdfLink = filiereCard.querySelector('.btn-pdf-download') ? filiereCard.querySelector('.btn-pdf-download').href : '#';

            const modalHtml = `
                <div class="modal-detail-card">
                    <img src="${imgSrc}" alt="${title}" class="modal-detail-img" />
                    <div class="modal-detail-info">
                        <span class="badge-tag"><i class="fa-solid fa-graduation-cap"></i> Formation Certifiée IPASE</span>
                        <h2>${title}</h2>
                        <p class="modal-desc">${desc}</p>
                        <div class="modal-features">
                            <div><i class="fa-solid fa-award"></i> Diplôme Agréé par l'État</div>
                            <div><i class="fa-solid fa-briefcase"></i> Stages Pratiques Rémunérés</div>
                            <div><i class="fa-solid fa-user-check"></i> Insertion Professionnelle</div>
                        </div>
                        <div class="modal-actions">
                            <a href="${pdfLink}" target="_blank" download class="btn-pdf-download">
                                <i class="fa-solid fa-file-pdf"></i> Télécharger la Fiche Complète (PDF)
                            </a>
                            <a href="#" data-page-target="page-etudiant" class="btn-hero-secondary btn-close-open-reg">
                                <i class="fa-solid fa-user-plus"></i> S'inscrire à cette filière
                            </a>
                        </div>
                    </div>
                </div>
            `;
            openModal(modalHtml);
        }
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    // Événement 'click' sur le fond noir de la modale pour la fermer
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    }

    // Raccourci d'inscription depuis la modale
    document.addEventListener('click', (e) => {
        if (e.target.closest('.btn-close-open-reg')) {
            closeModal();
        }
    });
}

/* ── 5. ÉVÉNEMENTS 'change' & 'submit' : Formulaires & Validation Interactive ── */
